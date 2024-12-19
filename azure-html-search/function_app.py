import azure.functions as func
import logging
import json
from azure.core.credentials import AzureKeyCredential
from azure.search.documents import SearchClient
from typing import Optional, List, Union, Dict
import os
from dataclasses import dataclass
import re

# Initialize the function app
app = func.FunctionApp(http_auth_level=func.AuthLevel.ANONYMOUS)

@dataclass
class SearchRequest:
    search_text: str
    programs: Optional[Union[List[str], str]] = None
    ages_studied: Optional[Union[List[str], str]] = None
    focus_population: Optional[str] = None
    domain: Optional[str] = None
    subdomain_1: Optional[str] = None  # Added subdomain_1
    subdomain_2: Optional[str] = None  # Added subdomain_2
    subdomain_3: Optional[str] = None  # Added subdomain_3

def get_first_n_lines(text: str, n: int = 3) -> str:
    """Safely get first n lines from text content."""
    if not text:
        return ""
    # Convert to string if not already a string
    text_str = str(text) if text is not None else ""
    lines = text_str.split('\n')
    return '\n'.join(lines[:n]).strip()

def get_search_context(text: str, search_text: str, context_chars: int = 150) -> str:
    """
    Returns context around where the search term was found.
    Args:
        text (str): The full content text to search in
        search_text (str): The search term to look for
        context_chars (int): Number of characters to include before and after the match
    Returns:
        str: The context snippet with the search term and surrounding text
    """
    if not text or not search_text:
        logging.info(f"Empty text or search_text: text='{text}', search_text='{search_text}'")
        return ""
    
    # Convert to string and clean HTML
    text_str = str(text) if text is not None else ""
    # Remove HTML tags using simple regex
    clean_text = re.sub(r'<[^>]+>', ' ', text_str)
    # Remove extra whitespace
    clean_text = ' '.join(clean_text.split())
    
    # Log the cleaned text for debugging
    logging.info(f"Cleaned text length: {len(clean_text)}")
    logging.info(f"First 100 chars of cleaned text: {clean_text[:100]}")
    
    # Find the position of the search term (case insensitive)
    search_text_lower = search_text.lower()
    text_lower = clean_text.lower()
    
    position = text_lower.find(search_text_lower)
    if position == -1:
        # Try finding partial matches
        words = search_text_lower.split()
        for word in words:
            if len(word) > 3:  # Only search for words longer than 3 characters
                position = text_lower.find(word)
                if position != -1:
                    break
                
    if position == -1:
        logging.info(f"No match found for search_text: '{search_text}' in content")
        return ""
        
    # Calculate the context window
    start = max(0, position - context_chars)
    end = min(len(clean_text), position + len(search_text) + context_chars)
    
    # Get the context snippet
    snippet = clean_text[start:end].strip()
    
    # Add ellipsis if we're not at the start/end of the text
    if start > 0:
        snippet = f"...{snippet}"
    if end < len(clean_text):
        snippet = f"{snippet}..."
    
    logging.info(f"Found context snippet: {snippet}")
    return snippet

def get_first_url(embedded_urls: Union[str, List[str], None]) -> Optional[str]:
    """Safely get first URL from embedded_urls."""
    if not embedded_urls:
        return None
    if isinstance(embedded_urls, list):
        return embedded_urls[0] if embedded_urls else None
    if isinstance(embedded_urls, str):
        urls = embedded_urls.split(';')
        return urls[0] if urls else None
    return None

def ensure_list(value: Union[List[str], str, None]) -> Optional[List[str]]:
    if value is None:
        return None
    if isinstance(value, str):
        return [value]
    return value

def has_filters(search_request: SearchRequest) -> bool:
    """Check if any filters are present in the search request."""
    return any([
        search_request.programs,
        search_request.ages_studied,
        search_request.focus_population,
        search_request.domain,
        search_request.subdomain_1,  # Added subdomain checks
        search_request.subdomain_2,
        search_request.subdomain_3
    ])

def build_filter_string(search_request: SearchRequest) -> Optional[str]:
    filters = []
    
    if search_request.programs:
        programs = ensure_list(search_request.programs)
        programs_filter = " or ".join(f"programs/any(p: p eq '{prog}')" for prog in programs)
        filters.append(f"({programs_filter})")
        logging.info(f"Programs filter: {programs_filter}")
        
    if search_request.ages_studied:
        ages = ensure_list(search_request.ages_studied)
        ages_filter = " or ".join(f"ages_studied/any(a: a eq '{age}')" for age in ages)
        filters.append(f"({ages_filter})")
        logging.info(f"Ages filter: {ages_filter}")
    
    if search_request.focus_population:
        population_filter = f"focus_population/any(f: f eq '{search_request.focus_population}')"
        filters.append(f"({population_filter})")
        logging.info(f"Focus population filter: {population_filter}")
    
    if search_request.domain:
        filters.append(f"domain eq '{search_request.domain}'")
        logging.info(f"Domain filter: domain eq '{search_request.domain}'")
    
    # Add subdomain filters
    if search_request.subdomain_1:
        filters.append(f"subdomain_1 eq '{search_request.subdomain_1}'")
        logging.info(f"Subdomain 1 filter: subdomain_1 eq '{search_request.subdomain_1}'")
    
    if search_request.subdomain_2:
        filters.append(f"subdomain_2 eq '{search_request.subdomain_2}'")
        logging.info(f"Subdomain 2 filter: subdomain_2 eq '{search_request.subdomain_2}'")
    
    if search_request.subdomain_3:
        filters.append(f"subdomain_3 eq '{search_request.subdomain_3}'")
        logging.info(f"Subdomain 3 filter: subdomain_3 eq '{search_request.subdomain_3}'")
    
    final_filter = " and ".join(filters) if filters else None
    logging.info(f"Final filter string: {final_filter}")
    return final_filter

def extract_pdf_filename(pdf_url: str) -> str:
    """
    Extract filename from PDF URL.
    Example: https://americorps.gov/sites/default/files/evidenceexchange/MinnesotaAllianceWithYouth.20AC220660.Report-Revised_508_1.pdf
    Returns: MinnesotaAllianceWithYouth.20AC220660.Report-Revised_508_1.pdf
    """
    try:
        return pdf_url.split('/')[-1]
    except:
        return ""

def search_single_index(
    search_text: str,
    search_client: SearchClient,
    index_name: str,
    max_results: int = 100
) -> List[Dict]:
    """
    Search content field in index and return results.
    """
    try:
        logging.info(f"Searching content field in index '{index_name}' for: {search_text}")

        response = list(search_client.search(
            search_text=f"content:{search_text}",
            select=["content", "title", "sourcepage", "sourcefile", "storageUrl"],
            query_type="full",
            top=max_results
        ))
        
        logging.info(f"Found {len(response)} results")

        results = []
        for result in response:
            content = get_search_context(result.get("content", ""), search_text)
            
            result_dict = {
                "content": content,
                "title": result.get("title", ""),
                "sourcepage": result.get("sourcepage", ""),
                "sourcefile": result.get("sourcefile", ""),
                "storageUrl": result.get("storageUrl", "")
            }
            results.append(result_dict)

        return results
    except Exception as e:
        logging.error(f"Search error: {str(e)}", exc_info=True)
        return []

def extract_pdf_stem(pdf_url: str) -> str:
    """
    Extract filename without extension from PDF URL, handling URL encoding.
    """
    try:
        from urllib.parse import unquote
        # Get the filename from the URL
        filename = pdf_url.split('/')[-1]
        # URL decode the filename
        decoded_filename = unquote(filename)
        # Remove the extension
        stem = decoded_filename.rsplit('.', 1)[0]
        logging.info(f"Extracted PDF stem: {stem} from URL: {pdf_url}")
        return stem
    except Exception as e:
        logging.error(f"Error extracting PDF stem from URL {pdf_url}: {str(e)}")
        return ""

def normalize_string(s: str) -> str:
    """
    Normalize string by removing special characters and extra spaces.
    """
    import re
    # Replace special characters with spaces
    s = re.sub(r'[^a-zA-Z0-9\s]', ' ', s)
    # Replace multiple spaces with single space
    s = re.sub(r'\s+', ' ', s)
    # Strip and lowercase
    return s.strip().lower()

def check_pdf_in_titles(pdf_stem: str, titles: list) -> bool:
    """
    Check if PDF stem matches any title, using normalized comparison.
    """
    if not pdf_stem:
        return False
        
    normalized_pdf = normalize_string(pdf_stem)
    logging.info(f"Normalized PDF stem: {normalized_pdf}")
    
    for title in titles:
        normalized_title = normalize_string(title)
        logging.info(f"Checking against normalized title: {normalized_title}")
        if normalized_pdf == normalized_title:
            logging.info(f"Found match between '{pdf_stem}' and '{title}'")
            return True
    return False

def filter_pdf_urls(pdf_urls: List[str]) -> List[str]:
    """Filter out common policy PDFs and return only relevant ones."""
    if not pdf_urls:
        return []
        
    excluded_pdfs = [
        "Whistleblower_Rights_Employees_OGC",
        "Whistleblower_Rights_and_Remedies_Contractors_Grantees_OGC"
    ]
    
    return [
        url for url in pdf_urls 
        if not any(excluded in url for excluded in excluded_pdfs)
    ]

def extract_pdf_filename(pdf_url: str) -> str:
    """
    Extract filename without extension from PDF URL, handling URL encoding.
    """
    try:
        from urllib.parse import unquote
        # Get the filename from the URL
        filename = pdf_url.split('/')[-1]
        # URL decode the filename and remove extension
        decoded_filename = unquote(filename).rsplit('.', 1)[0]
        logging.info(f"Extracted filename: {decoded_filename} from URL: {pdf_url}")
        return decoded_filename
    except Exception as e:
        logging.error(f"Error extracting filename from URL {pdf_url}: {str(e)}")
        return ""

@app.route(route="search")
def search_function(req: func.HttpRequest) -> func.HttpResponse:
    try:
        logging.info("Received search request")
        request_body = req.get_json()
        
        search_text = request_body.get('search_text', '')
        search_request = SearchRequest(
            search_text=search_text,
            programs=request_body.get('programs'),
            ages_studied=request_body.get('ages_studied'),
            focus_population=request_body.get('focus_population'),
            domain=request_body.get('domain'),
            subdomain_1=request_body.get('subdomain_1'),
            subdomain_2=request_body.get('subdomain_2'),
            subdomain_3=request_body.get('subdomain_3')
        )

        # Initialize clients
        endpoint = os.environ["SEARCH_SERVICE_ENDPOINT"]
        key = os.environ["SEARCH_SERVICE_API_KEY"]
        primary_index_name = os.environ["SEARCH_INDEX_NAME"]
        secondary_index_name = os.environ.get("SECONDARY_SEARCH_INDEX_NAME", "pdf-html")

        credential = AzureKeyCredential(key)
        primary_client = SearchClient(endpoint=endpoint, index_name=primary_index_name, credential=credential)
        secondary_client = SearchClient(endpoint=endpoint, index_name=secondary_index_name, credential=credential)

        # Build filter string
        filter_string = build_filter_string(search_request)
        select_fields = [
            "content", 
            "embedded_urls", 
            "programs", 
            "ages_studied", 
            "focus_population",
            "domain",
            "subdomain_1",
            "subdomain_2",
            "subdomain_3",
            "resource_type",
            "pdf_urls",
            "title"
        ]

        # If no search text, just return the count
        if not search_text:
            results = primary_client.search(
                search_text="*",
                filter=filter_string,
                select=["id"],
                top=1,
                include_total_count=True
            )
            
            total_count = results.get_count() if hasattr(results, 'get_count') else sum(1 for _ in results)
            
            return func.HttpResponse(
                json.dumps({
                    "total_count": total_count,
                    "applied_filters": {
                        "programs": search_request.programs,
                        "ages_studied": search_request.ages_studied,
                        "focus_population": search_request.focus_population,
                        "domain": search_request.domain,
                        "subdomain_1": search_request.subdomain_1,
                        "subdomain_2": search_request.subdomain_2,
                        "subdomain_3": search_request.subdomain_3
                    }
                }),
                mimetype="application/json",
                status_code=200
            )
        
        # Execute primary search with search text
        primary_results = list(primary_client.search(
            search_text=search_text,
            filter=filter_string,
            select=select_fields,
            top=100,
            include_total_count=True
        ))
        
        total_count = primary_results.get_count() if hasattr(primary_results, 'get_count') else len(primary_results)
        
        # Process results
        search_results = []
        for result in primary_results:
            try:
                filtered_result = {
                    'content': get_search_context(result.get('content', ''), search_text),
                    'url': get_first_url(result.get('embedded_urls')),
                    'title': result.get('title', ''),
                    'programs': result.get('programs', []),
                    'ages_studied': result.get('ages_studied', []),
                    'focus_population': result.get('focus_population', []),
                    'domain': result.get('domain', ''),
                    'subdomain_1': result.get('subdomain_1', ''),
                    'subdomain_2': result.get('subdomain_2', ''),
                    'subdomain_3': result.get('subdomain_3', ''),
                    'resource_type': result.get('resource_type', ''),
                    'pdf_urls': filter_pdf_urls(result.get('pdf_urls', [])),
                    'found_in_pdf': False
                }

                # Only check PDFs if domain or resource_type is "evidence-exchange"
                domain = result.get('domain', '').lower()
                resource_type = result.get('resource_type', '').lower()
                
                if domain == "evidence-exchange" or resource_type == "evidence-exchange":
                    # Get secondary results once for each primary result
                    secondary_results = search_single_index(
                        search_text,
                        secondary_client,
                        secondary_index_name,
                        max_results=5
                    )
                    
                    # Check all PDFs against the secondary results
                    for pdf_url in filtered_result['pdf_urls']:
                        if not pdf_url:
                            continue

                        pdf_filename = extract_pdf_filename(pdf_url)
                        if not pdf_filename:
                            continue

                        logging.info(f"Checking PDF: {pdf_filename}")
                        
                        # Look for matches in secondary results
                        for sec_result in secondary_results:
                            if pdf_filename and sec_result.get('content', ''):
                                # Check if search text is in the PDF content
                                if search_text.lower() in sec_result['content'].lower():
                                    filtered_result['found_in_pdf'] = True
                                    filtered_result['pdf_content'] = sec_result.get('content', '')
                                    logging.info(f"Found search text in PDF content for {pdf_filename}")
                                    break
                        
                        if filtered_result['found_in_pdf']:
                            break

                if (filtered_result['content'] or 
                    filtered_result['url'] or 
                    filtered_result['pdf_urls']):
                    search_results.append(filtered_result)
                    
            except Exception as e:
                logging.error(f"Error processing result: {str(e)}")
                continue

        response_data = {
            "results": search_results,
            "total_count": total_count,
            "applied_filters": {
                "programs": search_request.programs,
                "ages_studied": search_request.ages_studied,
                "focus_population": search_request.focus_population,
                "domain": search_request.domain,
                "subdomain_1": search_request.subdomain_1,
                "subdomain_2": search_request.subdomain_2,
                "subdomain_3": search_request.subdomain_3
            }
        }

        return func.HttpResponse(
            json.dumps(response_data),
            mimetype="application/json",
            status_code=200
        )

    except Exception as e:
        logging.error(f"Error in search function: {str(e)}")
        return func.HttpResponse(
            json.dumps({
                "error": str(e),
                "type": type(e).__name__,
                "search_text": request_body.get('search_text', '') if 'request_body' in locals() else None,
                "filter_string": filter_string if 'filter_string' in locals() else None
            }),
            mimetype="application/json",
            status_code=500
        )