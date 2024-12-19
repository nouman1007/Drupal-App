import os
import json
import logging
import re
import azure.functions as func
from typing import List, Dict
from azure.core.credentials import AzureKeyCredential
from azure.search.documents import SearchClient

app = func.FunctionApp()

def get_search_context(text: str, search_text: str, context_chars: int = 300) -> str:
    """Returns context around where the search term was found."""
    if not text or not search_text:
        return ""
    
    text_str = str(text) if text is not None else ""
    clean_text = re.sub(r'<[^>]+>', ' ', text_str)
    clean_text = ' '.join(clean_text.split())
    
    search_terms = search_text.lower().split()
    text_lower = clean_text.lower()
    
    best_pos = -1
    for term in search_terms:
        pos = text_lower.find(term)
        if pos != -1:
            best_pos = pos
            break
    
    if best_pos == -1:
        return clean_text[:500] + "..."
        
    start = max(0, best_pos - context_chars)
    end = min(len(clean_text), best_pos + context_chars)
    
    snippet = clean_text[start:end].strip()
    
    if start > 0:
        snippet = f"...{snippet}"
    if end < len(clean_text):
        snippet = f"{snippet}..."
        
    return snippet



@app.route(route="search", auth_level=func.AuthLevel.ANONYMOUS, methods=["POST"])
def search_function(req: func.HttpRequest) -> func.HttpResponse:
    """
    Azure Function HTTP trigger handler.
    """
    try:
        req_body = req.get_json()
        search_text = req_body.get("search_text", "")

        if not search_text:
            return func.HttpResponse(
                json.dumps({"error": "search_text is required"}),
                status_code=400,
                mimetype="application/json"
            )

        # Initialize clients
        search_endpoint = os.getenv("SEARCH_ENDPOINT")
        search_key = os.getenv("SEARCH_KEY")
        index_names = os.getenv("INDEX_NAMES", "")
        
        # Log the raw values
        logging.info(f"Raw INDEX_NAMES environment variable: '{index_names}'")
        
        # Split and clean the index names
        index_list = [name.strip() for name in index_names.split(",") if name.strip()]
        logging.info(f"Processed index names: {index_list}")

        if not all([search_endpoint, search_key]) or not index_list:
            raise ValueError(f"Missing required environment variables. SEARCH_ENDPOINT: {'Yes' if search_endpoint else 'No'}, SEARCH_KEY: {'Yes' if search_key else 'No'}, Valid Index Names: {len(index_list)}")

        # Use the first valid index name
        index_name = index_list[0]
        logging.info(f"Using index: '{index_name}'")

        # Initialize search client
        search_credential = AzureKeyCredential(search_key)
        search_client = SearchClient(
            endpoint=search_endpoint,
            index_name=index_name,
            credential=search_credential
        )

        # Perform search
        results = search_single_index(
            search_text=search_text,
            search_client=search_client,
            index_name=index_name,
            max_results=10
        )

        return func.HttpResponse(
            json.dumps(results, ensure_ascii=False, indent=2),
            mimetype="application/json"
        )
        
    except ValueError as ve:
        logging.error(f"Configuration error: {str(ve)}")
        return func.HttpResponse(
            json.dumps({"error": str(ve)}),
            status_code=400,
            mimetype="application/json"
        )
    except IndexError:
        error_msg = "No valid index names found in configuration"
        logging.error(error_msg)
        return func.HttpResponse(
            json.dumps({"error": error_msg}),
            status_code=400,
            mimetype="application/json"
        )
    except Exception as e:
        logging.error(f"Search error: {str(e)}", exc_info=True)
        return func.HttpResponse(
            json.dumps({"error": str(e)}),
            status_code=500,
            mimetype="application/json"
        )