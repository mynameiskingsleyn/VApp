<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Vindiscount extends Model
{
	protected $fillable = ['dealer_code', 'discount_id', 'vin'] ;
    public $timestamps = false;
}
