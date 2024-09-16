---
sidebar_position: 2
---
import Todo from '@site/src/components/Todo';

# Docker
A build will create a distributable container based on the `Dockerfile` found
in the repository root. This container will have everything necessary to run
Drupal & integrate it with necessary external services. Drupal primarily relies
on data stored in environment variables set during the build process.

## Build Workflow
<Todo />

```mermaid
block-beta
  columns 6

  %% Docker Build.
  block:container_build["Docker Container Build\n\n\n\n\n\n\n\n\n\n\n\n\n\n"]:3
    columns 2
    aks[("Azure Key Store")] --- envars{{"Env Vars"}}
  end
  space:3
  docker_build<["Docker Build"]>(down):3
  space:3

  %% Docker Container.
  block:container["Docker Container\n\n\n\n\n\n\n\n\n\n\n\n\n\n"]:3
    columns 2
    env{{"Environment Variables"}}:1
    drupal>"Drupal"]:1
    nginx(["NGINX"]):1
  end

  %% Azure Cloud Services.
  block:azure["Azure Cloud Services\n\n\n\n\n\n\n\n\n\n"]:3
    columns 3
    space:3
    db[("DB")]
    blob[("Blob File Storage")]
    si[/"Search Index"\]
    space:1
    saisr((("AI\nSemantic Ranker")))
    sait((("AI\nTranslation")))
  end

  %% Browser.
  browser(["Browser"]):5

  %% Define the relationships between the entities.
  env --- drupal
  drupal --- db
  db --- drupal
  drupal --- blob
  blob --- drupal
  saisr --- si
  sait --- si
  drupal --- si
  si --- drupal
  nginx --- drupal
  drupal --- nginx
  nginx --- browser
  browser --- nginx
  blob --- browser
```

## Azure Integration
Azure must be integrated with Drupal in order for many site features to work.
Please visit the [Drupal Azure Integrations documentation](../develop/drupal/integrations/azure)
for more information. Developers use the same configuration all cloud environments use.
