<?php
use AlfredNutileInc\CoreApp\Webhooks\WebhooksServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
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
        $one = (object) [ 'id' => 'foo', 'callback_url' => 'http://foo', 'event' => 'eloquent.updated: ScreenShooter\Models\ScreenshooterJob'];
        $two = (object) [ 'id' => 'bar', 'callback_url' => 'http://foo', 'event' => 'eloquent.updated: ScreenShooter\Models\ScreenshooterJob'];
        $results = [
            $one,
            $two
        ];

        //Do not want to make real callbacks but do want to make sure methods are hit
        $provider = m::mock('AlfredNutileInc\CoreApp\Webhooks\WebhooksServiceProvider[buildRequestsPool, callbackCallbacks]',
            [new Application()]);
        //Set fake results cause we know the db works
        $provider->setWebhooks($results);
        //Instead of mocking all of the internals here I just return some results eg the two that I am testing
        $provider->shouldReceive('buildRequestsPool')->andReturn($results);
        //Set the event name
        $provider->setFiring('eloquent.updated: ScreenShooter\Models\ScreenshooterJob');
        //The Event object coming in we are only worried about updates
        $job = m::mock();
        $job->status = 'done'; //in the long run this is what counts
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