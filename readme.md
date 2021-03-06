# Webhooks Package

## Install

### Setup the provider

~~~
'AlfredNutileInc\CoreApp\Webhooks\WebhooksServiceProvider'
~~~

Run

~~~
php artisan vendor:publish
~~~

To publish the mirations

Before you migrate keep reading...


### Add to your DatabaseSeeder.php


~~~
#database/seeds/DatabaseSeeder.php
/**
 * Used by Webhooks to prevent seed issues
 */
Config::set('seeding', true);
~~~

And Webhooks

~~~
#database/seeds/DatabaseSeeder.php
Config::set('seeding', true);
$this->call('AppSeeder');
$this->call('WebhooksSeeder');
~~~

And copy over vendors/alfred-nutile-inc/webhooks/src/CoreApp/Webhooks/database/seeds/WebhooksSeeder.php to database/seeds

If you want seed data place it in there. There are a couple of examples in there.

@TODO remove the seeder step and make it part of publish

It will end up looking like this

~~~
#database/seeds/DatabaseSeeder.php

public function run()
{
	Model::unguard();
	if(Config::get('database.default') != 'sqlite') {
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
	}

	/**
	 * Used by Webhooks to prevent seed issues
	 */
	Config::set('seeding', true);
	$this->call('AppSeeder');
	$this->call('WebhooksSeeder');

	Config::set('seeding', false);
	if(Config::get('database.default') != 'sqlite') {
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}
}
~~~

Just speeds up seed work as the events are ignored

Now you can migrate

~~~
php artisan migrate
~~~

### Add the commands to your Kernal.php

~~~
    protected $commands = [
        'AlfredNutileInc\CoreApp\Webhooks\Console\WebhookAddCommand',
        'AlfredNutileInc\CoreApp\Webhooks\Console\WebhookDeleteCommand'
    ];
~~~

## Command to add and delete webhooks from the db

### Add

~~~
php artisan core-app:webhook-add http://full.com/post/path 'event.name In Quotes if needed'
~~~


### Delete

~~~
php artisan core-app:webhook-delete

+--------------------------------------+-----------------------------------------------------------+---------------------------------------------------------+
| id                                   | url                                                       | event                                                   |
+--------------------------------------+-----------------------------------------------------------+---------------------------------------------------------+
| 1bc89184-853b-4ac8-873e-294d7be06ed4 | http://foo.com                                            | eloquent.updated: ScreenShooter\Models\ScreenshooterJob |
| bc4de4e1-b90a-401f-8643-0f5fce4ff00b | http://foo.com                                            | foo                                                     |
| mock-1-webhook                       | https://approve-v2.dev:443/callbacks/screenshot_jobs      | eloquent.updated: ScreenShooter\Models\ScreenshooterJob |
| mock-2-webhook                       | https://approve-v2.dev:443/callbacks/screenshot_jobs_test | eloquent.updated: ScreenShooter\Models\ScreenshooterJob |
+--------------------------------------+-----------------------------------------------------------+---------------------------------------------------------+
~~~

To see all you can delete

~~~
php artisan core-app:webhook-delete foo-uuid
~~~

To delete that uuid

## How it works

During an event it will look for listeners in the db and if it finds them it will send the results to that callback.

This is **Cached** so it only hits the db once UNTIL someone adds a new webhook to listen to.

The callbacks are done asynchronously so the delay should not be long.

## Adding more events to listen to

Right now this package only listens to `public $listening = ['eloquent.*'];` which then searches the Webhooks table for
event callbacks.

You can make your own WebhooksWrapper class and extend the WebhooksServiceProvider and add more events.
Then register your Provider over the above and they will be added as well.

For example this extends the main provider to listen to other events

~~~
<?php

namespace App\Providers;

use AlfredNutileInc\CoreApp\Webhooks\WebhooksServiceProvider;

class WebhookExtendedProvider extends WebhooksServiceProvider
{

    public $listening = [
        'eloquent.*',
        '\\App\\Events\\CampaignMadeActive',
        '\\App\\Events\\MetaMasterUpdated'
    ];
}

~~~

## Return values

  * body will contain the model or event object json_encoded and serialized, the url, and the environment

# Notes

Move over test with this before going solo on this package
