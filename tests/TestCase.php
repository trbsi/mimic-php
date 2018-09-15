<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase {
        refreshTestDatabase as customRefreshTestDatabase;
    }

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://mimic.loc';

    /**
     * Id of our user we are testing with
     * @var int
     */
    protected $loggedUserId;

    /**
     * Bearer token
     * @var string
     */
    protected $token;

    /**
     * This is base64 for allowing access to API
     * @var string
     */
    protected $allowEntry;

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
        $this->allowEntry = base64_encode("almasi:slatkasi");
        //user id: 95
        $this->token = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9taW1pYy5sb2NcL2FwaVwvYXV0aFwvbG9naW4iLCJpYXQiOjE1MzQzNDk4MzcsImV4cCI6MTg2NDM0OTgzNywibmJmIjoxNTM0MzQ5ODM3LCJqdGkiOiI2T3dHdzdDSXQyc2JsZm9hIiwic3ViIjo5NX0.86tBOEiMZ74YIvIxc2K3r0m7AeKhcSz0kX1-0ps35DE';
        $this->loggedUserId = 95;
    }

    /**
     * Override method from RefreshDatabase
     *
     * @return void
     */
    public function refreshTestDatabase()
    {
        $this->customRefreshTestDatabase();
        //seed database
        shell_exec('php artisan db:seed');
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
            'AllowEntry' => $this->allowEntry,
            'Authorization' => $this->token,
            'Accept' => 'application/vnd.app.'.$this->version.'+json',
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
            'AllowEntry' => $this->allowEntry,
            'Authorization' => $this->token,
            'Accept' => 'application/vnd.app.'.$this->version.'+json',
        ]);
    }

    /**
     *
     * @param  string $url  URL to send data to
     * @param  array $data Array of data to post
     * @return response
     */
    public function doDelete($url, $data)
    {
        return $this->json('DELETE', 'api/'.$url, $data,
        [
            'AllowEntry' => $this->allowEntry,
            'Authorization' => $this->token,
            'Accept' => 'application/vnd.app.'.$this->version.'+json',
        ]);
    }

    /**
     *
     * @param  string $url  URL to send data to
     * @param  array $data Array of data to post
     * @return response
     */
    public function doPut($url, $data)
    {
        return $this->json('PUT', 'api/'.$url, $data,
        [
            'AllowEntry' => $this->allowEntry,
            'Authorization' => $this->token,
            'Accept' => 'application/vnd.app.'.$this->version.'+json',
        ]);
    }
}
