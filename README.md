CrudGenerator
=============

This is an attempt to build a generator that can create CRUD scaffolding in PHP that is easily extendable
and can be updated/regenerated at any time.

Planned Features
----------------

- Automatic generation of entities, controllers and views
- Convention over configuration (any default settings can be overridden)
- The generator code is not necessary to run the generated application
- Default template engine (Twig) can be replaced with any other solution
- Default database engine (PDO) can be replaced with any other solution
- Regenerating the scaffolding doesn't overwrite manual changes
- Different ways to import the model (from an existing database, from a config file, from existing entities...)

Usage
-----

1. Copy `generator/config.example.json` to `generator/config.json` and edit the settings in there.
2. Install the required dependencies via composer:

         $ composer install

3. Start the generator:

         $ php generator/generator.php
