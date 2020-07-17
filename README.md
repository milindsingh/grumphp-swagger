# grumphp-swagger
GrumPHP Swagger check

## Usage
- Install the task using composer:
```shell script
composer require adapttive/grumphp-swagger
```

- Add the following in your grumphp.yml:
```yaml
# GrumPHP configuration
grumphp:
  tasks:
    swagger:
        swagger_schema_url: https://magento.local/rest/all/schema?services=all
services:
    Adapttive\GrumPHP\Swagger:
        arguments:
            - '@process_builder'
            - '@formatter.raw_process'
        tags:
            - {name: grumphp.task, task: swagger, priority: 0}
```