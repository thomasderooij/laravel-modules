<?php return '<?php

namespace MyModulesDir\\MYMODULE\\Policies;

use App\\Models\\User;
use Illuminate\\Auth\\Access\\HandlesAuthorization;

class MyNewPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
}
';
