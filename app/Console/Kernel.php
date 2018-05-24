<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Helpers\Cron\UploadToAws;
use App\Helpers\Cron\FakeMimicData;
use App\Helpers\Cron\Hashtags\UpdateHashtagPopularity;

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
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //https://gist.github.com/davejamesmiller/bd857d9b0ac895df7604dd2e63b23afe (laravel what to use instead of new)
        $container = app();
        $model = [
            'UploadToAws' => $container->make(UploadToAws::class),
            'FakeMimicData' => $container->make(FakeMimicData::class),
            'UpdateHashtagPopularity' => $container->make(UpdateHashtagPopularity::class),
        ];
        
        $schedule->call(function () use ($model) {
            $model['UploadToAws']->uploadOriginalMimicsToAws();
            $model['UploadToAws']->uploadResponseMimicsToAws();
            $model['FakeMimicData']->run();
        })->everyFiveMinutes();

        $schedule->call(function () use ($model) {
            $model['UpdateHashtagPopularity']->run();
        })->daily();

        
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
