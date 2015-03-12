<?php namespace AlfredNutileInc\CoreApp\Webhooks\Console;

use AlfredNutileInc\CoreApp\Webhooks\Webhook;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class WebhookDeleteCommand extends Command {
	protected $name = 'core-app:webhook-delete';

	protected $description = "Delete a webhook to the system with a matching uuid. \n If you do not know the uuid do not include it and a list of existing rows will show";
	protected $uuid;


	public function __construct()
	{
		parent::__construct();
	}


	public function fire()
	{

		$this->uuid 		= $this->argument('uuid');

		if($this->uuid == false)
		{
			$all = Webhook::select('id', 'callback_url', 'event')->get();
			$this->table(['id', 'url', 'event'], $all->toArray());
			$this->info("Choose a uuid and use it for delete command");
		} else {

			if($result = Webhook::where('id', $this->uuid)->first())
			{
				$result->delete();
				$this->info(sprintf("Deleted uuid %s", $this->uuid));
			} else {
				$this->info(sprintf("Uuid %s not found :( try just delete with no uuid", $this->uuid));
			}
		}
	}

	public function getArguments()
	{
		return [
			['uuid', InputArgument::OPTIONAL, "UUID to delete or do not include to see all hooks"],
		];
	}


}
