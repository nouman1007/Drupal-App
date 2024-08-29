<?php

// src/Service/AzureSearchService.php

namespace Drupal\custom_search\Service;

use GuzzleHttp\Client;
use Drupal\Component\Serialization\Json;

class AzureSearchService {

  protected $httpClient;
  protected $apiKey;
  protected $endpoint;

  public function __construct() {
    $this->httpClient = new Client();
    $this->apiKey = 'YOUR_AZURE_SEARCH_API_KEY';
    $this->endpoint = 'https://YOUR_AZURE_SEARCH_SERVICE_NAME.search.windows.net';
  }

  public function search($query) {
    $response = $this->httpClient->request('GET', $this->endpoint . '/indexes/YOUR_INDEX_NAME/docs', [
      'query' => [
        'api-version' => '2021-04-30-Preview',
        'search' => $query,
      ],
      'headers' => [
        'Content-Type' => 'application/json',
        'api-key' => $this->apiKey,
      ],
    ]);

    $body = $response->getBody();
    return Json::decode($body->getContents());
  }
}
