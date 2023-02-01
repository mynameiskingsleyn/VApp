<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dlrmgdiscountfinance extends Model
{ 
   protected $fillable = ['discount_id','finance_option'] ;  
   public $timestamps = false; 
}