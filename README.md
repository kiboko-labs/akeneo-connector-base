# Akeneo Connector Utils

## TL;DR

This component brings tools and utilities for connectors developers.

This adapter brings :

 * XML data source parsing tools
 * Attribute option value discovering
 * Attribute list input
 * Assets processor (images attributes)
 * Variant group assets export
 * Dummy reader/processor/writer
 * Utility traits for your connectors
 
## Versions

| PIM version | Component version |
|:-----------:|:-----------------:|
| 1.6.*       | 1.2.*             |
| 1.5.*       | 1.1.*             | 
| 1.4.*       | 1.0.*             |

Note : From version 1.1, namespace changed from `Luni\Component\Connector` to `Kiboko\Component\Connector`
 
## Utilities

### `AttributeManager`

This helps you to create parameterizable connectors:

```php
<?php

$attributeManager = new Kiboko\Component\Connector\Manager\AttributeManager($attributeRepository);
$attributeManager->getAttributeChoices('pim_catalog_image'),
```
 
This is used for a media assets exporting job, like [`ProductAssetsProcessor`](#productassetsprocessor-and-variantgroupassetsprocessor)

```yaml
# Resources/config/readers.yml
parameters:
    luni_connector.reader.dummy_item.class: Kiboko\Component\Connector\Processor\DummyReader

services:
    luni_connector.reader.dummy_item:
        class: '%luni_connector.reader.dummy_item.class%'
```

```yaml
# Resources/config/processors.yml
parameters:
    luni_connector.processor.dummy_item.class: Kiboko\Component\Connector\Processor\DummyProcessor

services:
    luni_connector.processor.dummy_item:
        class: '%luni_connector.processor.dummy_item.class%'
```

```yaml
# Resources/config/writers.yml
parameters:
    luni_connector.writers.dummy_item.class: Kiboko\Component\Connector\Processor\DummyWriter

services:
    luni_connector.writers.dummy_item:
        class: '%luni_connector.writers.dummy_item.class%'
```

### `ProductAssetsProcessor` and `VariantGroupAssetsProcessor`

These processors are suited for Magento assets exporting from Akeneo CE, when you have created multiple image attributes.

![Assets export manager](docs/assets-connector.png)

In your bundle, you will need theses configurations:

```yaml
# Resources/config/services.yml
# app/config/services.yml
parameters:
    acme_dummy_connector.manager.attributes.class: Kiboko\Component\Connector\Manager\AttributeManager

    acme_dummy_connector.job.job_parameters.validator.image_attribute_validator.class:      Kiboko\Component\Connector\JobParameters\Constraint\ImageAttributeValidator
    acme_dummy_connector.job.job_parameters.validator.image_attribute_list_validator.class: Kiboko\Component\Connector\JobParameters\Constraint\ImageAttributeListValidator
    
    acme_dummy_connector.job.job_parameters.default_values_provider.assets_export.class:        Kiboko\Component\Connector\JobParameters\DefaultValuesProvider\ProductAssetsExport
    acme_dummy_connector.job.job_parameters.constraint_collection_provider.assets_export.class: Kiboko\Component\Connector\JobParameters\ConstraintCollectionProvider\ProductAssetsExport
    acme_dummy_connector.job.job_parameters.form_configuration_provider.assets_export.class:     Kiboko\Component\Connector\JobParameters\FormConfigurationProvider\ProductAssetsExport
services:
    # Validation services
    validator.image_attribute:
        class: '%acme_dummy_connector.job.job_parameters.validator.image_attribute_validator.class%'
        arguments:
            - '@luni_assets.manager.attributes'
        tags:
            - { name: validator.constraint_validator }
    validator.image_attribute_list:
        class: '%acme_dummy_connector.job.job_parameters.validator.image_attribute_list_validator.class%'
        arguments:
            - '@luni_assets.manager.attributes'
        tags:
            - { name: validator.constraint_validator }
         
    # Default values for our JobParameters
    acme_dummy_connector.job.job_parameters.default_values_provider.assets_export:
        class: '%acme_dummy_connector.job.job_parameters.default_values_provider.assets_export.class%'
        arguments:
            -
                - 'assets_job' # the job name
        tags:
            - { name: akeneo_batch.job.job_parameters.default_values_provider }

    # Validation constraints for our JobParameters
    acme_dummy_connector.job.job_parameters.constraint_collection_provider.assets_export:
        class: '%acme_dummy_connector.job.job_parameters.constraint_collection_provider.assets_export.class%'
        arguments:
            -
                - 'assets_job' # the job name
        tags:
            - { name: akeneo_batch.job.job_parameters.constraint_collection_provider }

    # Form configuration for our JobParameters
    acme_dummy_connector.job.job_parameters.form_configuration_provider.assets_export:
        class: '%acme_dummy_connector.job.job_parameters.form_configuration_provider.assets_export.class%'
        arguments:
            -
                - 'assets_job' # the job name
        tags:
            - { name: pim_import_export.job_parameters.form_configuration_provider }
```

## Removed functionality in version 1.2

### `NameAwareTrait` and `ConfigurationAwareTrait` traits

The traits `NameAwareTrait` and `ConfigurationAwareTrait` were removed in version 1.2, since Akeneo 1.6 changed its options retrieval in batch steps

See [Akeneo 1.6 changelog](https://github.com/akeneo/pim-community-standard/blob/1.6/UPGRADE-1.6.md#remove-the-reference-to-akeneocomponentbatchitemabstractconfigurablestepelement)

### `Kiboko\Component\Connector\Writer\File\CsvVariantGroupWriter` writer

This writer is not needed anymore
