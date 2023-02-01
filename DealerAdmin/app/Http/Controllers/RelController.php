<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Discount;

use App\Model\Vindiscount;

use App\Model\Financediscount;

use App\Model\Vinactivation;

class RelController extends Controller
{
   	private $modelDiscount,$modelVindiscount,$modelFinancediscount,$modelVinactivation;
	
	public function __construct(Discount $modelDiscount, Vindiscount $modelVindiscount, Financediscount $modelFinancediscount, Vinactivation $modelVinactivation){
		$this->modelVinactivation = $modelVinactivation;
		$this->modelDiscount = $modelDiscount;
		$this->modelVindiscount = $modelVindiscount;
		$this->modelFinancediscount = $modelFinancediscount;
	}

    public function index()
    {

    }
}
