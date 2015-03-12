<?php
use AlfredNutileInc\CoreApp\Webhooks\Webhook;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class WebhooksSeeder extends Seeder
{

    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function run()
    {
        $this->createHooks();
    }

    protected function createHooks()
    {
//        Webhook::create([
//            'id'            => 'mock-1-webhook',
//            'callback_url'  => env('WEBHOOK_SEED_CALLBACK_URL', 'http://foo.com'),
//            'event'         => 'eloquent.created: ScreenShooter\Models\ScreenshooterJob'
//        ]);
//
//        Webhook::create([
//            'id'            => 'mock-2-webhook',
//            'callback_url'  => env('WEBHOOK_SEED_CALLBACK_URL_2', 'http://foo.com'),
//            'event'         => 'eloquent.created: ScreenShooter\Models\ScreenshooterJob'
//        ]);
    }


}