import logging
import azure.functions as func
from azure.storage.blob import BlobServiceClient, ContentSettings
from azure.core.exceptions import ResourceExistsError
import os
from urllib.parse import unquote
import json

def extract_filename_from_url(url: str) -> str:
    """Remove https:// and replace slashes with underscores"""
    # Remove any trailing slashes
    url = url.rstrip('/')
    
    # Remove https:// or http:// from the URL
    if url.startswith('https://'):
        url = url[8:]  # Remove 'https://'
    elif url.startswith('http://'):
        url = url[7:]  # Remove 'http://'
    
    # Replace slashes with underscores
    url = url.replace('/', '_')
    
    # Ensure the URL ends with .html
    if not url.endswith('.html'):
        url = url + '.html'
    
    # Replace encoded characters in URL if any (like %2C) with their actual characters
    url = unquote(url)
        
    return url

def main(req: func.HttpRequest) -> func.HttpResponse:
    logging.info('Python HTTP trigger function processed a request for HTML upload.')
    
    try:
        # Get form data
        url = req.form.get('url')
        body = req.form.get('body')
        
        if not url or not body:
            return func.HttpResponse(
                json.dumps({"error": "Both 'url' and 'body' are required in form-data"}),
                mimetype="application/json",
                status_code=400
            )

        # Extract filename from URL
        filename = extract_filename_from_url(url)
        original_url = url  # Keep original URL for metadata
        logging.info(f'Extracted filename: {filename}')
        
        # Get connection string from environment variable
        connect_str = os.environ['AzureWebJobsStorage']
        
        # Create the BlobServiceClient
        blob_service_client = BlobServiceClient.from_connection_string(connect_str)
        
        # Get container client
        container_name = "htmlcontent"
        container_client = blob_service_client.get_container_client(container_name)
        
        # Create container if it doesn't exist
        try:
            container_client.get_container_properties()
        except Exception:
            container_client = blob_service_client.create_container(container_name)

        # Create blob client
        blob_client = container_client.get_blob_client(filename)
        
        # Upload the blob
        content_settings = ContentSettings(
            content_type='text/html',
            content_disposition=f'inline; filename="{filename}"'
        )
        
        blob_client.upload_blob(
            data=body,
            overwrite=True,
            content_settings=content_settings,
            metadata={'original_url': original_url}
        )
        
        logging.info(f'Successfully uploaded blob: {filename}')
        
        return func.HttpResponse(
            body=json.dumps({
                "message": "HTML content uploaded successfully",
                "container": container_name,
                "filename": filename,
                "originalUrl": original_url
            }),
            mimetype="application/json",
            status_code=202
        )
        
    except Exception as e:
        logging.error(f"Error: {str(e)}")
        return func.HttpResponse(
            json.dumps({"error": str(e)}),
            mimetype="application/json",
            status_code=500
        )