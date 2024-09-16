import Drupal from '@site/src/components/Drupal';

# Troubleshooting

## Theme

### The theme doesn't look right
1. Run `ddev install` & answer `n` to all prompts.
2. Reload the page.
3. If that does not resolve the issue, check the [themes](drupal/themes) section for additional information.

## File Storage

### I can't create a file via Drupal
* If `.ddev/config.local.yaml` does not exist, run `ddev install` to create it.

#### I still can't create a file via Drupal
* Make sure your Azure Blob Storage Sandbox Container exists, by following [these](../integrations/azure/blob-storage/#azure-sandbox-resource-configuration) instructions.

#### The above two options did not work
1.  Check & optimize <Drupal path="/admin/config/media/azure-blob-file-system" /> settings. Drupal may be timing out due to background image style generation taking too long.
2. Visit the [issue queue](https://www.drupal.org/project/issues/az_blob_fs?categories=All) for the Drupal Azure Blob File System module & troubleshoot the issue.

## Search

### Drupal reports a connection issue when attempting to search
* If `.ddev/config.local.yaml` does not exist, run `ddev install` to create it.

### Drupal returns no results when results are expected
1. Run `ddev install` & answer `n` to all prompts.
2. Check to confirm the following indexes are full:
  * <Drupal path="/admin/config/search/search-api/index/evidence_report" />
