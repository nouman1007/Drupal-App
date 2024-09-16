---
sidebar_position: 15
---
import Example from '@site/src/components/Example';
import Todo from '@site/src/components/Todo';

# Secure

<Todo />

## Generate a Security Report

<Example label="Sandbox Environment" code="ddev console security:report" />
<Example label="Cloud Environments" code="php scripts/console/bin/console security:report" />

Check the `local/reports` folder for the generated HTML file.
