<?php return '<?php

namespace Root\\NewModule\\Console;

use Illuminate\\Console\\Scheduling\\Schedule;
use Thomasderooij\\LaravelModules\\Console\\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application\'s command schedule.
     *
     * @param \\Illuminate\\Console\\Scheduling\\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(base_path(\'Root/NewModule/Console/Commands\'));

        require base_path(\'Root/NewModule/routes/console.php\');
    }
}
';
