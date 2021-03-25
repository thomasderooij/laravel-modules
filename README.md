## Laravel Modules
<p align="center">
<a href="https://travis-ci.com/thomasderooij/laravel-modules.svg?token=ihc7ZgBuFKG3bbmgdgKC&branch=v0.1.0"><img src="https://travis-ci.com/thomasderooij/laravel-modules.svg?token=ihc7ZgBuFKG3bbmgdgKC&branch=v0.1.0" alt="Build Status"></a>
<a href="https://packagist.org/packages/thomasderooij/laravel-modules"><img src="https://poser.pugx.org/thomasderooij/laravel-modules/license.svg" alt="License"></a>
</p>

## Install

Require this package with composer using the following command:

```bash
composer require thomasderooij/laravel-modules
```


## Docs
This package enables you to use the Laravel framework with separate modules for code that can be disables 
and have dependencies on other modules. A workbench is provided to keep track of the module you're currently
 working on, and all command, such as "make:controller" apply to the module currently in your workbench. 
 Each module has all functionality the vanilla Laravel has, and has service providers which are included in the project
  via a composite provider.

### Getting started
To get started, run the following commands:
```bash
php artisan module:init
php artisan migrate
```

### Commands
To manage your modules, you can use the following commands are provided:

```bash
php artisan module:new <module-name>
```
This creates a new module in your modules directory, and sets it to your workbench. It will also ask about its 
dependencies. If your module, called Users, is dependent on another module, called Auth, you can specify this here, 
and it will take this into account when running database migrations.<br/><br/>

```bash
php artisan module:delete <module-name>
```
This deletes a module and all of the code it contains. Only use this when you are sure you don't need the code
 in this module. If you are unsure, use the deactivate command.<br/><br/>

```bash
php artisan module:deactivate <module-name>
```
This deactivates a module. This means the code will remain intact, but the commands, controllers and routes
are not recognised, and as far as the software is concerned, do not exist.<br/><br/>

```bash
php artisan module:activate <module-name>
```
This reactivates a deactivated a module.<br/><br/>

```bash
php artisan module:set <module-name>
```
This sets one of your modules to your workbench<br/><br/>

```bash
php artisan module:unset
```
This clears your workbench<br/><br/>

```bash
php artisan module:check
```
This tells you which module, if any, is currently in your workbench<br/><br/>

```bash
php artisan module:add-dependency <module-name>
```
This allows you to add dependencies so your module, indicating your module cannot function without the upstream
module. Circular references are not allowed. E.g., Module Auth can not depend on User as long as module User is dependent
on Auth. If this is the case, you should probably consider making this just 1 module, instead of 2.
This command will only show you modules that are not downstream of your current module.<br/><br/>


```bash
php artisan module:delete-dependency <module-name>
```
This is the inverse of the add-dependency command. It shows you which dependencies your module has, and it
allows you to remove any or all of these.<br/><br/>

### Directory structure
When creating a new module, your directory structure will look as follow:

    .
    ├── app
    ├── bootstrap
    ├── config
    ├── database
    ├── modules<this is the default>
    │   └── YourModule
    │   │   ├── Console
    │   │   │   └── Kernel.php
    │   │   ├── Http
    │   │   │   └── Controllers
    │   │   │       ├── Controller.php
    │   │   ├── Providers
    │   │   │   ├── AuthServiceProvider.php
    │   │   │   ├── BroadcastServiceProvider.php
    │   │   │   ├── EventServiceProvider
    │   │   │   └── RouteServiceProvider
    │   │   └── routes
    │   │       ├── api.php
    │   │       ├── console.php
    │   │       └── web.php
    │   └── .tracker
    ├── public
    ├── resources
    ├── routes
    ├── storage
    ├── tests
    └── vendor

The .tracker file keeps track of the modules you have and their dependencies.<br/>
All directories you're not seeing, like Database, Events, Jobs, Exceptions etc. will be created when the make command 
 is invoked.<br/>
If you're having issues with PHPUnit, make sure you add your modules test directory to your phpunit.xml file.

### Laravel Commands
#### Make
All the make commands will apply to the module in your workbench and can be overwritten by using --module option.
If there is no module in your workbench and the --module option is not used, the commands
will display vanilla Laravel behaviour.
 To explicitly refer to the vanilla Laravel directories, you can use the --module=vanilla option.

#### Migrate
The migrate command looks at your module dependencies, and migrates them based on that. So make sure your downstream 
migrations don't reference your upstream migrations, because that be trouble.<br/>
Both the migrate and the migrate:fresh commands have a --modules option, in case you don't want to use your dependencies
and will migrate the modules in the order they are provided in. The modules should be comma separated, as displayed below
```bash
php artisan migrate <-- Will migrate based on your dependencies
php artisan migrate --modules=<module-1>,<module-2>....
php artisan migrate:fresh <-- Will migrate based on your dependencies
php artisan migrate:fresh --modules=<module-1>,<module-2>....
```
Migrating multiple modules in one command will make a separate migration batch per module.

### Bugs and unexpected behaviour
This project seems to be pretty functional, but might have bugs. Should you find any bugs or encounter unexpected behaviour, feel
 free to create an issue.

### Settings
In the settings, you will find a few things things:
* [`root`] <-- This is the default for your modules directory
* [`vanilla`] <-- Your app directory is considered a module, and its name can be found here. It defaults to "Vanilla"
* [`models_dir`] <-- The directory in which your models will be placed. It defaults to "Models"
* [`autoload`] <-- The directory your composer.json uses for psr4 autoloads

The vanilla Laravel name is just a module name for the default behaviour. If you want to change that name, either
change it in the config/modules file, or add 'MODULES_VANILLA={your preferred name here}' to your .env file

### Roadmap
The following things are planned, loosely
 * Broadcast service provider composite functionality
 * migrate:refresh command
 * Drawing pretty ASCII pictures to visualise your dependencies
 
In that order. Probably.

## License

This Laravel add-on is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
