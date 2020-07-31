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
 working on. Each module has all functionality the vanilla Laravel has, and has service providers which are 
 included in the project via a composite provider.

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
This creates a new module in your modules directory.

```bash
php artisan module:delete <module-name>
```
This deletes a module and all of the code it contains. Only use this when you are sure you don't need the code
 in this module. If you are unsure, use the deactivate command.

```bash
php artisan module:deactivate <module-name>
```
This deactivates a module. This means the code will remain intact, but the commands, controllers and routes
are not recognised, and as far as the software is concerned, do not exist.

```bash
php artisan module:activate <module-name>
```
This reactivates a deactivated a module.

```bash
php artisan module:set <module-name>
```
This sets one of your modules to your workbench

```bash
php artisan module:unset
```
This clears your workbench

```bash
php artisan module:check
```
This tells you which module, if any, is currently in your workbench

### Directory structure
When creating a new module, your directory structure will look as follow:

    .
    ├── app
    ├── bootstrap
    ├── config
    ├── database
    ├── modules<this is the default>
    │   └── YourModule
    │       ├── Console
    │       │   └── Kernel.php
    │       ├── Http
    │       │   └── Controllers
    │       │       ├── Controller.php
    │       ├── Providers
    │       │   ├── AuthServiceProvider.php
    │       │   ├── BroadcastServiceProvider.php
    │       │   ├── EventServiceProvider
    │       │   └── RouteServiceProvider
    │       └── routes
    │           ├── api.php
    │           ├── console.php
    │           └── web.php
    ├── public
    ├── resources
    ├── routes
    ├── storage
    ├── tests
    └── vendor

All other directories, like database, Events, Jobs, Exceptions etc. will be created when the make command 
 is invoked.

### Laravel Commands
#### Make
All the make commands will apply to the module in your workbench and can be overwritten by using --module option.
If there is no module in your workbench and the --module option is not used, the commands
will display vanilla Laravel behaviour.
 To explicitely refer to the vanilla Laravel directories, you can use the --module=vanilla option.

The following command(s) do not apply to your module (yet):
 * [`php artisan make:seeder`]

#### Migrate
The migrate command also applies to the module in your workbench.
Both the migrate and the migrate:fresh commands have a --modules option, and will migrate the 
modules in the order they are provided in. The modules should be comma separated, as displayed below
```bash
php artisan migrate --modules=<module-1>,<module-2>....
php artisan migrate:fresh --modules=<module-1>,<module-2>....
```
Migrating multiple modules in one command will make a separate migration batch per module.

### Bugs and unexpected behaviour
This project is currently in its beta stage. Should you find any bugs or encounter unexpected behaviour, feel
 free to create an issue.

### Settings
In the settings, you will find two things: Your current module directory, and the module name for the vanilla
laravel. You can change your module directory. Just make sure to also change it in your composer psr-4.
The vanilla Laravel name is just a module name for the default behaviour. If you want to change that name, either
change it in the config/modules file, or add 'MODULES_VANILLA={your prefered name here}' to your .env file

### Roadmap
The following things will be applied before moving to a 1.x stage:
 * Broadcast service provider composite functionality
 * module dependency tracking
 
In that order. Probably.

## License

This Laravel add-on is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
