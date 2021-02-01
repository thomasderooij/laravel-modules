<?php return '{
    "name": "laravel/laravel",
    "description": "The Laravel Framework.",
    "keywords": [
        "framework",
        "laravel"
    ],
    "license": "MIT",
    "type": "project",
    "require": {
        "laravel/framework": "~5.0"
    },
    "require-dev": {
        "phpunit/phpunit": "~4.0"
    },
    "autoload": {
        "classmap": [
            "database",
            "tests/TestCase.php"
        ],
        "psr-4": {
            "App\\\\": "app/",
            "MyModules\\\\": "MyModules/"
        }
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "minimum-stability": "dev"
}';
