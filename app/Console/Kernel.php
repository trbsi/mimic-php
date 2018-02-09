<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        $data = [
            'uploadToAws' => app('App\Helpers\Cron\UploadToAws'), 
            'fakeMimicData' => app('App\Helpers\Cron\FakeMimicData')
        ];
        
        $schedule->call(function () use ($data) {
            $data['uploadToAws']->uploadOriginalMimicsToAws();
            $data['uploadToAws']->uploadResponseMimicsToAws();
            $data['fakeMimicData']->adjustMimicData();
        })->everyFiveMinutes();
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
