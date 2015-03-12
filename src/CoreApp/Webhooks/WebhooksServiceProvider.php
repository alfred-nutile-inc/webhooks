<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 3/11/15
 * Time: 2:05 PM
 */

namespace AlfredNutileInc\CoreApp\Webhooks;

use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class WebhooksServiceProvider extends ServiceProvider {

    public $listening = ['eloquent.*'];
    protected $webhooks;
    protected $firing;
    protected $event;
    protected $callbacks = [];
    protected $pool;
    protected $requests = [];
    protected $options = ['verify' => false];

    /**
     * @var Client
     */
    protected $client;

    public function boot()
    {
        $this->publishes([
            __DIR__.'/database/migrations' => base_path('database/migrations'),
        ]);
    }

    public function register()
    {
        foreach($this->listening as $webookable)
        {
            $this->app['events']->listen($webookable, function($event) use ($webookable) {
                if(!Config::get("seeding"))
                    $this->react(Event::firing(), $event);
            });
        }
    }

    public function react($firing, $event)
    {
        $this->setFiring($firing);
        $this->setEvent($event);
        $this->getWebhooks();
        $this->seeIfMatchingWebhooksAndSet();
        $this->callbackCallbacks();
    }

    protected function getWebhooks()
    {
        if($this->webhooks == null)
            $this->setWebhooks();
        return $this->webhooks;
    }

    /**
     * @param mixed $webhooks
     *
     * Observer in Model to Clear this when new webhooks added.
     */
    protected function setWebhooks($webhooks = false)
    {
        if($webhooks == false)
        {
            $webhooks = Cache::rememberForever('webhooks', function()
            {
                try
                {
                    return Webhook::all();
                } catch(\Exception $e)
                {
                    return [];
                }
            });
        }
        $this->webhooks = $webhooks;
    }

    private function seeIfMatchingWebhooksAndSet()
    {
        foreach($this->webhooks as $webhook)
        {
            if($webhook->event == $this->getFiring())
            {
                $this->setCallbacks(['callback' => $webhook, 'event' => $this->getEvent()]);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getFiring()
    {
        return $this->firing;
    }

    /**
     * @param mixed $firing
     */
    public function setFiring($firing)
    {
        $this->firing = $firing;
    }

    /**
     * @return array
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * @param array $callbacks
     */
    public function setCallbacks($callbacks)
    {
        $this->callbacks[] = $callbacks;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    private function callbackCallbacks()
    {
        $this->buildRequestsPool();
        $this->sendRequests();
    }

    protected function buildRequestsPool()
    {
        foreach($this->callbacks as $callback)
        {
            $request = $this->getClient()
                ->createRequest(
                    'POST',
                    $callback['callback']->callback_url,
                    [ 'body' => json_encode(serialize($callback['event'])), 'verify' => false]);
            $this->setRequests($request);
        }
    }

    protected function createBody($body)
    {
        return json_encode(serialize($body));
    }

    protected function sendRequests()
    {
        if(count($this->requests) > 0 && App::environment() != 'testing')
        {
            $this->getClient()->sendAll(
                $this->getRequests()
            );
        }
    }

    /**
     * @return array
     */
    public function getPool()
    {
        return $this->pool;
    }

    /**
     * @param array $pool
     */
    public function setPool($pool)
    {
        $this->pool[] = $pool;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        if($this->client == null)
            $this->setClient();
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient($client = null)
    {
        if($client == null)
            $client = new Client();
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function getRequests()
    {
        return $this->requests;
    }

    public function setRequests($request)
    {
        $this->requests[] = $request;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($key, $value)
    {
        $this->options[$key] = $value;
    }


}