<?php

$databases = [];
$databases['default']['default'] = [
  'database' => getenv('DRUPAL_DATABASE_NAME'),
  'username' => getenv('DRUPAL_DATABASE_USER'),
  'password' => getenv('DRUPAL_DATABASE_PASSWORD'),
  'prefix' => '',
  'host' => getenv('DRUPAL_DATABASE_HOST'),
  'port' => getenv('DB_PORT') ?: '3306',
  'namespace' => 'Drupal\Core\Database\Driver\mysql',
  'driver' => 'mysql',
  'pdo' => array(
    PDO::MYSQL_ATTR_SSL_CA => '/var/www/html/DigiCertGlobalRootCA.crt.pem', // Path to SSL CA certificate
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true, // Optional: Disables server certificate verification if needed
  ),
];

$settings['hash_salt'] = getenv('hash_salt');

// Recommended setting for Drupal 10 only
$settings['state_cache'] = TRUE;

// This will prevent Drupal from setting read-only permissions on sites/default.
$settings['skip_permissions_hardening'] = TRUE;

// This will ensure the site can only be accessed through the intended host names.
$settings['trusted_host_patterns'] = ['.*'];

// Don't use Symfony's APCLoader. ddev includes APCu; Composer's APCu loader has better performance.
$settings['class_loader_auto_detect'] = FALSE;

// Set $settings['config_sync_directory'] if not set in settings.php.
if (empty($settings['config_sync_directory'])) {
  $settings['config_sync_directory'] = '../config/sync';
}

// Override drupal/symfony_mailer default config to use Mailpit.
$config['symfony_mailer.settings']['default_transport'] = 'sendmail';
$config['symfony_mailer.mailer_transport.sendmail']['plugin'] = 'smtp';
$config['symfony_mailer.mailer_transport.sendmail']['configuration']['user'] = '';
$config['symfony_mailer.mailer_transport.sendmail']['configuration']['pass'] = '';
$config['symfony_mailer.mailer_transport.sendmail']['configuration']['host'] = 'localhost';
$config['symfony_mailer.mailer_transport.sendmail']['configuration']['port'] = '1025';

// Enable verbose logging for errors.
// https://www.drupal.org/forum/support/post-installation/2018-07-18/enable-drupal-8-backend-errorlogdebugging-mode
$config['system.logging']['error_level'] = 'verbose';
$config['search_api.server.evidence_report']['backend_config']['connector_config']['url'] = getenv('KEYSTORE_URL');
$config['search_api.server.evidence_report']['backend_config']['connector_config']['api_key'] = getenv('KEYSTORE_API_KEY');
