Change log
==========

Version 4.0.0-3.1
-----------------
* Refactor the WebHemi\Data to PHP 7.1 and fix the Unit Tests.
* Add missing functionality of loading data back into the form after submit.
* Fix issues with WebHemi\Form.
* Add unit tests to WebHemi\Form.

Version 4.0.0-3.0
-----------------
* Refactor almost the complete WebHemi namespace to PHP 7.1.
* Refactor the WebHemi\Form to be more simple but still be flexible (experimental) - TODO: Unit Tests needed.
* Refactor the WebHemi\Form view templates to let user take the render process into their hands.
* Introduce SQLiteDriver and SQLiteAdapter to be more proper in Unit Tests.
* Refactor Middleware classes to change Request and Response via parameter reference (no need return value).
* Fix codes with 'mixed' argument and return type to be more consistent where possible.

Version 4.0.0-2.2
-----------------
* Create the [Docker Builder](https://github.com/Gixx/docker-builder) project to provide a PHP 7.1 dev environment:
  * with nginx 
  * with https (self-signed)
  * with PHP 7.1
  * with MySQL 5.7
* Plan to refactor the codebase to PHP 7.1:
  * will use strict type mode if possible
  * will add parameter type hinting (try to eliminate "mixed" type),
  * will add return types (also nullable),
  * will refactor classes to use only interface declarations for parameters and return types.

Version 4.0.0-2.1
-----------------
* Add AuthAdapterInterface.
* Add a very basic Auth adapter and ACL Middleware implementations.
* Refactor config structure and Application parameters.
* Solved to add core Services to the DI, so they can be injected into other services if needed.

Version 4.0.0-2.0
-----------------
* Add brand new WebHemi\Form support
* Add Twig templates for generating semantic WebHemi\Form markup  
* Solve Twig template inheritance (when no custom found use the default one)
* Minor fixes
* Move all continuous integration tools to Scrutinizer

Version 4.0.0-1.2
-----------------
* Continue on cleaning up the configuration
* Clean up unnecessary Exception classes
* Http adapter:
  * Extend Psr\Http\Message interfaces to link as less external resources as possible
  * Standardize Request object attributes in the new (extended) Http interfaces
* Middleware Actions:
  * Add new interface to be able to distinguish its instances form other middleware classes
  * Add an AbstractMiddlewareAction class with finalized invoke method
* Routing:
  * Fix issues with URLs without tailing slash
  * Add ability of route config parameters (regex)
* Application:
  * Finalize web application execution process (managing the pipeline)
  * Add Unit test
* Unit Test:
  * Add traits and new fixtures
  * Follow up changes

Version 4.0.0-1.1
-----------------
* Add RouterAdapter implemtation
* Add RendererAdapterInterface implementation
* Add Environment management
* Clean up configuration
* Add custom theme support
* Add application support for both sub-domain and sub-directory types

Version 4.0.0-1.0
-----------------
* Plan, document and implement Middleware Pipeline system
* Define and implement base middleware classes: Routing-, Dispatcher-, FinalMiddleware
* Add InMemoryAdapter implementation
* Connect repository to Scrutinizer CI and improve code quality
* Add Unit tests, improve code coverage
* Minor changes in code

Version 4.0.0-0.5
-----------------
* According to a [DI benchmark](https://github.com/TomBZombie/php-dependency-injection-benchmarks) switched to SymfonyDI
* Add ConfigInterface and implementation
* Add DependencyInjectionAdapterInterface implementation
* Add basic DI configuration
* Fix DataStorage::$dataGroup names

Version 4.0.0-0.4
-----------------
* Applied code style changes suggested by StyleCI
* Fixed all PHP MD issues
* Fix filename typo issues
* Change the way of using the Entities

Version 4.0.0-0.3
-----------------
* Add first/fake unit test
* Fix NPath complexity

Version 4.0.0-0.2
-----------------
* Building basic application framework
* Define Interfaces
* Basic Implementations of some Interfaces

Version 4.0.0-0.1
-----------------
* Initial commit
