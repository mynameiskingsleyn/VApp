<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
	protected $guarded = [];
	
   /**
     * Get the session that owns the stage.
     */
    public function lead()
    {
      //  return $this->belongsTo('App\Leadsession');
    }
}
