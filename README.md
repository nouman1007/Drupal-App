# Azure Functions Project Documentation

This repository contains Azure Functions for handling search operations:
- `azure-html-search`: Function for processing HTML content
- `azure-pdf-search`: Function for processing PDF content

## Prerequisites

- Azure CLI installed and configured
- Azure Functions Core Tools
- Python 3.9
- An active Azure subscription
- Azure Storage Account
- Azure Search Service configured

## Project Structure

```
├── Ai-Search/
├── BlobUpload/
├── azure-html-search/
├── azure-pdf-search/
├── BlobIndexTrigger/
├── UploadHtmlBody/
├── host.json
├── local.settings.json
└── requirements.txt
```

## Initial Setup

### 1. Create and Activate Python Virtual Environment

For Windows:
```bash
# Create virtual environment
python -m venv .venv

# Activate virtual environment
.venv\Scripts\activate
```

For macOS/Linux:
```bash
# Create virtual environment
python -m venv .venv

# Activate virtual environment
source .venv/bin/activate
```

### 2. Install Dependencies

With the virtual environment activated, install the required packages:
```bash
pip install -r requirements.txt
```

## Deployment Steps

Replace the placeholders (in angle brackets <>) with your desired values.

### 1. Create an App Service Plan

```bash
az appservice plan create \
  --name <your-app-service-plan-name> \
  --resource-group <your-resource-group> \
  --location eastus \
  --sku B1 \
  --is-linux

# Example:
# --name testing-html \
# --resource-group rg-Aisearch-Agent
```

### 2. Create the Function App

```bash
az functionapp create \
  --name <your-function-app-name> \
  --storage-account <your-storage-account> \
  --resource-group <your-resource-group> \
  --plan <your-app-service-plan-name> \
  --runtime python \
  --runtime-version 3.9 \
  --functions-version 4 \
  --os-type linux

# Example:
# --name azure-html-testing \
# --storage-account stkf5c5wgrhtage \
# --resource-group rg-Aisearch-Agent \
# --plan testing-html
```

### 3. Configure Environment Variables

Set up the environment variables based on your `local.settings.json`:

```bash
az functionapp config appsettings set \
  --name <your-function-app-name> \
  --resource-group <your-resource-group> \
  --settings \
  AzureWebJobsStorage="" \
  FUNCTIONS_WORKER_RUNTIME="python" \
  SEARCH_SERVICE_ENDPOINT="<your-search-endpoint>" \
  SEARCH_SERVICE_API_KEY="<your-search-api-key>" \
  SEARCH_INDEX_NAME="<your-search-index>" \
  SECONDARY_SEARCH_INDEX_NAME="<your-secondary-index>" \
  OPENAI_ENDPOINT="<your-openai-endpoint>" \
  OPENAI_KEY="<your-openai-key>"
```

### 4. Deploy the Function

Deploy your function to Azure:

```bash
func azure functionapp publish <your-function-app-name>

# Example:
# func azure functionapp publish azure-html-testing
```

## Environment-Specific Deployments

Each function might have different settings in their respective `local.settings.json`. Here's how to deploy for specific functions:

### For azure-html-search:
```bash
# Example configuration
az functionapp config appsettings set \
  --name azure-html-testing \
  --resource-group rg-Aisearch-Agent \
  --settings \
  AzureWebJobsStorage="" \
  FUNCTIONS_WORKER_RUNTIME="python" \
  SEARCH_SERVICE_ENDPOINT="https://gptkb-kf5c5wgrhtage.search.windows.net" \
  SEARCH_SERVICE_API_KEY="your-api-key" \
  SEARCH_INDEX_NAME="html-dev-index-updated" \
  SECONDARY_SEARCH_INDEX_NAME="pdf-html" \
  OPENAI_ENDPOINT="https://sementickernal1627560484.openai.azure.com/" \
  OPENAI_KEY="your-openai-key"
```

### For azure-pdf-search:
```bash
# Use similar structure but with PDF-specific settings from local.settings.json
az functionapp config appsettings set \
  --name <your-pdf-function-name> \
  --resource-group <your-resource-group> \
  --settings \
  # Add your PDF function-specific settings here
```

## Important Notes

- Ensure your Azure Storage Account exists before creating the function app
- Keep sensitive information like API keys secure
- Different functions may require different environment variables
- Always activate your virtual environment before running or deploying functions
- Keep your virtual environment folder (.venv) in .gitignore


## API Management Integration

After deploying your function, follow these steps to integrate it with Azure API Management:

### 1. Create API Operation

In Azure Portal:
- Navigate to your API Management service
- Select "APIs" from the menu
- Configure a new operation with:
  ```
  Method: POST
  URL: /search
  Consumes content type: application/json
  ```

### 2. Configure Inbound Policy

Add the following inbound policy to your API operation:

```xml
<policies>
    <inbound>
        <base />
        <set-backend-service base-url="https://<your-function-app-name>.azurewebsites.net/api" />
        <set-method>POST</set-method>
        <rewrite-uri template="/search" />
        <set-header name="Content-Type" exists-action="override">
            <value>application/json</value>
        </set-header>
    </inbound>
</policies>

<!-- Example:
<set-backend-service base-url="https://func-html-search.azurewebsites.net/api" />
-->
```

[Previous content remains the same until "Deployment Steps" section...]

## API Management Integration

After deploying your function, follow these steps to integrate it with Azure API Management:

### 1. Create API Operation

In Azure Portal:
- Navigate to your API Management service
- Select "APIs" from the menu
- Configure a new operation with:
  ```
  Method: POST
  URL: /search
  Consumes content type: application/json
  ```

### 2. Configure Inbound Policy

Add the following inbound policy to your API operation:

```xml
<policies>
    <inbound>
        <base />
        <set-backend-service base-url="https://<your-function-app-name>.azurewebsites.net/api" />
        <set-method>POST</set-method>
        <rewrite-uri template="/search" />
        <set-header name="Content-Type" exists-action="override">
            <value>application/json</value>
        </set-header>
    </inbound>
</policies>

<!-- Example:
<set-backend-service base-url="https://func-html-search.azurewebsites.net/api" />
-->
```

### 3. Test the API

Using Postman or any API testing tool:

```bash
# Endpoint
POST https://<your-api-name>.azure-api.net/search

# Headers
Content-Type: application/json

# Request Body
{
    "search_text": "your search text",
    "domain": "your domain"
}

# Example:
# POST https://index-search.azure-api.net/search
# Body:
{
    "search_text": "Influencing the academic and career pathways of Reading Partners AmeriCorps Alumni",
    "domain": "evidence-exchange"
}
```

### Common APIM Integration Issues

1. Backend URL Mismatch
   - Verify the function app URL in the backend service setting
   - Ensure the API path (/search) matches your function's route

2. Authentication
   - Check if function-level authentication is properly configured
   - Verify API Management subscription key if required

3. CORS Issues
   - Configure CORS in both Function App and API Management if needed


### Common APIM Integration Issues

1. Backend URL Mismatch
   - Verify the function app URL in the backend service setting
   - Ensure the API path (/search) matches your function's route

2. Authentication
   - Check if function-level authentication is properly configured
   - Verify API Management subscription key if required

3. CORS Issues
   - Configure CORS in both Function App and API Management if needed

## Troubleshooting

1. Verify Azure CLI is logged in
2. Check resource group and storage account exist
3. Ensure all environment variables are properly set
4. Verify Python version compatibility
5. Make sure virtual environment is activated
6. Check function app logs if deployment fails
7. Verify all required settings from `local.settings.json` are properly configured in Azure
