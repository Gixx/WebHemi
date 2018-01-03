# Change log #

## Version 4.0.0-5.2 ##
* Replace the Symfony Dependency Injection Container with a WebHemi solution with keep the performance (speed) but reduce
the memory consumption
* Change copyright information to 2018

## Version 4.0.0-5.1 ##

* Refactor the Environment module to truely support multiple domains that points to the same document root.
* Add HTTP Clien module (just in case)
* Add FTP module (I have plans to use it)
* Add Mailer module
* TODO: I should really write some unit tests... It's the lowest coverage EVER :(

## Version 4.0.0-5.0 ##

* Huge improvement in codebase:
  * Add Filesystem classes (DataStorage, DataEntity, Actions)
  * Add final version of some helpers: GetCategoriesHelper, GetTagsHelper and GetDatesHelper
* Improvement in Router: finally found a solution how to handle dynamic content
  * Get rid of FastRoute since it was unable to fulfill complex regular expressions like `^(?P<path>\/[\w\/\-]*\w)?\/(?P<basename>(?!index\..?html$)[\w\-]+\.[a-z0-9]{2,5})$`
  * Develop a simple Router
  * Introduce RouteProxy to handle dynamic contents
* Add/rename some database tables 
* Extend database tables with new fields where they were necessary
* Update unit tests to work also with the new codes

## Version 4.0.0-4.6 ##

* Improvements in the default theme.
* Add I18n module:
  * Service to set language, locale and timezone
  * Driver to handle `.po` translations (currently only via the `gettext` PHP extension)
  * The `WebHemi\DateTime` also uses the I18n module to get the correct date format
* Fix composer package versions to avoid errors upon accidental update.
* Increase unit test coverage.
* Minor fixes (documentation, typos etc.)

## Version 4.0.0-4.5 ##

* Add ValidatorInterface with basic validators:
  * _NotEmptyValidator_ - to check if an element's value is empty (including the multi-select elements)
  * _RangeValidator_ - to check if an element's value is within the range set up

## Version 4.0.0-4.4 ##

* Add support of CLI applications (e.g.: cronjobs)
* Fix config inheritance and Dependency Injection issues
* Typehints:
  * Fix missing parameter typehints
  * Fix typo issues
  * Add better PHPDoc typehints (e.g.: in index.php)
* Update packages to fix Code Coverage issues

## Version 4.0.0-4.3 ##

* Add support for connecting to multiple databases
* Add support for service configuration inheritance 
* Refactor the Dependency Injection Container Service:
  * Add abstract class for the internal operation (init, resolve inheritance etc)
  * Change the way of registering services and service instances
  * The Symfony adapter implements only the library-specific methods

~~**NOTE: There's an issue with the PHPUnit Code Coverage: partially covers some array assignments**~~

## Version 4.0.0-4.2 ##

* Add Application function skeletons
* Add Control Panel categories
* Add Theme manager function skeletons
* A bit simplify the ACL: the policy has no use the isAllowed field, since if a policy is not defined for a resource, than that resource is not allowed to view...

## Version 4.0.0-4.1 ##

* Add support for form presets.
* Add Markdown parser and renderer filter.
* Minor fixes

## Version 4.0.0-4.0 ##

* I had an idea, so I refactored the whole WebHemi namespace:
  * moved the adapters to their module's namespace,
  * add `ServiceInterface` to almost every module,
  * rename the adapters to `ServiceAdapter` and use namespace aliases,
  * own solution for a module is moved under `<Module>\ServiceAdapter\Base` namespace.
* Made the `index.php` to be independent from the implementation: use `ServiceInterface` declarations for the core objects.
* Refactor the unit tests as well, but in the easiest possible way: change the `use` statements and used aliases.
* Update the README diagrams for a better and more precize overview.
* Minor fixes regarding to the refactor process.

## Version 4.0.0-3.3 ##

* Refactor UnitTests to PHPUnit 6.
* Add/fix unit tests, increase coverage.
* Refactor trait used by the renderer and helper:
  * rename ThemeCheckTrait to GetSelectedThemeResourcePathTrait since that is what it does, 
  * hide properties and methods used by this trait from the descendant classes,
  * make it to be independent of descendant classes (inject dependencies).

## Version 4.0.0-3.2 ##

* Restructure Config.
* Add WebHemi\Acl to be able to reuse validation.
* Add RendererFilterInterface and RendererHelperInterface.
* Make the renderer helpers and filters configurable.
* Refactor the TwigRendererAdapter and TwigExtension to use the renderer configuration.
* Add basic renderer helpers (getStat(), defined(file), isAllowed(url))
* Minor fixes in Auth-related codes.
* Add/fix unit tests. TODO: reach 100% coverage again.

## Version 4.0.0-3.1 ##

* Refactor the WebHemi\Data to PHP 7.1 and fix the Unit Tests.
* Add missing functionality of loading data back into the form after submit.
* Fix issues with WebHemi\Form.
* Add unit tests to WebHemi\Form.

## Version 4.0.0-3.0 ##

* Refactor almost the complete WebHemi namespace to PHP 7.1.
* Refactor the WebHemi\Form to be more simple but still be flexible (experimental) - TODO: Unit Tests needed.
* Refactor the WebHemi\Form view templates to let user take the render process into their hands.
* Introduce SQLiteDriver and SQLiteAdapter to be more proper in Unit Tests.
* Refactor Middleware classes to change Request and Response via parameter reference (no need return value).
* Fix codes with 'mixed' argument and return type to be more consistent where possible.

## Version 4.0.0-2.2 ##

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

## Version 4.0.0-2.1 ##

* Add AuthAdapterInterface.
* Add a very basic Auth adapter and ACL Middleware implementations.
* Refactor config structure and Application parameters.
* Solved to add core Services to the DI, so they can be injected into other services if needed.

## Version 4.0.0-2.0 ##

* Add brand new WebHemi\Form support
* Add Twig templates for generating semantic WebHemi\Form markup  
* Solve Twig template inheritance (when no custom found use the default one)
* Minor fixes
* Move all continuous integration tools to Scrutinizer

## Version 4.0.0-1.2 ##

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

## Version 4.0.0-1.1 ##

* Add RouterAdapter implemtation
* Add RendererAdapterInterface implementation
* Add Environment management
* Clean up configuration
* Add custom theme support
* Add application support for both sub-domain and sub-directory types

## Version 4.0.0-1.0 ##

* Plan, document and implement Middleware Pipeline system
* Define and implement base middleware classes: Routing-, Dispatcher-, FinalMiddleware
* Add InMemoryAdapter implementation
* Connect repository to Scrutinizer CI and improve code quality
* Add Unit tests, improve code coverage
* Minor changes in code

## Version 4.0.0-0.5 ##

* According to a [DI benchmark](https://github.com/TomBZombie/php-dependency-injection-benchmarks) switched to SymfonyDI
* Add ConfigInterface and implementation
* Add DependencyInjectionAdapterInterface implementation
* Add basic DI configuration
* Fix DataStorage::$dataGroup names

## Version 4.0.0-0.4 ##

* Applied code style changes suggested by StyleCI
* Fixed all PHP MD issues
* Fix filename typo issues
* Change the way of using the Entities

## Version 4.0.0-0.3 ##

* Add first/fake unit test
* Fix NPath complexity

## Version 4.0.0-0.2 ##

* Building basic application framework
* Define Interfaces
* Basic Implementations of some Interfaces

## Version 4.0.0-0.1 ##

* Initial commit
