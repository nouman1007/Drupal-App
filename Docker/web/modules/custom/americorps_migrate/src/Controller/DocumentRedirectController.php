<?php
namespace Drupal\americorps_migrate\Controller;

use DomainException;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;

/**
 * Provides a redirect for document file requests.
 */
class DocumentRedirectController extends ControllerBase {

  /**
   * Redirects file requests to the external URL.
   *
   * @param string $file
   *   The file name from the URL.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response to the external URL.
   */
  public function redirectFile($file, Request $request) {
    $account = getenv('AZURE_BLOB_ACCOUNT_NAME');
    $container = getenv('AZURE_BLOB_CONTAINER_NAME');
    if (empty($account)) {
      throw new DomainException('AZURE_BLOB_ACCOUNT_NAME must not be empty.');
    }
    if (empty($container)) {
      throw new DomainException('AZURE_BLOB_CONTAINER_NAME must not be empty.');
    }
    $domain = $account . '.blob.core.windows.net';
    $external_url = 'https://' . $domain . '/' . $container . '/' . $file;
    return new TrustedRedirectResponse($external_url, 307);
  }

}
