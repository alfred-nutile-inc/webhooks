<?php namespace AlfredNutileInc\CoreApp\Webhooks\Console;

use AlfredNutileInc\CoreApp\Helpers\UuidHelper;
use AlfredNutileInc\CoreApp\Webhooks\Webhook;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class WebhookAddCommand extends Command {
	use UuidHelper;
	
        protected $signature = 'core-app:webhook-add {url : Url to callback to} {event: Even in single quotes to list for eg \'\\App\\Events\\UserCreated\\\'}';

	protected $description = 'Add a webhook to the system';
	protected $url;
	protected $event;


	public function __construct()
	{
		parent::__construct();
	}


	public function fire()
	{

		$this->url      = $this->argument('url');
		$this->event 	= $this->argument('event');

		try {
			Webhook::create([
				'id' 		    => $this->getUuid(),
				'callback_url' 	=> $this->url,
				'event'			=> $this->event
			]);

			$this->info(sprintf("Webhook added to system for %s and event %s with id %s", $this->url, $this->event, $this->getUuid()));
		} catch(\Exception $e)
		{
			$this->error(sprintf("Error adding webhook to database %s", $e->getMessage()));
		}
	}

}
