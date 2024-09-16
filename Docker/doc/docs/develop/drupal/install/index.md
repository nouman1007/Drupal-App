---
sidebar_position: 1
---
import Note from '@site/src/components/Note';
import Todo from '@site/src/components/Todo';
import Video from '@site/src/components/Video';

# Sandbox Installation
This project is setup to use [DDEV](https://ddev.com/about) for development within a sandbox.

The following services are used to develop the site:

- DDEV
- Composer
- NodeJS
- NPM
- Gulp
- Gulp-CLI
- Storybook 8
- OpenAI API

<Video url="https://www.youtube.com/watch?v=8TaL6UmOohc" />

## Install DDEV
DEV is a Docker-based tool that provides a local development environment for
Drupal. Here are some ways to install DDEV on macOS and Windows:

### macOS
  Install DDEV using Homebrew by following these steps:
      1. Install Homebrew
      2. Run `brew install ddev/ddev/ddev`
      3. Run `ddev -v` to confirm that DDEV is installed

### Windows
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

### Linux
<Todo />

## Repository Setup
Clone the repo to your Sandbox Development Machine
- `git clone git@github.com:skytech-americorps/AmeriCorpsLMS.git`

## Configure DDEV Globally
Navigate to the cloned repo directory and run
- `ddev config --global --auto`

## Install
Install the DDEV AmeriCorps Sandbox
- `ddev install`

<Note message="This command can safely be used throughout the development process" />

## Develop
That's it! Start [developing](..)!
