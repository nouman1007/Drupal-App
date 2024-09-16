<?php

namespace Drupal\americorps_utils\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for displaying the ENV_NAME variable.
 */
class EnvironmentController extends ControllerBase {

  /**
   * Displays the value of the ENV_NAME variable.
   *
   * @return array
   *   Render array for the page.
   */
  public function page() {
    $vars = [
      'ENV_NAME',
      'DRUPAL_DATABASE_HOST',
      'DRUPAL_DATABASE_NAME',
      'AZURE_BLOB_ACCOUNT_NAME',
      'AZURE_BLOB_CONTAINER_NAME',
      'KEYSTORE_URL',
      'AZURE_AI_SEARCH_INDEX',
    ];

    $markup = 'Environment Variables:<pre><code>';
    foreach ($vars as $var) {
      $markup .= $var . '=' . $this->getValue($var) . PHP_EOL;
    }
    $markup .= '</code></pre>';

    return [
      '#type' => 'markup',
      '#markup' => $markup,
    ];
  }

  private function getValue(string $var, bool $raw = FALSE): string
  {
    if ($raw) {
      return isset($_ENV[$var]) ? $_ENV[$var] : '';
    }
    else {
      $value = getenv($var);
      return $value ? $value : '';
    }
  }

}
