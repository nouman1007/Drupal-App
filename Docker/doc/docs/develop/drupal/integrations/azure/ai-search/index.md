import Critical from '@site/src/components/Critical';
import Note from '@site/src/components/Note';
import Drupal from '@site/src/components/Drupal';
import Video from '@site/src/components/Video';

# Azure AI Search
<Video url="https://www.youtube.com/watch?v=_2Ax43Dd3Fg" />

* [Overview](https://azure.microsoft.com/en-us/products/ai-services/ai-search)
* [Drupal Module](https://drupal.org/project/search_api_aais)
* [Drupal Module Issues](https://www.drupal.org/project/issues/search_api_aais?categories=All)
* [API](https://learn.microsoft.com/en-us/rest/api/searchservice)

## Configuration
The Drupal module can be configured <Drupal path="/admin/config/search/search-api" label="here" />.

### Environment Variables

The following environment variables must be defined within every built container:
```bash
KEYSTORE_URL=
KEYSTORE_API_KEY=
```

### DDEV Sandbox Environment
Refer to [Drupal](../..) documentation if DDEV is not already installed.

<Critical message="In order to Drupal obtain search results, Drupal must connect with your Azure AI Search index." />

<Note message="Azure settings are defined in `.ddev/config.local.yaml` for DDEV users. Use `ddev install` if it does not exist." />

#### Advanced
Azure settings are distributed in the encrypted `.ddev/dist.config.local.yaml` file. Use `ddev decrypt` & `ddev encrypt` if that file needs to be edited.
