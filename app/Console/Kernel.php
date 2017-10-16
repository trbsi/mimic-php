<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Helpers\Cron\UploadToAws;
use App\Helpers\Cron\FakeUsername;
use App\Helpers\Cron\ResizeImages;

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
        $uploadToAws = new UploadToAws;
        $fakeUsername = new FakeUsername;
        $resizeImages = new ResizeImages;

        $schedule->call(function () {
            $uploadToAws->uploadOriginalMimicsToAws();
            $uploadToAws->uploadResponseMimicsToAws();
        })->everyTenMinutes();

        $schedule->call(function () {
            $fakeUsername->adjustMimicUpvoteAndUsername();
            $resizeImages->resizeImages();
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
