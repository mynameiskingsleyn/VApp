<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Vinactivation extends Model
{
    protected $fillable = ['dealer_code', 'discount_id', 'vin'] ;
    public $timestamps = false;
}
