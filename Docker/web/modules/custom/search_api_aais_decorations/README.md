# Search API AAIS Decorations
This module overrides certain functionality provided by Search API AAIS module.

## Overridden Functionality

### Semantic Ranking
This functionality can likely be removed in the near future, once Azure AI Search is fully configured for this project.

If so, these files & this documentation section can likely be removed:

* [Services YAML](search_api_aais_decorations.services.yml)
* [QueryParamBuilderDecorator](src/QueryParamBuilderDecorator.php)

### Server Credentials Form Fields
Server credentials should not be committed.

## Configure Azure AI Search
Only do this if not already configured.

1. Create Azure Cognitive Search Service:
    - Go to the Azure Portal, search for "Azure Cognitive Search," and create a new service.
    - Configure the service (e.g., name, pricing tier, resource group).
2. Create an Index in Azure AI Search:
    - Define the schema for the index, including fields like title, summary, content, metadata, etc.
    - Use Azure Portal, CLI, or REST API to create and configure the index.
3. Upload Documents to Azure AI Search:
    - Use Azure Blob Storage or another data source to store documents.
   - Configure data import to Azure AI Search.
