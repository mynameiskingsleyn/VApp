<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
	protected $fillable = ['dealer_code', 'discount_name', 'flat_rate','percent_offer', 'start_date', 'end_date', 'discount_saved', 'uuid', 'bulk_flag', 'rule_flag', 'status'] ;
   public $timestamps = false;
}
