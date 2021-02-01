<?php return '<?php

return [

    /*
     |------------------------------------------------------
     | Default modules root
     |------------------------------------------------------
     |
     | Here you can specify the default root directory for your modules
     | Should you change this, make sure to also make a change in your composer.json under
     | "autoload": {
     |     "psr-4": {
     |         "MyModules\\\\": "MyModules/"
     |     }
     | }
     | And run "composer dump-autoload" in your terminal.
     */
    "root" => env("MODULES_ROOT", "MyModules"),
    "vanilla" => env("VANILLA_LARAVEL", "Vanilla"),
    "autoload" => env("MODULES_AUTOLOAD", "MyModules"),
];
';
