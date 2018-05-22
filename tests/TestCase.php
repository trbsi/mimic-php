<?php

namespace Tests;

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
        //user id: 96, original email: dario_facebook@yahoo.com
        $this->token = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9taW1pYy5sb2NcL2FwaVwvYXV0aFwvbG9naW4iLCJpYXQiOjE1MjQwODIwODMsImV4cCI6MTUzMTQ4OTQ0MywibmJmIjoxNTI0MDgyMDgzLCJqdGkiOiJOMm5Vc2RQUmNOZ3g5TlFBIiwic3ViIjo5Nn0.sFxNguxkoQiI6AWEh1G0-UwTrlqbuTdvQd71A1qpG4A';
    }

    /**
     *
     * @param  string $url  URL to post to
     * @param  array $data Array of data to post
     * @param  string $version Api vesrion: v1, v2...
     * @return response
     */
    public function doPost($url, $data, $version)
    {
        return $this->json('POST', 'api/'.$url, $data,
        [
            'AllowEntry' => $this->allow_entry,
            'Authorization' => $this->token,
            'Accept' => 'application/vnd.app.'.$version.'+json',
        ]);
    }

    /**
     *
     * @param  string $url  URL to get to
     * @param  array $data Array of data to post
     * @param  string $version Api vesrion: v1, v2...
     * @return response
     */
    public function doGet($url, $data, $version)
    {
        return $this->json('GET', 'api/'.$url, $data,
        [
            'AllowEntry' => $this->allow_entry,
            'Authorization' => $this->token,
            'Accept' => 'application/vnd.app.'.$version.'+json',
        ]);
    }

    /**
     *
     * @param  string $url  URL to send data to
     * @param  array $data Array of data to post
     * @param  string $version Api vesrion: v1, v2...
     * @return response
     */
    public function doDelete($url, $data, $version)
    {
        return $this->json('DELETE', 'api/'.$url, $data,
        [
            'AllowEntry' => $this->allow_entry,
            'Authorization' => $this->token,
            'Accept' => 'application/vnd.app.'.$version.'+json',
        ]);
    }
}
