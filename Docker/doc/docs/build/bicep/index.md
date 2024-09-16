---
sidebar_position: 3
---
import Video from '@site/src/components/Video';
import Todo from '@site/src/components/Todo';

# Bicep
## Azure Infrastructure Deployment
<Video url="https://www.youtube.com/watch?v=MP60ND7Upn4" />

## Azure Infrastructure Deployment

This README provides an overview of the Azure infrastructure deployment using Bicep templates, covering key modules such as Virtual Network, MySQL Server, Key Vault, Azure Container Registry (ACR), and App Service.

### Modules and Resources

Each module is responsible for deploying specific Azure resources. The following sections provide details about the resources within each module.

****please provide instructions for how to run the scripts****
****Add a instruction to add a parameterized resource group****
***** Add instruction for creating a security policy for key vault manually****


### File Structure

- `README.md`
- `parameters.json`
- `main.bicep`
- `modules`

    - **`network.bicep`**
        - Defines the Virtual Network and associated subnets.
        - Configures security groups for MySQL and App Service subnets.
        - Outputs the subnet IDs.

    - **`mysql.bicep`**
        - Defines the MySQL Flexible Server.
        - Configures the MySQL database.
        - Outputs the MySQL server host URL.

    - **`keyVault.bicep`**
        - Defines the Key Vault.
        - Creates secrets for MySQL credentials and hash salt.
        - Outputs the Key Vault URI.

    - **`acr.bicep`**
        - Defines the Azure Container Registry (ACR).
        - Configures ACR admin user.
        - Outputs ACR credentials and login server URL.

    - **`appService.bicep`**
        - Defines the App Service Plan and Web App.
        - Integrates with ACR and Key Vault.
        - Outputs the Web App URL and principal ID.

    - **`keyVaultAccessPolicy.bicep`**
        - Configures Key Vault access policies for the Web App.
        - Outputs the Key Vault resource ID.

### Modules Overview

- **ACR Module (`modules/acr.bicep`)**
    - **Description:** Deploys an Azure Container Registry (ACR) resource.
    - **Resources:**
        - ACR with admin user enabled and basic SKU.

  - **Parameters:**
    | Name             | Type   | Description                 |
    |------------------|--------|-----------------------------|
    | `location`       | string | The location of the ACR.     |
    | `acrName`        | string | Name of the ACR resource.    |
  - **Outputs:**
    | Name          | Type   | Description             |
    |---------------|--------|-------------------------|
    | `acrLoginUrl` | string | ACR login server URL.    |
    | `acrUserName` | string | ACR admin username.    |
    | `acrPassword` | string | ACR admin password.    |

- **Network Module (`modules/network.bicep`)**
  - **Description:** Creates a Virtual Network (VNet) with subnets.
  - **Resources:**
    - Virtual Network with defined CIDR ranges.
    - Subnets for MySQL and App Service with associated Network Security Groups (NSGs).

  - **Parameters:**

    | Name                       | Type   | Description                        |
    |----------------------------|--------|------------------------------------|
    | `location`                 | string | The location/region for the resources. |
    | `vnetName`                 | string | Name of the VNet.                  |
    | `mysqlSubnetName`          | string | Name of the MySQL subnet.          |
    | `appServiceSubnetName`     | string | Name of the App Service subnet.    |
    | `vnetCidrRange`            | string | CIDR range for the VNet.           |
    | `mySqlSubnetCidrRange`     | string | CIDR range for the MySQL subnet.   |
    | `appServiceSubnetCidrRange`| string | CIDR range for the App Service subnet. |
  - **Outputs:**
    | Name        | Type   | Description              |
    |-------------|--------|--------------------------|
    | `mysqlSubnetId`    | string | ID of the MySQL subnet. |
    | `appServiceSubnetId`    | string | ID of the App Service subnet. |

- **Key Vault Module (`modules/keyvault.bicep`)**
  - **Description:** Deploys an Azure Key Vault.
  - **Parameters:**
    | Name             | Type   | Description                      |
    |------------------|--------|----------------------------------|
    | `kvName`         | string | Name of the Key Vault.           |
    | `location`       | string | The location of the Key Vault.   |
  - **Outputs:**
    | Name          | Type   | Description                      |
    |---------------|--------|----------------------------------|
    | `kvUri`       | string | The URI of the Key Vault.        |

- **Key Vault Policy Module (`modules/keyvaultpolicy.bicep`)**
    - **Description:** Configures Key Vault access policies for the Web App to retrieve secrets.
- **Resources:**
  - Key Vault access policy for Web App’s managed identity.
  - **Parameters:**
    | Name             | Type   | Description                             |
    |------------------|--------|-----------------------------------------|
    | `kvName`         | string | Name of the Key Vault to apply policies to. |
    | `principalId`       | string | Object ID of the principal.             |

- **MySQL Server Module (`modules/mysql.bicep`)**
    - **Description:** Deploys the MySQL Flexible Server and configures the database.
- **Resources:**
  - MySQL Server with specified version, SKU, and storage configuration.
  - MySQL Database with charset and collation.
  - **Parameters:**
    | Name                       | Type    | Description                                     |
    |----------------------------|---------|-------------------------------------------------|
    | `location`                 | string  | The location/region for the resources.          |
    | `mysqlServerName`          | string  | Name of the MySQL Server.                       |
    | `mysqlAdminUser`           | string  | Admin username for the MySQL Server.            |
    | `mysqlAdminPassword`       | string  | Admin password for the MySQL Server.            |
    | `mysqlDatabaseName`        | string  | Name of the MySQL database.                     |
    | `mysqlSubnetId`            | string  | ID of the MySQL subnet.                         |
    | `skuName`                  | string  | Azure database for MySQL SKU name.              |
    | `StorageSizeGB`            | int     | Azure database for MySQL storage size (in GB).  |
    | `StorageIops`              | int     | Azure database for MySQL storage IOPS.          |
    | `mysqlVersion`             | string  | MySQL version.                                  |
    | `SkuTier`                  | string  | Azure database for MySQL pricing tier.          |
    | `backupRetentionDays`      | int     | MySQL Server backup retention days.             |
    | `geoRedundantBackup`       | string  | Geo-Redundant Backup setting for MySQL Server.  |

  - **Outputs:**
    | Name          | Type   | Description                      |
    |---------------|--------|----------------------------------|
    | `mysqlServerHost`       | string | URL of the MySQL Server.        |


- **Key Vault Module (`modules/keyVault.bicep`)**
    - **Description:** Deploys the Azure Key Vault and configures secrets for MySQL credentials and hash salt.
- **Resources:**
  - Key Vault with soft delete disabled.
  - Secrets for MySQL admin user, password, database, and hash salt.
- **Outputs:**
    | Name            | Type   | Description                      |
    |-----------------|--------|----------------------------------|
    | `keyVaultUri`   | string | URI of the Key Vault.            |
    | `keyVaultResource` | object | Key Vault resource object.      |
    | `keyVaultId`    | string | Key Vault resource ID.           |

### App Service Module (`appService.bicep`)
- **Description:** Deploys the App Service Plan and Web App, integrating with ACR and Key Vault.
- **Resources:**
  - App Insights and Log Analytics Workspace.
  - App Service Plan with specified SKU.
  - Web App configured to use Docker from ACR, with app settings for Key Vault secrets.
- **Parameters:**
    | Name                    | Type   | Description                                |
    |-------------------------|--------|--------------------------------------------|
    | `location`              | string | Azure region where resources will be deployed. |
    | `appServicePlanName`    | string | Name of the App Service Plan.              |
    | `webAppName`            | string | Name of the Web App.                       |
    | `dockerImageVersion`    | string | Version of the Docker image to be used.     |
    | `acrLoginServer`        | string | Login server URL of the Azure Container Registry (ACR). |
    | `keyVault`              | string | Name of the Key Vault.                     |
    | `appServiceSubnetId`    | string | Subnet ID for the App Service.             |
    | `acrUserName`           | string | Username for accessing ACR.                |
    | `acrPassword`           | secure | Password for accessing ACR.                |
    | `appskuName`            | string | SKU name for the App Service Plan.         |
    | `appSkuTier`            | string | SKU tier for the App Service Plan.         |
    | `appInsightsName`       | string | Name of the Application Insights instance. |
    | `logAnalyticsWorkspaceName` | string | Name of the Log Analytics workspace.       |
- **Outputs:**

    | Name           | Type   | Description                            |
    |----------------|--------|----------------------------------------|
    | `webAppUrl`    | string | URL of the Web App (from `webApp.properties.defaultHostName`). |
    | `principalId`  | string | Principal ID of the Web App identity (from `webApp.identity.principalId`). |

- **Key Vault Secrets Module (`modules/keyVaultSecrets.bicep`)**
    - **Description:** Configures secrets in Key Vault for various application settings.
    - **Resources:**

      - **`dbUserSecret`**: Stores the MySQL admin user in the Key Vault as a secret named `dbUser`.
      - **`dbPasswordSecret`**: Stores the MySQL admin password in the Key Vault as a secret named `dbPassword`.
      - **`dbDatabaseSecret`**: Stores the MySQL database name in the Key Vault as a secret named `dbDatabase`.
      - **`dbHostSecret`**: Stores the MySQL server host URL in the Key Vault as a secret named `dbHost`.
      - **`hashSaltSecret`**: Stores the hash salt in the Key Vault as a secret named `hashSalt`.
      - **`azureBlobAccountKeySecret`**: Stores the Azure Blob Storage account key in the Key Vault as a secret named `AZURE_BLOB_ACCOUNT_KEY`.
      - **`azureBlobAccountNameSecret`**: Stores the Azure Blob Storage account name in the Key Vault as a secret named `AZURE_BLOB_ACCOUNT_NAME`.
      - **`azureBlobContainerNameSecret`**: Stores the Azure Blob Storage container name in the Key Vault as a secret named `AZURE_BLOB_CONTAINER_NAME`.
      - **`envKeySecret`**: Stores the environment key in the Key Vault as a secret named `env_key`.
      - **`envNameSecret`**: Stores the environment name in the Key Vault as a secret named `ENV_NAME`.
      - **`keystoreApiKeySecret`**: Stores the keystore API key in the Key Vault as a secret named `KEYSTORE_API_KEY`.
      - **`keystoreUrlSecret`**: Stores the keystore URL in the Key Vault as a secret named `KEYSTORE_URL`.


- **parameters:**

    | Name                        | Type   | Description                                 |
    |-----------------------------|--------|---------------------------------------------|
    | `keyVaultName`              | string | Name of the Azure Key Vault.                |
    | `mysqlAdminUser`            | string | Admin username for the MySQL server.        |
    | `mysqlAdminPassword`        | string | Admin password for the MySQL server.        |
    | `mysqlDatabaseName`         | string | Name of the MySQL database.                 |
    | `mysqlServerName`           | string | Name of the MySQL server.                   |
    | `hashSalt`                  | string | Salt used for hashing in Drupal.            |
    | `azureBlobAccountKey`       | string | Access key for the Azure Blob Storage account. |
    | `azureBlobAccountName`      | string | Name of the Azure Blob Storage account.     |
    | `azureBlobContainerName`    | string | Name of the Azure Blob Storage container.   |
    | `envKey`                    | string | Environment key for application configuration. |
    | `envName`                   | string | Name of the environment (e.g., dev, uat).   |
    | `keyVaultUri`               | string | URI of the Azure Key Vault.                 |
    | `keystoreApiKey`            | string | API key for accessing the keystore.         |
    | `keystoreUrl`               | string | URL for accessing the keystore.             |
.
### **Main Bicep File**

- **`Description`**: The main Bicep file that orchestrates the deployment of the entire infrastructure.

## Parameters

| Parameter Name            | Type      | Description                                              |
|----------------------------|-----------|----------------------------------------------------------|
| `location`                 | string    | The location/region where resources will be deployed.     |
| `acrName`                  | string    | Name of the Azure Container Registry.                     |
| `appServicePlanName`       | string    | Name of the App Service Plan.                             |
| `logAnalyticsWorkspaceName`| string    | Name of the Log Analytics Workspace.                      |
| `appInsightsName`          | string    | Name of the Application Insights resource.                |
| `webAppName`               | string    | Name of the Web App.                                      |
| `mysqlServerName`          | string    | Name of the MySQL Server.                                 |
| `mysqlAdminUser`           | string    | Username for the MySQL Server administrator.              |
| `mysqlAdminPassword`       | secure    | Password for the MySQL Server administrator.              |
| `mysqlDatabaseName`        | string    | Name of the MySQL Database.                               |
| `keyVaultName`             | string    | Name of the Azure Key Vault.                              |
| `vnetName`                 | string    | Name of the Virtual Network (VNet).                       |
| `mysqlSubnetName`          | string    | Name of the subnet for the MySQL Server.                  |
| `appServiceSubnetName`     | string    | Name of the subnet for the App Service.                   |
| `dockerImageVersion`       | string    | Version of the Docker image to be deployed.               |
| `vnetCidrRange`            | string    | CIDR range for the Virtual Network.                       |
| `mySqlSubnetCidrRange`     | string    | CIDR range for the MySQL subnet.                          |
| `appServiceSubnetCidrRange`| string    | CIDR range for the App Service subnet.                    |
| `appSkuTier`               | string    | Pricing tier for the App Service Plan (e.g., Standard).   |
| `appskuName`               | string    | SKU name for the App Service Plan (e.g., S1).             |
| `hashSalt`                 | string    | A cryptographic salt used for hashing in the Drupal app.  |
| `AZURE_BLOB_ACCOUNT_KEY`     | string | The access key for the Azure Blob Storage account.    |
| `AZURE_BLOB_ACCOUNT_NAME`    | string | The name of the Azure Blob Storage account.           |
| `AZURE_BLOB_CONTAINER_NAME`  | string | The name of the container within the Azure Blob Storage account. |
| `env_key`                    | string | An environment-specific key used for configuration or access control. |
| `ENV_NAME`                   | string | The name of the environment (e.g., dev, uat, prod).   |
| `KEYSTORE_API_KEY`           | string | The API key used for accessing the keystore service.  |
| `KEYSTORE_URL`               | string | The URL for accessing the keystore service.          |

## Deployment Steps

1. Ensure all parameters are defined either in a parameters file or passed during deployment.
2. Deploy the Bicep templates using Azure CLI or the Azure portal.

- **Resource Group Creation:**
    ```bash
    az group create --name uat1Americorps001Eastus --location eastus
    ```

- **Deployment Commands:**
    ```bash
    az deployment group create --resource-group uat1Americorps001Eastus --template-file main.bicep --parameters parameters.json
    az deployment group validate --resource-group uat1Americorps001Eastus --template-file main.bicep --parameters parameters.json
    ```

- **Key Vault Purge Command:**
    ```bash
    az keyvault purge --name uat1AmericorpsKeyVault --location eastus
