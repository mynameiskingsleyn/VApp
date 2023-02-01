<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Incentivebonus extends Model
{ 
	protected $table = 'fca_ore_incentives_bonus_cash';

     protected $dates = ['expire_date'];

    //  protected function setExpiresOnAttribute($value)
    // { 
    //      if($value < \Carbon\Carbon::now()->setTimeFromTimeString("23:59"))
    //        return true;
    //      else
    //       return false;  
    // }
	 
} 