ketwaroo\laravel-autoroute
================


This is not french for highway but short for 'Automatic Router'.

It will allow you to use something closer to the classic routing like Zend Framework.

**Important Note this is a fallback route handler.** It will be registered as the very last route. Any route you define using `routes.php` files will take precedence.



## Some Terminology

 * request - The path being requested. e.g. `/admin/users/edit/1`
 * segments - The request path string being split by `/` directory separator.
 * prefix - First segment. e.g. The prefix in the request `/admin/users/edit/1` is `admin`

# Configuration

use config:publish command to

`php artisan config:publish  "cccisd/autoroute"`

@todo make this a post installation command in composer.json

You will need to define your routes in the config file for now.
Later we can have it read routes from the database if needed.

config file should look like this:

```php
<?php

/**
 * @copyright (c) 2014, 3C Institute
 */
use Cccisd\Autoroute\Constant as ARConst;

return array(
    ARConst::CONFIGKEY_DO_AUTOROUTE       => true,
    ARConst::CONFIGKEY_PACKAGE_PREFIX_MAP => array(
        '/' => 'cccisd/framework::Void.blank', // load blank action in void controller.
    ),
);

```

Side note; the config keys are defined as constants. The package was written to leverage the 'intellisense'
features of most modern IDEs.


### CONFIGKEY_DO_AUTOROUTE

default true; enables/disables automatic router.

### CONFIGKEY_PACKAGE_PREFIX_MAP

defines which url prefix (key) is handled by what package (value).

prefixes are defined as follows:
	
`/` matches the home page. *It is special.* All other prefixes should not contain a forward slash.

Examples of valid prefix formats are `user`,`admin`,`about-us`,`resources`.
I.e. lower-dash case or url case.

Invalid prefixes `moo/meow`, `login?site=foo&lang=bar`, `ABOUT_US`.

The home page prefix is a bit special

handler definition:

`vendor/package[:controller[.action]]`

A package name in the format `vendor/package` must be specified for each prefix.
You can optionally override which controller.action to use for that prefix.
Otherwise it will map to the next 2 segments in the request URL after the prefix
by default (see Default Dispatcher).


## Default Dispatcher


The default dispatcher will determine the right controller class and action method
to call for the matched prefix.

It will try to default to `<Package/Namespace>/Controllers/HomeController::indexAction()`
if no additional controller/action segments are present in the 

example:

consider the following configuration;

```php
<?php
use Cccisd\Autoroute\Constant as ARConst;

return array(
    ARConst::CONFIGKEY_DO_AUTOROUTE       => true,
    ARConst::CONFIGKEY_PACKAGE_PREFIX_MAP => array(
        '/' => 'my/site',
        'fuu' => 'my/foo-package',
        'bor' => 'bar/bar',
    ),
);

```
The following URLs would map like so:

`/` maps to `My\Site\Controllers\HomeController::indexAction()`
`fuu/maximum` maps to `My\FooPackage\Controllers\MaximumController::indexAction()`
`fuu/min/stuff` maps to `My\FooPackage\Controllers\MinController::stuffAction()`
`bar/min/stuff` maps to `Bar\Bar\Controllers\MinController::stuffAction()`

`foo/min/stuff` results in a 404 error as the prefix `foo` is not defined. Any uncaught
exception thrown within the dispatcher should result in a 404 http error as well.


### Fallback route for the Fallback route

The home route `/` is *special* in that the package used for that route is the final
fallback package before a 404 error is thrown if the first segment of the url is not
defined in the prefix map.

For example:

```php
<?php
use Cccisd\Autoroute\Constant;
return array(
    Constant::CONFIGKEY_PACKAGE_PREFIX_MAP => array(
        '/' => 'my/site',
        'fuu' => 'my/foo-package',
        'bar' => 'bar/bar',
    ),
);

```

`my/site` package has the following controllers:

`My/Site/Controllers/HomeController`
`My/Site/Controllers/SubController`

If request is `/sub`, it does not match any prefix defined, however, `my/site` does have
a `sub` controller. The request will therefore be routed to `my/site::sub.index`.

This is an effort saver as you do not need to define all prefixes and what 
`vendor/package::controller.action` they point to.

The home package will usually be your main site package and it is implied that most
requests would be handled by it.


## Package Override Dispatcher

You can define custom Autoroute dispatcher per package as well.

It needs to extend `\Cccisd\Autoroute\Dispatcher\DispatcherAbstract` and be autoloadable
using the class name `<Vendor>\<Package>\Autoroute\Dispatcher`.


### the run() abstract method

The abstract definition has no required parameters for that method.

This means implementation of that method must have all parameters optional.

### getRouteUri() abstract method

returns what the uri without the prefix should look like.

### getRouteWhere()

returns conditions for the parameters in the route uri. return empty array for no conditions.


# Future Tasks

Event hooks should be added to this package to enable code injection.

----
work in progress.


