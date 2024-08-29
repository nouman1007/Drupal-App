# Learning Campus Project
[Sandbox](https://learning-campus-lms.ddev.site) | [Dev](https://devdrupal10-3-1-defea3dwgaa8ceaf.eastus-01.azurewebsites.net) | [UAT](https://uat1americorpswebapp.azurewebsites.net) | [Prod](  https://americorps.gov)

## Environment Variables
This project primarily uses just one environment variable to help tell Drupal what settings to
use. Additional environment variables may be used by DevOps if deemed necessary.
Make sure the `HOME` environment variable is set to one of the following options:

* prod
* uat
* dev

If the `HOME` var is not set, Drupal will run as if in a `sandbox` dev environment.

## Integrate Azure with Drupal
This site relies on Azure for File Storage & Search functionality.
It will not work well without those services integrated.

1. Follow the instructions within [settings.local.dist.php](web/sites/default/settings.local.dist.php).
2. If you are not sure which values to use, ask your supervisor for that info via Teams.

# Drupal Environment Setup

This project is setup to use DDEV for local development. The following services
are used to develop the site:

- DDEV
- Composer
- NodeJS
- NPM
- Gulp
- Gulp-CLI
- Storybook 8
- OpenAI API

## Steps for Local Development:
1. Install DDEV:

    DEV is a Docker-based tool that provides a local development environment for
    Drupal. Here are some ways to install DDEV on macOS and Windows:
    ```
    macOS:
      Install DDEV using Homebrew by following these steps:
          1. Install Homebrew
          2. Run `brew install ddev/ddev/ddev`
          3. Run `ddev -v` to confirm that DDEV is installed

    Windows:
      DDEV recommends using WSL2 to install DDEV on Windows, but you can also
      install it directly on Windows with an installer. Here are some steps for
      installing DDEV on Windows using WSL2:
          1. Install the Chocolatey package manager (optional)
          2. Initialize mkcert
          3. Install WSL2 and a distro like Ubuntu
          4. Optionally install Docker Desktop for Windows and enable WSL2
          integration with the distro
          5. Install DDEV inside your distro
          6. Verify that your distro uses WSL version 2 with wsl.exe -l -v
    ```
2. Clone the repo to your local machine:
    - `git clone git@github.com:skytech-americorps/AmeriCorpsLMS.git`
3. Navigate to the cloned repo directory and run:
    - `ddev config --global --auto`
4. Install Composer Dependencies:
    - `composer install`
5. Navigate to the custom themes directory:
    - `cd web/themes/custom/drupalx_theme`
7. Install the correct NodeJS version:
    - `nvm install`
    - `nvm use`
8. Install NodeJS Dependencies:
    - `npm ci`
4. Compile the CSS and JavaScript files with Gulp:
    - `npm run build`
5. Start Storybook to view the Component Library in your browser:
    - `npm run storybook`
