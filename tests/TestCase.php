<?php

class TestCase extends \PHPUnit_Framework_TestCase {

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
//        $app = require __DIR__.'/../bootstrap/app.php';
//
//        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
//
//        return $app;
    }

    public function refreshDb()
    {
//        $path = base_path();
//        exec("cd $path && php artisan migrate:refresh --seed");
    }

}
