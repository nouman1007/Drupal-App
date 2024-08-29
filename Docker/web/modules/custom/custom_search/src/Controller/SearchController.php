<?php

// src/Controller/SearchController.php

namespace Drupal\custom_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\custom_search\Service\AzureSearchService;

class SearchController extends ControllerBase {
  protected $azureSearchService;

  public function __construct(AzureSearchService $azureSearchService) {
    $this->azureSearchService = $azureSearchService;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('custom_search.azure_search_service')
    );
  }

  public function search(Request $request) {
    $query = $request->query->get('q');
    $results = $this->azureSearchService->search($query);
    return new JsonResponse($results);
  }
}
