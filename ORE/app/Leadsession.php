<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leadsession extends Model
{ 
	protected $guarded = [];
	/**
     * Get the session id for the staging table.
     */
    public function stages()
    {
        //return $this->hasMany('App\Stage');
    }
}
