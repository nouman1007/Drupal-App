import Critical from '@site/src/components/Critical';
import Note from '@site/src/components/Note';
import Drupal from '@site/src/components/Drupal';
import Video from '@site/src/components/Video';

# Azure Blob Storage
<Video url="https://www.youtube.com/watch?v=c9T46EIHg4I" />

* [Overview](https://azure.microsoft.com/en-us/products/storage/blobs)
* [Drupal Module](https://drupal.org/project/az_blob_fs)
* [Drupal Module Issues](https://www.drupal.org/project/issues/az_blob_fs?categories=All)
* [Blob File Explorer](https://azure.microsoft.com/en-us/products/storage/storage-explorer)
* [API](https://learn.microsoft.com/en-us/rest/api/storageservices/blob-service-rest-api)

## Configuration

### Azure Sandbox Resource Configuration
<Critical message="In order to create Drupal media files, Drupal must connect with your Azure Blob File container." />

1. Visit the [Azure Portal](https://portal.azure.com).
2. Click on "sandboxstoragelmc" under "Resources".
3. Search for "Containers" in the field on the top-left & click the "Containers" link.
4. Click on the `+ Container` button. A menu will appear to the right.
5. Name it the value of `AZURE_BLOB_CONTAINER_NAME` found in `.ddev/config.local.yaml`.
6. Choose "Container" in the "Anonymous access level" field.
7. Click the `Create` button.

<Note message="If `.ddev/config.local.yaml` does not exist, run `ddev install`" />

### Drupal Module
The module can be configured <Drupal path="/admin/config/media/azure-blob-file-system" label="here" />.

### Environment Variables
The following environment variables must be defined within every built container:
```bash
AZURE_BLOB_ACCOUNT_NAME=
AZURE_BLOB_CONTAINER_NAME=
AZURE_BLOB_ACCOUNT_KEY=
```

### DDEV Sandbox Environment
Refer to [Drupal](../..) documentation if DDEV is not already installed.

<Note message="Azure settings are defined in `.ddev/config.local.yaml` for DDEV users." />

<Note message="Use `ddev install` to update `.ddev/config.local.yaml` if necessary." />

<Note message="Azure settings are distributed in the encrypted `.ddev/dist.config.local.yaml` file. Use `ddev decrypt` & `ddev encrypt` if that file needs to be edited." />
