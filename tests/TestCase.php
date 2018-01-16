<?php

namespace App;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://mimic.loc';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }


    public function setUp()
    {
        parent::setUp();
        $this->allow_entry = base64_encode("almasi:slatkasi");
        $this->token = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjk2LCJpc3MiOiJodHRwOi8vbWltaWMubG9jL2FwaS9hdXRoL2xvZ2luIiwiaWF0IjoxNTE2MTI5NjYyLCJleHAiOjIxNDc0ODM2NDcsIm5iZiI6MTUxNjEyOTY2MiwianRpIjoiNmxablRWSUxuZWw5azh6ViJ9.HVnkL2xZJY5H2cEOLEfmU-Yq5Z4_84sgMSzQ6A_cr6Q';
    }

    /**
     *
     * @param  string $url  URL to post to
     * @param  array $data Array of data to post
     * @return response
     */
    public function doPost($url, $data)
    {
        return $this->json('POST', 'api/'.$url, $data,
        [
            'AllowEntry' => $this->allow_entry,
            'Authorization' => $this->token,
        ]);
    }

    /**
     *
     * @param  string $url  URL to get to
     * @param  array $data Array of data to post
     * @return response
     */
    public function doGet($url, $data)
    {
        return $this->json('GET', 'api/'.$url, $data,
        [
            'AllowEntry' => $this->allow_entry,
            'Authorization' => $this->token,
        ]);
    }
}
