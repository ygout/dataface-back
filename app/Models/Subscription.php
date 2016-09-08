<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
	
	public $timestamps = false;

    public function users() {
        return $this->belongsToMany('App\User', 'user_subscriptions');
    }

}
