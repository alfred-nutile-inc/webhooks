<?php
use AlfredNutileInc\CoreApp\Webhooks\WebhooksServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Mockery as m;


class WebhooksListenerTest extends \TestCase {

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
        $job = m::creat();
        $job->status = 'done';
        $provider->setEvent($job);

        //Act
        $provider->react($provider->getFiring(), $provider->getEvent());

        //Assert
        $this->assertCount(2, $provider->getCallbacks());
    }


    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }
}