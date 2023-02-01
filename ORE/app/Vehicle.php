<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
	/*
		packages\fcaore\databucket\src\SqlQueries.php
		packages\fcaore\databucket\src\Http\cronController.php		
	*/
  // protected $table = 'fca_ore_input_18feb';
    protected $table = 'fca_ore_input';
    public $timestamps = false;
}
