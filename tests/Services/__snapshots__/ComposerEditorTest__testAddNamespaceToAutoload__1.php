<?php return '{
    "name": "thomasderooij/laravel-modules",
    "description": "package description",
    "keywords": [
        "key",
        "words"
    ],
    "licence": "MIT",
    "require": {
        "package": "^version",
        "other-packer": "version.*"
    },
    "require-dev": {
        "dev-package": "1.2.3",
        "helpers": "1.*"
    },
    "autoload": {
        "psr-4": {
            "Thomasderooij\\\\LaravelModules\\\\": "src",
            "ModuleRoot\\\\": "moduleRoot/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Thomasderooij\\\\LaravelModules\\\\Tests\\\\": "tests"
        }
    },
    "extra": {
        "laravel": [
            "things"
        ]
    },
    "prefer-stable": true
}';
