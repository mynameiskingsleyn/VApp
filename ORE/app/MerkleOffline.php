<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MerkleOffline extends Model
{
	protected $table = 'fca_ore_merkleoffline';
  
    public $timestamps = true;
	
	/* protected function getDateFormat()
	{
		return 'U';
	} */
}
