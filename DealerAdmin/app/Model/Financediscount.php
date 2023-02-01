<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Financediscount extends Model
{
   protected $fillable = ['discount_id','finance_option'] ;  
   public $timestamps = false;
}
