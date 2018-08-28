<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Cron\UploadToAws;
use App\Cron\Hashtags\UpdateHashtagPopularity;
use App\Cron\PushNotifications\RemoveOldPushTokens;

//FAKES
use App\Cron\Fakes\Mimics\Upvotes\FakeUpvotes;
use App\Cron\Fakes\Mimics\Upvotes\ResetFakeUpvotes;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
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

        //REAL
        $model = [
            'UploadToAws' => $container->make(UploadToAws::class),
            'UpdateHashtagPopularity' => $container->make(UpdateHashtagPopularity::class),
            'RemoveOldPushTokens' => $container->make(RemoveOldPushTokens::class),
        ];
        
        $schedule->call(function () use ($model) {
            $model['UploadToAws']->uploadOriginalMimicsToAws();
            $model['UploadToAws']->uploadResponseMimicsToAws();
        })->everyFiveMinutes();

        $schedule->call(function () use ($model) {
            $model['UpdateHashtagPopularity']->run();
        })->daily();

        $schedule->call(function () use ($model) {
            $model['RemoveOldPushTokens']->run();
        })->weekly();

        //FAKES
        //Upvotes
        $model = [
            'FakeUpvotes' => $container->make(FakeUpvotes::class),
            'ResetFakeUpvotes' => $container->make(ResetFakeUpvotes::class),
        ];

        $schedule->call(function () use ($model) {
            $model['ResetFakeUpvotes']->run();
        })->weekly();
        
        $schedule->call(function () use ($model) {
            $model['FakeUpvotes']->run();
        })->everyTenMinutes();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
        $this->load(__DIR__.'/Commands/Mimic');
    }
}
