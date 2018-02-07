<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Helpers\Cron\UploadToAws;
use App\Helpers\Cron\FakeMimicData;

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
        $data1 = ['uploadToAws' => new UploadToAws, 'fakeMimicData' => new FakeMimicData];
        
        $schedule->call(function () use ($data1) {
            $data1['uploadToAws']->uploadOriginalMimicsToAws();
            $data1['uploadToAws']->uploadResponseMimicsToAws();
            $data1['fakeMimicData']->adjustMimicData();
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
