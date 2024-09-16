---
sidebar_position: 1
---
import Note from '@site/src/components/Note';
import Example from '@site/src/components/Example';

# Develop
AmeriCorps uses [Drupal](drupal) & [Azure Cloud Services](drupal/integrations/azure) to provide cutting-edge user experiences.

<Note message="'Sandbox' generically refers to a Developer's local development environment, or to its related cloud resources." />

## Maintenance
At any time in the development process, `ddev install` can be run to perform common
maintenance tasks. It is interactive, so you can rest assured it will not overwrite
existing work unless you allow it to.

## File Encryption
<Note message="Secrets should not be committed to the Git repository unless encrypted." />

This repository configures DDEV to install Ansible, which provides robust `ddev encrypt` & `ddev decrypt` commands.

<Example label="Decrypt distributed Sandbox configuration" code="ddev decrypt .ddev/dist.config.local.yaml" />
<Example label="Encrypt distributed Sandbox configuration" code="ddev encrypt .ddev/dist.config.local.yaml" />
 