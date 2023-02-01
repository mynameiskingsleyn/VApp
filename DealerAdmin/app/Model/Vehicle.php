<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
   protected $table = 'fca_ore_input'; 
   public $timestamps = false;
   
     public function scopeOfDealer($query, $dealer_code)
    {
       if(!empty($dealer_code)) return $query->where('dealer_code',$dealer_code);
       else return $query;
     }

     public function scopeOfDriveType($query, $drive_type)
    {
       if(!empty($drive_type)) return $query->where('drive_type',$drive_type);
       else return $query;
     }
}