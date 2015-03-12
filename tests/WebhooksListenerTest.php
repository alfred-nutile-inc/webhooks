<?php
use AlfredNutileInc\CoreApp\Webhooks\WebhooksServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;

use Illuminate\Support\Facades\Event;
use ScreenShooter\Helpers\UuidHelper;
use ScreenShooter\Models\ScreenshooterJob;
use Mockery as m;
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 3/11/15
 * Time: 2:29 PM
 */

class WebhooksListenerTest extends \TestCase {
    use UuidHelper;

    public function setUp()
    {
        parent::setUp();
    }


    /**
     * Looking for updated and status = done
     * @test
     */
    public function should_react_to_matching_event_in_db()
    {
        //Arrange
        $provider = new WebhooksServiceProvider(new Application());
        $provider->setFiring('eloquent.updated: ScreenShooter\Models\ScreenshooterJob');
        $job = ScreenshooterJob::first();
        $job->status = 'done';
        $provider->setEvent($job);

        //Act
        $provider->react($provider->getFiring(), $provider->getEvent());

        //Assert
        $this->assertCount(2, $provider->getCallbacks());
    }

    /**
     * @test
     */
    public function should_callback_in_parallel()
    {

    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }
}