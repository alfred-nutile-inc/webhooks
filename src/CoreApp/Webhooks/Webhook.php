<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 3/12/15
 * Time: 7:37 AM
 */

namespace AlfredNutileInc\CoreApp\Webhooks;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Cache;

class Webhook extends Eloquent {


    public $fillable = ['id', 'callback_url', 'event'];


    public static function boot()
    {
        parent::boot();

        Webhook::creating(function($results) {
            Cache::forget('webhooks');
        });
    }
}