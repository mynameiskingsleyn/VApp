<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Filterdiscount extends Model
{
	protected $fillable = ['discount_id', 'filtergroup_id'] ;
    public $timestamps = false;
}
