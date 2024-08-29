<?php

namespace Drupal\search_api_aais_decorations\Plugin\search_api\backend;

use DomainException;
use Drupal\Component\Utility\Html;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\Core\Link;
use Drupal\Core\Plugin\PluginDependencyTrait;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Url;
use Drupal\search_api\Backend\BackendPluginBase;
use Drupal\search_api\DataType\DataTypePluginManager;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Plugin\ConfigurablePluginInterface;
use Drupal\search_api\Query\QueryInterface;
use Drupal\search_api\SearchApiException;
use Drupal\search_api_aais\Azure\Fields\FieldsParamBuilder;
use Drupal\search_api_aais\AzureAiSearchBackendInterface;
use Drupal\search_api_aais\BackendClient\BackendClientFactory;
use Drupal\search_api_aais\BackendClient\BackendClientInterface;
use Drupal\search_api_aais\Connector\AzureAiSearchConnectorInterface;
use Drupal\search_api_aais\Connector\AzureAiSearchConnectorPluginManager;
use Drupal\search_api_aais\Exception\InvalidConnectorException;
use Drupal\search_api_autocomplete\SearchInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\search_api_aais\Plugin\search_api\backend\SearchApiAzureAiSearchBackend as Base;

/**
 * Azure AI Search backend for Search API.
 *
 * @SearchApiBackend(
 *   id = "search_api_aais",
 *   label = @Translation("Azure AI Search"),
 *   description = @Translation("Index items using an Azure AI Search server.")
 * )
 */
class SearchApiAzureAiSearchBackend extends Base {

  use DependencySerializationTrait {
    __sleep as traitSleep;
  }
  use PluginDependencyTrait;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * The data type plugin manager service.
   *
   * @var \Drupal\search_api\DataType\DataTypePluginManager
   */
  protected DataTypePluginManager $dataTypePluginManager;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
   */
  protected EventDispatcherInterface $eventDispatcher;

  /**
   * The connector plugin manager.
   *
   * @var \Drupal\search_api_aais\Connector\AzureAiSearchConnectorPluginManager
   */
  protected AzureAiSearchConnectorPluginManager $connectorPluginManager;

  /**
   * The Azure AI Search API client factory.
   *
   * @var \Drupal\search_api_aais\BackendClient\BackendClientFactory
   */
  protected BackendClientFactory $backendClientFactory;

  /**
   * An instantiated Azure AI Search API client.
   *
   * @var \Drupal\search_api_aais\BackendClient\BackendClientInterface
   */
  protected BackendClientInterface $client;

  /**
   * An instance of an object implementing the Config Factory Interface.
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ModuleHandlerInterface $module_handler, DataTypePluginManager $data_type_plugin_manager, AzureAiSearchConnectorPluginManager $connector_plugin_manager, BackendClientFactory $backend_client_factory, LoggerInterface $logger, EventDispatcherInterface $event_dispatcher, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $module_handler, $data_type_plugin_manager, $connector_plugin_manager, $backend_client_factory, $logger, $event_dispatcher, $config_factory);

    $this->moduleHandler = $module_handler;
    $this->dataTypePluginManager = $data_type_plugin_manager;
    $this->connectorPluginManager = $connector_plugin_manager;
    $this->backendClientFactory = $backend_client_factory;
    $this->eventDispatcher = $event_dispatcher;
    $this->logger = $logger;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('module_handler'),
      $container->get('plugin.manager.search_api.data_type'),
      $container->get('plugin.manager.search_api_aais.connector'),
      $container->get('search_api_aais.aais_backend_client_factory'),
      $container->get('logger.channel.search_api_aais'),
      $container->get('event_dispatcher'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __sleep(): array {
    $vars = $this->traitSleep();

    // Prevent the client from getting serialized.
    unset($vars[array_search('client', $vars)]);

    return $vars;
  }

  /**
   * {@inheritdoc}
   *
   * @todo Method overriding is to support return types in 10.x. Remove
   * once drupal:10.x support is dropped.
   */
  // phpcs:ignore
  public function __wakeup(): void {
    parent::__wakeup();
  }

  /**
   * {@inheritdoc}
   */
  public function getSupportedFeatures(): array {
    $features = parent::getSupportedFeatures();

    if ($this->moduleHandler->moduleExists('search_api_aais_autocomplete')) {
      $features[] = 'search_api_autocomplete';
    }

    return $features;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsDataType($type): bool {
    // @todo List supported custom data types.
    // @todo Create @SearchApiDataType plugins for each non-default data type.
    // @see https://docs.microsoft.com/en-us/rest/api/searchservice/supported-data-types
    // Support all Azure-related data types.
    return in_array($type, FieldsParamBuilder::AZURE_DATA_TYPES);
  }

  /**
   * Gets the Azure AI Search connector.
   *
   * @return \Drupal\search_api_aais\Connector\AzureAiSearchConnectorInterface
   *   The Azure AI Search connector.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *   Thrown when a plugin error occurs.
   * @throws \Drupal\search_api_aais\Exception\InvalidConnectorException
   *   Thrown when a connector is invalid.
   */
  public function getConnector(): AzureAiSearchConnectorInterface {
    $connector = $this->connectorPluginManager->createInstance($this->configuration['connector'], $this->configuration['connector_config']);
    if (!$connector instanceof AzureAiSearchConnectorInterface) {
      throw new InvalidConnectorException(sprintf('Invalid connector %s', $this->configuration['connector']));
    }

    $connector->setEventDispatcher($this->eventDispatcher);
    $connector->setBackendClientFactory($this->backendClientFactory);

    return $connector;
  }

  /**
   * {@inheritdoc}
   */
  public function getClient(): BackendClientInterface {
    // Set a reference to the configuration array.
    $config = &$this->configuration['connector_config'];

    // Fill injected config if necessary.
    $name = $this->server->getConfigDependencyName();
    $backendConfig = $this->configFactory->get($name)->get('backend_config');
    if (empty($backendConfig['connector_config']['url'])) {
      throw new DomainException('No backend connector URL config found for ' . $name);
    }
    if (empty($backendConfig['connector_config']['api_key'])) {
      throw new DomainException('No backend connector API Key config found for ' . $name);
    }
    $config['url'] = $backendConfig['connector_config']['url'];
    $config['api_key'] = $backendConfig['connector_config']['api_key'];

    if (!isset($this->client)) {
      $this->client = $this->getConnector()->createClient($config);
    }

    return $this->client;
  }

  /**
   * {@inheritdoc}
   */
  public function viewSettings(): array {
    $info = parent::viewSettings();

    $connector = $this->getConnector();
    $client = $this->getClient();

    $url = $connector->getUrl();
    $info[] = [
      'label' => $this->t('Azure AI Search URL'),
      'info' => Link::fromTextAndUrl($url, Url::fromUri($url)),
    ];

    if ($this->server->status()) {
      // If the server is enabled, check whether Azure AI Search can be reached.
      $ping = $this->server->isAvailable();
      if ($ping) {
        $msg = $this->t('The Azure AI Search API could be reached');
      }
      else {
        $msg = $this->t('The Azure AI Search API could not be reached. Further data is therefore unavailable.');
      }
      $info[] = [
        'label' => $this->t('Connection'),
        'info' => $msg,
        'status' => $ping ? 'ok' : 'error',
      ];
    }

    // Fetch the indexes on the remote server.
    $indexes = [];

    $response_data = $client->request('indexes');

    if ($response_data->isSuccessful()) {
      $body = $response_data->getBody();
      if ($body) {
        foreach ($body['value'] as $index) {
          $indexes[] = Html::escape($index['name']);
        }
      }
    }

    $info[] = [
      'label' => $this->t('Indexes on the server'),
      'info'  => empty($indexes) ? $this->t('No indexes found') : implode(', ', $indexes),
    ];

    return $info;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    $configuration = parent::defaultConfiguration();

    $configuration['connector'] = 'standard';
    $configuration['connector_config'] = [
      'url' => NULL,
      'api_key' => NULL,
      'api_version' => '2020-06-30-Preview',
    ];

    return $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $options = $this->getConnectorOptions();
    $form['connector'] = [
      '#type' => 'radios',
      '#title' => $this->t('Azure AI Search Connector'),
      '#description' => $this->t('Choose a connector to use for this Azure AI Search server.'),
      '#options' => $options,
      '#default_value' => $this->configuration['connector'],
      '#required' => TRUE,
      '#ajax' => [
        'callback' => [$this, 'buildAjaxConnectorConfigForm'],
        'wrapper' => 'aais-connector-config-form',
        'method' => 'replace',
        'effect' => 'fade',
      ],
    ];

    $this->buildConnectorConfigForm($form, $form_state);

    return $form;
  }

  /**
   * Builds the backend-specific configuration form.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function buildConnectorConfigForm(array &$form, FormStateInterface $form_state): void {
    $form['connector_config'] = [];

    $connector_id = $this->configuration['connector'];
    if (isset($connector_id)) {
      /** @var \Drupal\search_api_aais\Connector\AzureAiSearchConnectorInterface $connector */
      $connector = $this->connectorPluginManager->createInstance($connector_id, $this->configuration['connector_config']);
      if ($connector instanceof ConfigurablePluginInterface) {
        $form_state->set('connector', $connector_id);
        // Attach the Azure AI Search connector plugin configuration
        // form.
        $connector_form_state = SubformState::createForSubform($form['connector_config'], $form, $form_state);
        $form['connector_config'] = $connector->buildConfigurationForm($form['connector_config'], $connector_form_state);

        // Modify the backend plugin configuration container element.
        $form['connector_config']['#type'] = 'details';
        $form['connector_config']['#title'] = $this->t('Configure %plugin Azure AI Search connector', ['%plugin' => $connector->label()]);
        $form['connector_config']['#description'] = $connector->getDescription();
        $form['connector_config']['#open'] = TRUE;
      }
    }
    $form['connector_config'] += ['#type' => 'container'];
    $form['connector_config']['#attributes'] = [
      'id' => 'aais-connector-config-form',
    ];
    $form['connector_config']['#tree'] = TRUE;
  }

  /**
   * Handles switching the selected connector plugin.
   */
  public static function buildAjaxConnectorConfigForm(array $form, FormStateInterface $form_state) {
    // The work is already done in form(), where we rebuild the entity according
    // to the current form values and then create the backend configuration form
    // based on that. So we just need to return the relevant part of the form
    // here.
    return $form['backend_config']['connector_config'];
  }

  /**
   * Gets a list of connectors for use in an HTML options list.
   *
   * @return array
   *   An associative array of plugin id => label.
   */
  protected function getConnectorOptions(): array {
    $options = [];
    foreach ($this->connectorPluginManager->getDefinitions() as $plugin_id => $plugin_definition) {
      $options[$plugin_id] = Html::escape($plugin_definition['label']);
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state): void {
    if (!$this->isAvailable()) {
      $form_state->setError($form['connector_config'], $this->t('There was a problem connecting to the Azure server. Please verify the connector configuration.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function addIndex(IndexInterface $index): void {
    if ($this->propagateRemoteIndexChanges($index)) {
      $this->getClient()->addIndex($index);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function updateIndex(IndexInterface $index): void {
    // For now dependent on skip creation, as an update means a deletion and the
    // creation of a new index.
    if ($this->propagateRemoteIndexChanges($index)) {
      $this->getClient()->updateIndex($index);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function removeIndex($index): void {
    if ($this->propagateRemoteIndexChanges($index)) {
      $this->getClient()->removeIndex($index);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function indexItems(IndexInterface $index, array $items): array {
    if (!$index->isReadOnly()) {
      return $this->getClient()->indexItems($index, $items);
    }

    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function deleteItems(IndexInterface $index, array $item_ids) {
  }

  /**
   * {@inheritdoc}
   */
  public function deleteAllIndexItems(IndexInterface $index, $datasource_id = NULL): void {
    if (!$index->isReadOnly()) {
      $this->getClient()->clearIndex($index, $datasource_id);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function search(QueryInterface $query): void {
    try {
      $this->getClient()->search($query);
    }
    catch (SearchApiException $e) {
      $this->messenger()->addError($e->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getAutocompleteSuggestions(QueryInterface $query, SearchInterface $search, string $incomplete_key, string $user_input): array {
    // This functionality is handled by a dedicated suggester.
    // @see \Drupal\search_api_aais_autocomplete\Plugin\search_api_autocomplete\suggester\AzureAiSearch
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function isAvailable(): bool {
    $client = $this->getClient();
    $response_data = $client->request('/');
    return $response_data->isSuccessful();
  }

  /**
   * {@inheritdoc}
   */
  public function requiresSemanticConfiguration(): bool {
    return version_compare($this->configuration['connector_config']['api_version'], self::MINIMAL_SEMANTIC_CONFIGURATION_VERSION, '>=');
  }

  /**
   * Execute remote index changes (create or update)?
   *
   * @return bool
   *   Execute remote index changes?
   */
  public function propagateRemoteIndexChanges(IndexInterface $index): bool {
    // When the index was created with the option to skip remote creation,
    // return false. This will prevent the search backend to create or update
    // the remote index.
    return !$index->getThirdPartySetting('search_api_aais', 'skip_remote_index_actions');
  }

}
