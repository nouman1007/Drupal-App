import azure.functions as func
import logging
import traceback
import os
import json
import io
import re
from datetime import datetime
from typing import List, Optional, Tuple, Dict
from azure.core.exceptions import AzureError
from azure.storage.blob import BlobServiceClient
from azure.core.credentials import AzureKeyCredential
from azure.search.documents import SearchClient
from azure.core.credentials_async import AsyncTokenCredential
from openai import AsyncAzureOpenAI
from tenacity import retry, stop_after_attempt, wait_exponential
from PyPDF2 import PdfReader

logger = logging.getLogger("BlobIndexTrigger")

class File:
    def __init__(self, filename: str, content: Optional[bytes] = None, content_type: str = None):
        self.name = filename
        self._content = content
        self.content_type = content_type
        self.acls = {}

    def filename(self) -> str:
        return self.name

    def filename_to_id(self) -> str:
        return self.name.replace('/', '_').replace('.', '_')

    async def get_content(self) -> bytes:
        return self._content

class SplitPage:
    def __init__(self, text: str, page_num: int = 0):
        self.text = text
        self.page_num = page_num

class TextSplitter:
    def __init__(self, max_tokens: int = 2000):  # Conservative token limit
        self.max_tokens = max_tokens
        
    async def extract_text(self, file: File) -> str:
        content = await file.get_content()
        try:
            # Handle PDF files
            if file.content_type.lower() == 'application/pdf' or file.name.lower().endswith('.pdf'):
                pdf_file = io.BytesIO(content)
                pdf_reader = PdfReader(pdf_file)
                
                # Extract text from each page
                text = ""
                for page in pdf_reader.pages:
                    text += page.extract_text() + "\n\n"
                
                return text.strip()
            else:
                # Handle text files
                try:
                    text = content.decode('utf-8')
                    return text.strip()
                except UnicodeDecodeError:
                    text = content.decode('latin-1')
                    return text.strip()
                
        except Exception as e:
            logger.error(f"Error extracting text: {str(e)}")
            raise

    def split_text(self, text: str) -> List[SplitPage]:
        # Clean the text
        text = re.sub(r'\s+', ' ', text).strip()
        
        # Split into sentences (roughly)
        sentences = re.split(r'(?<=[.!?])\s+', text)
        
        sections = []
        current_section = []
        current_length = 0
        section_number = 0
        
        for sentence in sentences:
            # Estimate tokens (very conservatively)
            sentence_length = len(sentence.split())
            
            # If adding this sentence would exceed the limit
            if current_length + sentence_length > self.max_tokens:
                if current_section:
                    # Create a section from accumulated sentences
                    section_text = ' '.join(current_section)
                    sections.append(SplitPage(
                        text=section_text,
                        page_num=section_number
                    ))
                    section_number += 1
                    logger.info(f"Created section {section_number} with ~{current_length} tokens")
                    
                    # Reset accumulator
                    current_section = []
                    current_length = 0
            
            # Add the sentence to current section
            current_section.append(sentence)
            current_length += sentence_length
        
        # Don't forget the last section
        if current_section:
            section_text = ' '.join(current_section)
            sections.append(SplitPage(
                text=section_text,
                page_num=section_number
            ))
            logger.info(f"Created final section {section_number + 1} with ~{current_length} tokens")
        
        logger.info(f"Split text into {len(sections)} sections")
        return sections

class Section:
    def __init__(self, split_page: SplitPage, content: File, category: Optional[str] = None,
                 title: Optional[str] = None, urls: Optional[List[str]] = None):
        self.split_page = split_page
        self.content = content
        self.category = category
        self.title = title or ""
        self.urls = urls or []

class OpenAIEmbeddings:
    def __init__(self, endpoint: str, deployment: str, api_key: str):
        self.endpoint = endpoint.rstrip('/')
        self.deployment = deployment
        self.api_key = api_key
        self.model = "text-embedding-ada-002"
        self.dimensions = 1536
        self.max_tokens = 8000

    @retry(stop=stop_after_attempt(3), wait=wait_exponential(multiplier=1, min=4, max=10))
    async def create_embeddings(self, texts: List[str]) -> List[List[float]]:
        client = AsyncAzureOpenAI(
            api_key=self.api_key,
            azure_endpoint=self.endpoint,
            api_version="2023-05-15"
        )

        embeddings = []
        
        for i, text in enumerate(texts):
            try:
                # Count tokens more accurately
                token_count = len(text.split())
                if token_count > self.max_tokens:
                    logger.warning(f"Text {i+1} too long ({token_count} tokens). Truncating...")
                    # Truncate by words to be safe
                    words = text.split()[:self.max_tokens]
                    text = ' '.join(words)
                
                response = await client.embeddings.create(
                    input=text,
                    model=self.model
                )
                embeddings.append(response.data[0].embedding)
                logger.info(f"Created embedding {i+1} of {len(texts)} (size: {len(response.data[0].embedding)})")
            except Exception as e:
                logger.error(f"Error creating embedding {i+1}: {str(e)}")
                raise

        return embeddings

class SearchInfo:
    def __init__(self, endpoint: str, credential: str, index_name: str):
        self.endpoint = endpoint
        self.credential = AzureKeyCredential(credential)
        self.index_name = index_name

    def create_search_client(self):
        return SearchClient(
            endpoint=self.endpoint,
            index_name=self.index_name,
            credential=self.credential
        )

def sourcepage_from_file_page(filename: str, page: int = 0) -> str:
    """Generate sourcepage string for a given file and page number"""
    if os.path.splitext(filename)[1].lower() == ".pdf":
        return f"{os.path.basename(filename)}#page={page+1}"
    else:
        return os.path.basename(filename)

async def get_blob_content(blob_url: str) -> Tuple[bytes, str, str]:
    """Get blob content and metadata from URL"""
    try:
        logger.info(f"Getting blob content from URL: {blob_url}")
        
        url_path = blob_url.replace("https://", "")
        parts = url_path.split('/')
        
        if len(parts) < 2:
            raise ValueError(f"Invalid blob URL format: {blob_url}")
            
        account = parts[0].split('.')[0]
        
        container_name = None
        blob_name = None
        
        for i, part in enumerate(parts):
            if part == "evidencefiles":
                container_name = part
                blob_name = '/'.join(parts[i+1:])
                break
        
        if not container_name or not blob_name:
            raise ValueError(f"Could not find container or blob name in URL: {blob_url}")
            
        logger.info(f"Parsed URL - Account: {account}, Container: {container_name}, Blob: {blob_name}")
        
        # Get blob content
        connection_string = os.environ["AzureWebJobsStorage"]
        blob_service_client = BlobServiceClient.from_connection_string(connection_string)
        container_client = blob_service_client.get_container_client(container_name)
        blob_client = container_client.get_blob_client(blob_name)
        
        # Download content
        try:
            download_stream = blob_client.download_blob()
            content = download_stream.readall()
            content_type = download_stream.properties.content_settings.content_type or 'application/octet-stream'
            
            logger.info(f"Successfully downloaded blob - Size: {len(content)} bytes, Type: {content_type}")
            
            return content, content_type, blob_name
            
        except Exception as download_error:
            logger.error(f"Error downloading blob content: {str(download_error)}")
            raise
        
    except Exception as e:
        logger.error(f"Error getting blob content: {str(e)}")
        logger.error(f"Traceback: {traceback.format_exc()}")
        raise

async def process_file_and_update_index(blob_content: bytes, content_type: str, file_name: str, search_info: SearchInfo) -> None:
    """Process file content and update search index"""
    try:
        logger.info(f"Starting to process file: {file_name}")
        
        if not blob_content or len(blob_content) == 0:
            logger.warning(f"Empty file content for {file_name}")
            return
        
        file = File(
            filename=file_name,
            content=blob_content,
            content_type=content_type
        )

        text_splitter = TextSplitter()
        
        # Extract and split text
        try:
            text = await text_splitter.extract_text(file)
            if not text:
                logger.warning(f"No text content extracted from {file_name}")
                return
                
            split_pages = text_splitter.split_text(text)
            logger.info(f"Successfully split {file_name} into {len(split_pages)} pages")
            
        except Exception as e:
            logger.error(f"Error extracting text from {file_name}: {str(e)}")
            raise

        # Create sections
        sections = [
            Section(
                split_page=split_page,
                content=file,
                category=None,
                title=os.path.basename(file_name),
                urls=[]
            )
            for split_page in split_pages
        ]
        
        logger.info(f"Created {len(sections)} sections")

        # Initialize embeddings service
        embeddings_service = OpenAIEmbeddings(
            endpoint=os.environ["AZURE_OPENAI_ENDPOINT"],
            deployment=os.environ["AZURE_OPENAI_DEPLOYMENT"],
            api_key=os.environ["AZURE_OPENAI_KEY"]
        )

        # Create embeddings
        texts = [section.split_page.text for section in sections]
        try:
            embeddings = await embeddings_service.create_embeddings(texts)
            logger.info(f"Created {len(embeddings)} embeddings")
        except Exception as e:
            logger.error(f"Error creating embeddings: {str(e)}")
            raise

        # Create search client and upload documents
        search_client = search_info.create_search_client()
        
        # Structure documents with embeddings
        documents = [
            {
                "id": f"{section.content.filename_to_id()}-page-{i}",
                "content": section.split_page.text,
                "embedding": embedding,
                "title": section.title,
                "category": section.category,
                "sourcefile": section.content.filename(),
                "sourcepage": sourcepage_from_file_page(
                    filename=section.content.filename(),
                    page=section.split_page.page_num
                ),
                "urls": section.urls,
                "storageUrl": f"https://{os.environ['AZURE_STORAGE_ACCOUNT_NAME']}.blob.core.windows.net/evidencefiles/{file_name}"
            }
            for i, (section, embedding) in enumerate(zip(sections, embeddings))
        ]
        
        # Upload documents
        try:
            result = search_client.upload_documents(documents)
            logger.info(f"Uploaded {len(result)} documents to search index")
        except Exception as upload_error:
            logger.error(f"Error uploading to search index: {str(upload_error)}")
            raise

    except Exception as e:
        logger.error(f"Error processing file {file_name}: {str(e)}")
        logger.error(f"Traceback: {traceback.format_exc()}")
        raise

async def main(event: func.EventGridEvent) -> None:
    """Azure Function triggered when a new blob is created"""
    try:
        event_data = event.get_json()
        logger.info(f'Event Type: {event.event_type}')
        logger.info(f'Subject: {event.subject}')
        logger.info(f'Data: {json.dumps(event_data)}')
        
        if event.event_type != "Microsoft.Storage.BlobCreated":
            logger.info(f"Skipping event type: {event.event_type}")
            return
            
        blob_url = event_data.get('url', '')
        content_length = event_data.get('contentLength', 0)
        event_content_type = event_data.get('contentType', 'application/octet-stream')
        
        if not blob_url:
            logger.warning('No blob URL found in event data')
            return

        if content_length == 0:
            logger.warning(f'Empty file detected, contentLength is 0')
            return

        # Get blob content
        try:
            blob_content, content_type, file_name = await get_blob_content(blob_url)
            content_type = event_content_type or content_type
            
            if not blob_content or len(blob_content) == 0:
                logger.warning(f'Empty content received for file: {file_name}')
                return
                
        except ValueError as ve:
            logger.error(f"Error accessing blob: {str(ve)}")
            return

        logger.info(f"Processing file: {file_name}")
        logger.info(f"File details - Size: {len(blob_content)}, Type: {content_type}")

        # Initialize search configuration
        search_info = SearchInfo(
            endpoint=os.environ["AZURE_SEARCH_SERVICE_ENDPOINT"],
            credential=os.environ["AZURE_SEARCH_ADMIN_KEY"],
            index_name=os.environ["AZURE_SEARCH_INDEX_NAME"]
        )

        # Process file
        await process_file_and_update_index(
            blob_content=blob_content,
            content_type=content_type,
            file_name=file_name,
            search_info=search_info
        )

        logger.info(f"Successfully processed file: {file_name}")

    except Exception as e:
        logger.error(f"Error processing event: {str(e)}")
        logger.error(f"Traceback: {traceback.format_exc()}")
        raise