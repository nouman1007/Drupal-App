# Release Checklist

## Preparation
Refer to the [README](README.md) for information about any one of these items.

- [ ] Ensure all tests are passing (check GitHub workflows).
- [ ] Ensure changes are documented in the [change log](CHANGELOG.md). Release titles should be linked to GitHub.
- [ ] Ensure all changes above make it into the `main` branch

## Release

- [ ] Create a signed tag locally and push it up. Tag should be named `xx.yy.zz`.
- [ ] Go to GitHub and add release notes from the relevant `CHANGELOG` section.

## Build Script Requirements
The build script should ideally do the following, in order.
If any of the steps fail, the build must not be deployed.

- [ ] Create a DB backup
- [ ] Install Composer dependencies: `composer install`
- [ ] Ensure `settings.local.php` file exists & is configured for the target environment (see [settings.local.dist.php](web/sites/default/settings.local.dist.php) for more info)
- [ ] Run Drupal database updates: `drush updb`
- [ ] Ensure Drupal caches are rebuilt: `drush cr`
- [ ] Import Drupal configuration: `drush cim`
- [ ] Ensure Drupal caches are rebuilt again: `drush cr`
- [ ] Run any additional Drupal database updates: `drush updb`
- [ ] Run Drupal cron: `drush cron`
- [ ] Ensure Drupal caches are rebuilt again: `drush cr`
- [ ] Run unit tests, if any
- [ ] Run behavioral tests, if any
- [ ] Deploy the build
