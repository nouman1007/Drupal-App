<?php
/**
 * These instructions are written with both Developers & DevOps engineers in mind.
 *
 * 1. Copy this file to settings.local.php
 *
 *    e.g. `cp settings.local.dist.php settings.local.php`
 *
 * 2. Replace the {{ * }} tags below with values the site should use in this environment.
 *
 *    e.g. `sed -i 's/{{ az_blob_account_name }}/FOO/g' settings.local.php`
 *    e.g. `sed -i 's/{{ az_blob_container_name }}/BAR/g' settings.local.php`
 *
 * 3. Uncomment lines, where necessary.
 *
 *    e.g. `sed -i 's|^// \(\$config.*;\)$|\1|' settings.local.php`
 */

// Define the environment Drupal will run within. Defaults to `sandbox`.
// Valid options: sandbox|dev|uat|prod
// $environment_name = '{{ environment_name }}';

// Azure Blob Storage configuration.
$config['az_blob_fs.settings']['az_blob_account_name'] = '{{ az_blob_account_name }}';
$config['az_blob_fs.settings']['az_blob_container_name'] = '{{ az_blob_container_name }}';
$config['key.key.azure_blob_storage']['key_provider_settings']['key_value'] = '{{ az_blob_key }}';

// Azure AI Search configuration.
$config['search_api.server.{{ search_api_server }}']['backend_config']['connector_config']['url'] = '{{ search_api_server_url }}';
$config['search_api.server.{{ search_api_server }}']['backend_config']['connector_config']['api_key'] = '{{ search_api_server_api_key }}';

// Override drupal/symfony_mailer default config to use Mailpit.
// $config['symfony_mailer.settings']['default_transport'] = 'sendmail';
// $config['symfony_mailer.mailer_transport.sendmail']['plugin'] = 'smtp';
// $config['symfony_mailer.mailer_transport.sendmail']['configuration']['user'] = '{{ sendmail_user }}';
// $config['symfony_mailer.mailer_transport.sendmail']['configuration']['pass'] = '{{ sendmail_pass }}';
// $config['symfony_mailer.mailer_transport.sendmail']['configuration']['host'] = 'localhost';
// $config['symfony_mailer.mailer_transport.sendmail']['configuration']['port'] = '1025';
