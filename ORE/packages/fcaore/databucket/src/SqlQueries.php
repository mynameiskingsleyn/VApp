<?php namespace Fcaore\Databucket;

use Illuminate\Support\Facades\Redis;
use DB;
use App\Dealer;
use App\Vehicle;
use App\Catvehicle;
use App\Mdoca;
use App\Dlrmgdiscount;
use App\Dlrmgdiscountfinance;
use App\Discountfiltergroup;
use App\PaymentMethodAllocation;
use App\Incentivebonus;
use Illuminate\Support\Arr;
use App\Mdocaalternatedealers;

trait SqlQueries
{
	private $tableNameCategories 	= 'fca_ore_categories';
	private $tableNameSubcategories = 'fca_ore_subcategories';
	private $tableNameCatvehicle 	= 'fca_ore_category_vehicle';
	//private $tableNameInput 		= 'fca_ore_input_18feb';
	private $tableNameInput 		= 'fca_ore_input';
	private $tableNameModel 		= 'fca_ore_model';
	private $tableNameModelView 	= 'fca_modal_view';
	private $tableNameDealer 		= 'fca_ore_dealer_info';
	private $tableNameDealerEliminate = 'fca_ore_dealer_eliminate';
	private $tableNameStages 		= 'stages';
	private $tableNameLeadsessions  = 'leadsessions';
	private $tableNameLeads         = 'leads';
	private $tableNameOptions        = 'fca_ore_options';

	private $brand_arrays ;
	private $brand_arrays_string;

	private $ExecuteSettings 		= true;
	private $year_restrict 			= 2020;

	private $from_year_search ;

	public function from_year_search(){
		return config('ore.etl.starting_year')-1;
	}
	public function brand_arrays(){
		return config('ore.etl.make');
	}
	public function brand_arrays_string(){
		return config('ore.etl.make_strings');
	}

	public function ExecuteSettings(){
		if($this->ExecuteSettings){
			ini_set('max_execution_time', 0);
			set_time_limit(0);
			ini_set('memory_limit', -1);
		}
	}
	/**
	 * SNI - Database Information with Category, Sub_Category and Category_vehicle tables
	 */
	public function sniSummaryQuery(){
		Databucket::ExecuteSettings();

		return DB::Select('SELECT DISTINCT oc.ref_id AS vehicle_type,oc.year,oc.cat_id,oc.title AS cat_title,os.subcat_id,os.title AS sub_title,os.ref_id,
			 ocv.cat_veh_id,ocv.options,ocv.vin FROM '.$this->tableNameCategories.' AS oc 
			 LEFT JOIN '.$this->tableNameSubcategories.' AS os ON oc.cat_id=os.cat_id 
			 LEFT JOIN '.$this->tableNameCatvehicle.' AS ocv ON oc.cat_id=ocv.cat_id AND os.subcat_id=ocv.subcat_id
			 WHERE oc.year > '.$this->from_year_search().' and oc.title IN ('.$this->brand_arrays_string().')');
	}

	/**
	 * Vehicle table segregate from summary Query
	 */
public function sniSegregateSummaryQuery_kings($params_vechType, $params_year, $params_subcatid="n/a"){
        Databucket::ExecuteSettings();

        $sniquery = "";
        try{
            if(strtolower($params_vechType) == 'new'){

                $sniquery = 'SELECT DISTINCT oc.ref_id AS vehicle_type,oc.year,oc.cat_id,oc.title AS cat_title,os.subcat_id,os.title AS sub_title,os.ref_id,
				ocv.cat_veh_id,ocv.options,ocv.vin FROM '.$this->tableNameCategories.' AS oc 
				LEFT JOIN '.$this->tableNameSubcategories.' AS os ON oc.cat_id=os.cat_id 
				LEFT JOIN '.$this->tableNameCatvehicle.' AS ocv ON oc.cat_id=ocv.cat_id AND os.subcat_id=ocv.subcat_id 
				WHERE oc.ref_id="'.$params_vechType.'" AND oc.`year` = '.$params_year.' AND (os.subcat_id='.$params_subcatid.' OR os.ref_id='.$params_subcatid.')';
			 }else{

              /*   $yAppend  = '';

                foreach($params_subcatid as $k=>$v){

                    if($k == 0)
                        $yAppend .= ' AND (os.subcat_id='.$v.' OR os.ref_id='.$v.')';
                    else
                        $yAppend .= ' OR (os.subcat_id='.$v.' OR os.ref_id='.$v.')';
                }

                $sniquery = 'SELECT DISTINCT oc.ref_id AS vehicle_type,oc.year,oc.cat_id,oc.title AS cat_title,os.subcat_id,os.title AS sub_title,os.ref_id,
                    ocv.cat_veh_id,ocv.options,ocv.vin FROM '.$this->tableNameCategories.' AS oc
                    LEFT JOIN '.$this->tableNameSubcategories.' AS os ON oc.cat_id=os.cat_id
                    LEFT JOIN '.$this->tableNameCatvehicle.' AS ocv ON oc.cat_id=ocv.cat_id AND os.subcat_id=ocv.subcat_id
                    WHERE oc.ref_id="'.$params_vechType.'" AND oc.`year` <= '.$params_year.' '.$yAppend; */

            }

            return	DB::select($sniquery);
        }catch(\Exception $e){
            \Log::Error($e->getMessage());
            return false;
        }
    }

	public function maxPriceCustom($vehicle_type, $make, $model, $year){
		$model = $this->splReg($model);
		
		  $query = 'select max(msrp) as max, min(msrp) as min from '.$this->tableNameInput.' where vehicle_type = "'.$vehicle_type.'" and make = "'.$make.'" and model = "'.$model.'" and year = "'.$year.'" ';
			return	DB::select($query);

	}
	public function maxPriceCustomByDealers($vehicle_type, $make, $model, $year,$dealers){
		$model = $this->splReg($model);
		if(count($dealers) ==0){
			$dlr_code = '00000';
		}else{
				$dlr_code = implode(',', $dealers);
		}
		
		  $query = 'select max(msrp) as max, min(msrp) as min from '.$this->tableNameInput.' where vehicle_type = "'.$vehicle_type.'" and make = "'.$make.'" and model = "'.$model.'" and year = "'.$year.'" and dealer_code in ("'.$dlr_code.'") ';
			return	DB::select($query);

	}

   public function sniSegregateSummaryQuery($params_vechType, $params_year, $params_subcatid){
	Databucket::ExecuteSettings();

	if(strtolower($params_vechType) == 'new'){

		/* $sniquery = 'SELECT DISTINCT oc.ref_id AS vehicle_type,oc.year,oc.cat_id,oc.title AS cat_title,os.subcat_id,os.title AS sub_title,os.ref_id,
				ocv.cat_veh_id,ocv.options,ocv.vin FROM '.$this->tableNameCategories.' AS oc
				LEFT JOIN '.$this->tableNameSubcategories.' AS os ON oc.cat_id=os.cat_id
				LEFT JOIN '.$this->tableNameCatvehicle.' AS ocv ON oc.cat_id=ocv.cat_id AND os.subcat_id=ocv.subcat_id
				WHERE oc.ref_id="'.$params_vechType.'" AND oc.`year` = '.$params_year.' AND (os.subcat_id='.$params_subcatid.' OR os.ref_id='.$params_subcatid.')
				'; */

		//return DB::select("call sniSegregateSummaryQuery($params_year, $params_subcatid);");
		return	DB::select('SELECT DISTINCT oc.ref_id AS vehicle_type,oc.year,oc.cat_id,oc.title AS cat_title,os.subcat_id,os.title AS sub_title,os.ref_id,
				ocv.cat_veh_id,ocv.options,ocv.vin FROM '.$this->tableNameCategories.' AS oc 
				LEFT JOIN '.$this->tableNameSubcategories.' AS os ON oc.cat_id=os.cat_id 
				LEFT JOIN '.$this->tableNameCatvehicle.' AS ocv ON oc.cat_id=ocv.cat_id AND os.subcat_id=ocv.subcat_id 
				WHERE oc.ref_id="'.$params_vechType.'" AND oc.`year` = '.$params_year.' AND (os.subcat_id='.$params_subcatid.' OR os.ref_id='.$params_subcatid.')
				');
	}else{

		/*
		CPO SNIPPET
		$yAppend  = '';

		foreach($params_subcatid as $k=>$v){

			if($k == 0)
				$yAppend .= ' AND (os.subcat_id='.$v.' OR os.ref_id='.$v.')';
			else
				$yAppend .= ' OR (os.subcat_id='.$v.' OR os.ref_id='.$v.')';
		}

		 $cpo_used = 'SELECT DISTINCT oc.ref_id AS vehicle_type,oc.year,oc.cat_id,oc.title AS cat_title,os.subcat_id,os.title AS sub_title,os.ref_id,
				ocv.cat_veh_id,ocv.options,ocv.vin FROM '.$this->tableNameCategories.' AS oc
				LEFT JOIN '.$this->tableNameSubcategories.' AS os ON oc.cat_id=os.cat_id
				LEFT JOIN '.$this->tableNameCatvehicle.' AS ocv ON oc.cat_id=ocv.cat_id AND os.subcat_id=ocv.subcat_id
				WHERE oc.ref_id="'.$params_vechType.'" AND oc.`year` <= '.$params_year.' '.$yAppend;


		return	DB::select($cpo_used);  */
	}
   }

   /**
	* SNI - Landing Page Query for New Vehicles
	*
    */
	public function package_options($vehicle_type, $makeName){
		try{
			if($makeName == 'alfa_romeo') $make = 'ALFA ROMEO';
			$package_and_options = Vehicle::select('vin','option_desc')
										->where('vehicle_type','=',$vehicle_type)
										->where('make','=',$makeName)
										->where('year','>=',$this->year_restrict)
										->where('vin','!=','')
										->where('trim_code','!=','')
										->get();
			}catch(\Exception $ex){
				dd($ex->getMessage());
			}
			return $package_and_options;

	}

	public function optionCodes(){
		try{
			 $row = DB::table($this->tableNameOptions)->get();
			 $codes = json_decode(collect($row), true);

			 foreach($codes as $key => $value){
				   if(!Databucket::hexists($value['options_cd'], 'options_desc')){
					 	Databucket::isCacheHMSet($value['options_cd'], 'options_desc',$value['options_desc']);

				   }

			 }

			}catch(\Exception $ex){
				dd($ex->getMessage());
			}
			return true;

	}
	public function isDealerDelete($dealerCode){
			$row = DB::table($this->tableNameDealerEliminate)->where(['dlr_code' => $dealerCode, 'status' => 0])->count();

			 if($row<=0) return "proceed"; else return "block";

	}



	public function queryLandingModalView(){
		DB::table($this->tableNameModelView)->truncate(); 
		DB::insert("insert into fca_modal_view(year,brand,model_desc,number,trim_code,upper_level_pkg_cd,exterior_color_code,ratio,msprice,towing_capacity_count)
    SELECT YEAR,make,model_desc,COUNT(*) AS num,max(trim_code) as trim_code, max(upper_level_pkg_cd) AS upper_level_pkg_cd, max(exterior_color_code) AS exterior_color_code,concat(max(city_mpg),'/',max(hwy_mpg)),MIN(msrp_price) as msrp_price,MAX(towing_capacity_count) AS towing_capacity_count  FROM (
    SELECT year,make,model_desc,trim_desc,MIN(baseVehicleMsrp) AS msrp_price, MAX(city_mpg) AS city_mpg, MAX(hwy_mpg) AS hwy_mpg, MAX(towing_capacity) AS towing_capacity_count,max(trim_code) as trim_code, max(upper_level_pkg_cd) AS upper_level_pkg_cd,  max(exterior_color_code) AS exterior_color_code
    FROM fca_ore_input t1 INNER JOIN fca_ore_model t2 ON t1.model_desc=t2.modelDesc  AND t1.year=t2.modelYearDescription
    WHERE make IN ('RAM','DODGE','FIAT','JEEP','CHRYSLER') AND vehicle_type='New' AND YEAR >=2019 GROUP BY 1,2,3,4) a1 GROUP BY 1,2,3 HAVING COUNT(*) >0");
		return true;
	}

   /**
	* SNI - Landing Page Query for New Vehicles
	*
    */
	 /**
	* SNI - Landing Page Query for New Vehicles
	*
    */
   public function sniLandingQuery($params_vechType, $makeName, $zipcode='00000', $miles=150){
	Databucket::ExecuteSettings();
	//	$array_walk = Databucket::MilesByDealers($zipcode, $miles);
	//	$KeyDealers = array_column($array_walk,'dlr_dba_name','dlr_code');
	//	$allDealers = array_keys($KeyDealers);

	   try{
				$query =  DB::table($this->tableNameModelView)
								->where('brand','=',$makeName)
								->where('model_desc', '!=', "All-New Ram 1500")
								->select('year','model_desc as models','number as cnt','upper_level_pkg_cd','trim_code','exterior_color_code','ratio as city_mpg','towing_capacity_count as towing_capacity_count','msprice as msrp_price','msprice as maxs_msrp')
								->orderBy('models', 'ASC') ->orderBy('year', 'ASC') 
								->get();

				 

				// $query =  DB::table($this->tableNameCategories)
				// ->leftjoin($this->tableNameSubcategories, $this->tableNameCategories.'.cat_id', '=', $this->tableNameSubcategories.'.cat_id')
				// ->join($this->tableNameInput, function($join) use ($params_vechType, $makeName, $allDealers){
				// 		$join->on($this->tableNameInput.'.model', '=', $this->tableNameSubcategories.'.title')
				// 		->where($this->tableNameInput.'.vehicle_type','=',$params_vechType)
				// 		->where($this->tableNameInput.'.trim_code','<>','')
				// 		->where($this->tableNameInput.'.msrp','>',0)
				// 		->where($this->tableNameInput.'.vin','<>','')
				// 		->whereIn($this->tableNameInput.'.dealer_code',$allDealers)
				// 		->where($this->tableNameInput.'.year','<=',$this->year_restrict)
				// 		->whereRaw($this->tableNameInput.'.year = '.$this->tableNameCategories.'.year')
				// 		->where($this->tableNameInput.'.make','=',$makeName);
				// })
				// ->join($this->tableNameModel, function($join) use ($makeName){
				// 		$join->on($this->tableNameInput.'.model', '=', $this->tableNameModel.'.modelDesc')
				// 		->whereRaw($this->tableNameInput.'.year = '.$this->tableNameModel.'.modelYearDescription')
				// 		->where($this->tableNameModel.'.franchiseDescription','=',$makeName);
				// })
				// ->select($this->tableNameCategories.'.cat_id',$this->tableNameInput.'.interior_fabric',$this->tableNameInput.'.exterior_color_code', $this->tableNameCategories.'.title',$this->tableNameCategories.'.year',$this->tableNameCategories.'.ref_id AS vehicle_type', $this->tableNameSubcategories.'.subcat_id',$this->tableNameSubcategories.'.title as models',$this->tableNameInput.'.trim_code', $this->tableNameInput.'.upper_level_pkg_cd AS upper_level_pkg_cd',$this->tableNameInput.'.body_style as body_style',  DB::raw('(SELECT COUNT(options) FROM '.$this->tableNameCatvehicle.' WHERE '.$this->tableNameCatvehicle.'.cat_id='.$this->tableNameCategories.'.cat_id AND '.$this->tableNameCatvehicle.'.subcat_id='.$this->tableNameSubcategories.'.subcat_id) AS cnt'),DB::raw('MIN('.$this->tableNameModel.'.baseVehicleMsrp) AS msrp_price'), DB::raw('MAX('.$this->tableNameInput.'.msrp) AS maxs_msrp'), DB::raw('MAX('.$this->tableNameInput.'.city_mpg) AS city_mpg'),DB::raw('MAX('.$this->tableNameInput.'.hwy_mpg) AS hwy_mpg'),DB::raw('MAX('.$this->tableNameInput.'.towing_capacity) AS towing_capacity_count') )
				// ->where($this->tableNameCategories.'.ref_id','=',$params_vechType)
				// ->where($this->tableNameCategories.'.title','=',$makeName)
				// ->groupBy($this->tableNameSubcategories.'.title', $this->tableNameCategories.'.year')
				// ->orderBy($this->tableNameInput.'.model', 'asc')
				// ->get();
	   }catch(\Exception $ex){
				\Log::error($ex->getMessage());
	   }
	   return $query;
   }
    /**
	* SNI - Getall NEW int<Cat id> and int<Subcat id> BY string{brand} string{model} int{year}
	*
    */
   public function sniCatSubCatQuery($params_vechType, $vehicle_brands=null, $year=null, $model=null){
	Databucket::ExecuteSettings();

		if($vehicle_brands==null)  $vehicle_brands = $this->brand_arrays();
		 if($model!=null)  $model = Databucket::customIBModel($model);
		$model = $this->splReg($model);
		//Databucket::resolveModel($model);

	   try{


			if($vehicle_brands==null || $model==null || $year==null){
				//$vehicle_brands = array('jeep','chrysler','ram','dodge','fiat','alfa romeo');


				$query =  DB::table($this->tableNameCategories)
				->leftjoin($this->tableNameSubcategories, $this->tableNameSubcategories.'.cat_id', '=', $this->tableNameCategories.'.cat_id')
				->select($this->tableNameCategories.'.title as brandName', $this->tableNameSubcategories.'.title as modelName', $this->tableNameCategories.'.year as year', $this->tableNameSubcategories.'.subcat_id', $this->tableNameSubcategories.'.cat_id')
				->where($this->tableNameCategories.'.ref_id','=',$params_vechType)
				->whereIn($this->tableNameCategories.'.title',$vehicle_brands)
				->where($this->tableNameCategories.'.year','>',2017)
				->orderBy($this->tableNameCategories.'.title')
				->get();
			}else{
				$query =  DB::table($this->tableNameCategories)
				->leftjoin($this->tableNameSubcategories, $this->tableNameSubcategories.'.cat_id', '=', $this->tableNameCategories.'.cat_id')
				->select($this->tableNameCategories.'.title as brandName', $this->tableNameSubcategories.'.title as modelName', $this->tableNameCategories.'.year as year', $this->tableNameSubcategories.'.subcat_id', $this->tableNameSubcategories.'.cat_id')
				->where($this->tableNameCategories.'.ref_id','=',$params_vechType)
				->where($this->tableNameCategories.'.title','=',$vehicle_brands)
				->where($this->tableNameSubcategories.'.title','=', $model)
				->where($this->tableNameCategories.'.year','<=',$year)
				->orderBy($this->tableNameCategories.'.title')
				->get();
			}


	   }catch(\Exception $ex){
				dd($ex->getMessage());
	   }
	   return $query;
   }


   /***
   *  SNI12 - Max-Min Price and Max-Min year
   */
   public function CpoUsedMaxs( $params_vechType,$makeName, $model, $year, $dealerCode){
  /*  if($makeName == 'alfa_romeo') $makeName = 'ALFA ROMEO';
   $model = str_replace("_"," ",$model);
   $model = str_replace("-"," ",$model);

   $query =  DB::table($this->tableNameInput)
						->select(DB::raw('MAX(internetPrice) as maxs_msrp'), DB::raw('MIN(internetPrice) as msrp_price'), DB::raw('MAX(year) as max_year'), DB::raw('MIN(year) as min_year'))
						->where($this->tableNameInput.'.vehicle_type','=',$params_vechType)
						->where($this->tableNameInput.'.make','=',$makeName)
						->where($this->tableNameInput.'.internetPrice','>',0)
						->where($this->tableNameInput.'.year','<=', $year)
						->where($this->tableNameInput.'.model','>',$model)
						->where($this->tableNameInput.'.trim_desc','<>','')
						->whereIn($this->tableNameInput.'.dealer_code',$dealerCode)
				->get();
				 */
		$query='';
		return $query;
   }
   /**
	 * Landing Page: Cpo Query
	 */
	public function CpoQuery( $params_vechType,$makeName, $dealerCode){
		Databucket::ExecuteSettings();
		/* try{

				$query =  DB::table($this->tableNameInput)
						->select($this->tableNameInput.'.model as models', $this->tableNameInput.'.year', 'body_style as body_style','thumbnail','interior_fabric', 'exterior_color_code','trim_code','upper_level_pkg_cd as upper_level_pkg_cd', 'vehicle_type', DB::raw('COUNT(DISTINCT vin) as cnt'), DB::raw('MAX(internetPrice) as maxs_msrp'), DB::raw('MIN(internetPrice) as msrp_price'), DB::raw('MAX(city_mpg) as city_mpg'), DB::raw('MAX(hwy_mpg) as hwy_mpg'), DB::raw('MAX(towing_capacity) as towing_capacity_count'), DB::raw('MAX(year) as max_year'), DB::raw('MIN(year) as min_year'))
						->where($this->tableNameInput.'.vehicle_type','=',$params_vechType)
						->where($this->tableNameInput.'.make','=',$makeName)
						->where($this->tableNameInput.'.internetPrice','>',0)
						->where($this->tableNameInput.'.year','>',2014)
						->where($this->tableNameInput.'.trim_desc','<>','')
						->whereIn($this->tableNameInput.'.dealer_code',$dealerCode)
						->groupBy($this->tableNameInput.'.model')
				->get();

			return $query;
			exit;
		}catch(\Exception $ex){
				dd($ex->getMessage());
		}    */
		$query='';return $query;
	}


	/*
	Getting make + type + year + model wise vehicle
	*/
	public function makeTypeYearModel($type, $make){
		 if($make == 'alfa_romeo') $make = 'ALFA ROMEO';

		$result = Vehicle::select('year','model')
					->where('vehicle_type',$type)
					->where('make',$make)
					->where('year','>',$this->from_year_search())
					->groupBy('year','model')
					->get();


		return $result;

		//alternate query
		//SELECT DISTINCT `year`,model FROM ore_input WHERE make='jeep' AND vehicle_type='new'
	}


	/*
	Getting make + type + year + model wise dealers
	*/
	public function DealerTypeYearModel($type, $make, $year, $model){
		try{
			 if($make == 'alfa_romeo') $make = 'ALFA ROMEO';
			if($model!='') $model = Databucket::customIBModel($model);

				$query = DB::table($this->tableNameDealer)
				->leftjoin($this->tableNameInput, $this->tableNameInput.'.dealer_code', '=', $this->tableNameDealer.'.dlr_code')
				->select($this->tableNameDealer.'.dlr_code', $this->tableNameDealer.'.dlr_dba_name')
				->where($this->tableNameInput.'.vehicle_type','=',$type)
				->where($this->tableNameInput.'.make','=',$make)
				->where($this->tableNameInput.'.year','=',$year)
				->where($this->tableNameInput.'.model','=',$model)
				->groupBy($this->tableNameDealer.'.dlr_code')
				->get();
		}catch(\Exception $ex){
				dd($ex->getMessage());
		}
		return $query;

	}



   /**
	 * SNI - Filters created based on MAKE and VEHICLE TYPES.
	 */
	public function MasterVehicleRelation($type, $make, $year, $model){
		Databucket::ExecuteSettings();
		$newarray = array();
		$limit_init = config('databucket.limit_size');
		$isSetVinList = config('databucket.isSetVinList');
		$have_chunk = config('databucket.have_chunk');
		//$model = str_replace("-"," ", $model);
		//$model = str_replace("_"," ", $model);

		if($model!='') $model = Databucket::customIBModel($model);
		if($make!='') $makeName = Databucket::customIBModel($make);

		if($model == 'all new ram 1500') $model = "all-new ram 1500";
  try{
		$result = DB::table($this->tableNameInput)
		->select(
					$this->tableNameInput.'.vin',
					//$this->tableNameInput.'.vehtype',
					$this->tableNameInput.'.vehicle_type',
					$this->tableNameInput.'.year',
					$this->tableNameInput.'.make',
					$this->tableNameInput.'.model',

					$this->tableNameInput.'.trim_desc',
					$this->tableNameInput.'.trim_code',
					//$this->tableNameInput.'.interior_fabric',

					$this->tableNameInput.'.exterior_color_desc',
					$this->tableNameInput.'.exterior_color_code',
					$this->tableNameInput.'.drive_type',
					$this->tableNameInput.'.towing_capacity',
					$this->tableNameInput.'.transmission_desc',
					$this->tableNameInput.'.transmission_type',

					$this->tableNameInput.'.city_mpg',
					$this->tableNameInput.'.hwy_mpg',
					$this->tableNameInput.'.msrp',
					$this->tableNameInput.'.internetPrice',
					//$this->tableNameInput.'.option_desc_raw',
					//$this->tableNameInput.'.option_code',
					$this->tableNameInput.'.dealer_code',

					$this->tableNameInput.'.engine_horse_power',
					$this->tableNameInput.'.eng_desc',
					//$this->tableNameInput.'.engine_displacement',
					$this->tableNameInput.'.seating_capacity',
					$this->tableNameInput.'.upper_level_pkg_cd',
					$this->tableNameInput.'.body_style as body_style',

					//$this->tableNameInput.'.photo_URL',

					$this->tableNameInput.'.wheel_base'
			)
		->where($this->tableNameInput.'.vehicle_type',$type)
		->where($this->tableNameInput.'.year',$year)
		->where($this->tableNameInput.'.make', $makeName)
		->where($this->tableNameInput.'.model',$model);

		if(strtolower($type) == 'cpo' || strtolower($type) == 'used'){
			$result = $result->where($this->tableNameInput.'.internetPrice','>',0)
							->where($this->tableNameInput.'.trim_desc','<>','')
							->groupBy($this->tableNameInput.'.vin');
		}else{
			$result = $result->where($this->tableNameInput.'.msrp','>',0)
							 ->where($this->tableNameInput.'.trim_code','<>','')
							 ->groupBy($this->tableNameInput.'.vin');
		}
		if($have_chunk){
				$tcount = $result->count();

//				for($i=0; $i<$tcount; $i+=$limit_init){
//					$newarray[] =  $result->offset($i)->limit($limit_init)->get();
//				}
            $newarray = $result->get()->chunk($limit_init,function ($data){
                $dataCollect = [];
                foreach($data as $chunk){
                    $dataCollect = array_merge($dataCollect,$chunk);
                }
                return $dataCollect;
            });
            $newarray = $this->mergeCollections($newarray);

		}else{
				$newarray[] = $result->distinct($this->tableNameInput.'.vin')->get();
		}

	// Is set vin list => Start
	if($isSetVinList){
		foreach($newarray as $key=>$val){
			$str= '';
			foreach($val as $key1=>$val1){
				$vin = $val1->vin;

				if(!Databucket::hexists($vin, 'vin')){

					$option_desc_raw = $val1->option_desc_raw;
					$option_code = $val1->option_code;

					$arr_option_desc_raw = explode("|", $option_desc_raw);
					$arr_option_code = explode("|", $option_code);
					if(count($arr_option_code) == count($arr_option_desc_raw)){
						$array_codes=array_combine($arr_option_code,$arr_option_desc_raw);
					}else{
						$array_codes = [];
					}

					if (array_key_exists($val1->exterior_color_code,$array_codes)){
						Databucket::isCacheHMSet($vin, 'ext_color_raw',$array_codes[$val1->exterior_color_code]);
					}else{
						Databucket::isCacheHMSet($vin, 'ext_color_raw','');
					}


					Databucket::isCacheHMSet($vin, 'vin',$vin);
					Databucket::isCacheHMSet($vin, 'vehType',$val1->vehtype); // N - New, U-Used, C-CPO
					Databucket::isCacheHMSet($vin, 'vehicle_type',$val1->vehicle_type); // N - New, U-Used, C-CPO
					Databucket::isCacheHMSet($vin, 'year',$val1->year);
					Databucket::isCacheHMSet($vin, 'make',$val1->make);
					Databucket::isCacheHMSet($vin, 'model',$val1->model);

					Databucket::isCacheHMSet($vin, 'trim_desc',$val1->trim_desc);
					Databucket::isCacheHMSet($vin, 'trim_code',$val1->trim_code);
					Databucket::isCacheHMSet($vin, 'interior_fabric',$val1->interior_fabric);


					Databucket::isCacheHMSet($vin, 'exterior_color_desc',$val1->exterior_color_desc);
					Databucket::isCacheHMSet($vin, 'exterior_color_code',$val1->exterior_color_code);
					Databucket::isCacheHMSet($vin, 'drive_type',$val1->drive_type);
					Databucket::isCacheHMSet($vin, 'towing_capacity',$val1->towing_capacity);
					Databucket::isCacheHMSet($vin, 'transmission_desc',$val1->transmission_desc);
					Databucket::isCacheHMSet($vin, 'transmission_type',$val1->transmission_type);

					Databucket::isCacheHMSet($vin, 'city_mpg',$val1->city_mpg);
					Databucket::isCacheHMSet($vin, 'hwy_mpg',$val1->hwy_mpg);
					Databucket::isCacheHMSet($vin, 'internetPrice',$val1->internetPrice);
					Databucket::isCacheHMSet($vin, 'msrp',$val1->msrp);
					Databucket::isCacheHMSet($vin, 'photo_URL','');
					Databucket::isCacheHMSet($vin, 'dealer_code',$val1->dealer_code);

					Databucket::isCacheHMSet($vin, 'engine_horse_power',$val1->engine_horse_power);
					Databucket::isCacheHMSet($vin, 'eng_desc',$val1->eng_desc);
					Databucket::isCacheHMSet($vin, 'wheel_base',$val1->wheel_base);
					Databucket::isCacheHMSet($vin, 'engine_displacement',$val1->engine_displacement);
					Databucket::isCacheHMSet($vin, 'seating_capacity',$val1->seating_capacity);

					Databucket::isCacheHMSet($vin, 'upper_level_pkg_cd',$val1->upper_level_pkg_cd);
					Databucket::isCacheHMSet($vin, 'body_style',$val1->body_style);
				}
			}


		}
	}
	 }catch(\Exception $ex){
                \Log::error('Master Vehicle function  error!!');
				\Log::error($ex->getMessage());
        }
        //dd($newarray);
		return $newarray;
	}





	public function vinSet($make, $vehicle_type){
	Databucket::ExecuteSettings();
		$newarray = array();

			if($make == 'alfa_romeo') $makeName = 'ALFA ROMEO';  else  $makeName = $make;
		try{
			$result = DB::table($this->tableNameInput)
					->select(
					$this->tableNameInput.'.vin',
					$this->tableNameInput.'.vehtype',
					$this->tableNameInput.'.vehicle_type',
					$this->tableNameInput.'.year',
					$this->tableNameInput.'.make',
					$this->tableNameInput.'.model',

					$this->tableNameInput.'.trim_desc',
					$this->tableNameInput.'.trim_code',

					$this->tableNameInput.'.interior_fabric',


					$this->tableNameInput.'.stock_number',
					$this->tableNameInput.'.interior_meta_color_desc',
					$this->tableNameInput.'.doors',

					$this->tableNameInput.'.exterior_color_desc',
					$this->tableNameInput.'.exterior_color_code',
					$this->tableNameInput.'.drive_type',
					$this->tableNameInput.'.towing_capacity',
					$this->tableNameInput.'.transmission_desc',
					$this->tableNameInput.'.transmission_type',

					$this->tableNameInput.'.city_mpg',
					$this->tableNameInput.'.hwy_mpg',
					$this->tableNameInput.'.msrp',
					$this->tableNameInput.'.internetPrice',
					$this->tableNameInput.'.option_desc_raw',
					$this->tableNameInput.'.option_code',
					$this->tableNameInput.'.dealer_code',

					$this->tableNameInput.'.engine_horse_power',
					$this->tableNameInput.'.eng_desc',
					$this->tableNameInput.'.engine_displacement',
					$this->tableNameInput.'.seating_capacity',
					$this->tableNameInput.'.upper_level_pkg_cd',
					$this->tableNameInput.'.body_style as body_style',

					//$this->tableNameInput.'.photo_URL',

					$this->tableNameInput.'.wheel_base'
			)
		->where($this->tableNameInput.'.vehicle_type',$vehicle_type)
		->where($this->tableNameInput.'.year','>',$this->from_year_search())
		->where($this->tableNameInput.'.make', $makeName);

	//	if(strtolower($type) == 'cpo' || strtolower($type) == 'used'){
			//$result = $result->where($this->tableNameInput.'.internetPrice','>',0)
							//->where($this->tableNameInput.'.trim_desc','<>','');
		//}else{
			$result = $result->where($this->tableNameInput.'.msrp','>',0)->where($this->tableNameInput.'.trim_code','<>','');
		//}

		$newarray[] = $result->groupBy($this->tableNameInput.'.vin')->get();

		foreach($newarray as $key=>$val){
			$str= '';
			foreach($val as $key1=>$val1){
				$vin = $val1->vin;

				if(!Databucket::hexists($vin, 'vin')){

					$option_desc_raw = $val1->option_desc_raw;
					$option_code = $val1->option_code;

					$arr_option_desc_raw = explode("|", $option_desc_raw);
					$arr_option_code = explode("|", $option_code);
					if(count($arr_option_code) == count($arr_option_desc_raw)){
						$array_codes=array_combine($arr_option_code,$arr_option_desc_raw);
					}else{
						$array_codes = [];
					}

					if (array_key_exists($val1->exterior_color_code,$array_codes)){
						Databucket::isCacheHMSet($vin, 'ext_color_raw',$array_codes[$val1->exterior_color_code]);
					}else{
						Databucket::isCacheHMSet($vin, 'ext_color_raw','');
					}


					Databucket::isCacheHMSet($vin, 'vin',$vin);
					Databucket::isCacheHMSet($vin, 'vehType',$val1->vehtype); // N - New, U-Used, C-CPO
					Databucket::isCacheHMSet($vin, 'vehicle_type',$val1->vehicle_type); // N - New, U-Used, C-CPO
					Databucket::isCacheHMSet($vin, 'year',$val1->year);
					Databucket::isCacheHMSet($vin, 'make',$val1->make);
					Databucket::isCacheHMSet($vin, 'model',$val1->model);

					Databucket::isCacheHMSet($vin, 'trim_desc',$val1->trim_desc);
					Databucket::isCacheHMSet($vin, 'trim_code',$val1->trim_code);

					Databucket::isCacheHMSet($vin, 'stock_number',$val1->stock_number);
					Databucket::isCacheHMSet($vin, 'interior_meta_color_desc',$val1->interior_meta_color_desc);
					Databucket::isCacheHMSet($vin, 'doors',$val1->doors);

					Databucket::isCacheHMSet($vin, 'option_desc_raw',$option_desc_raw);
					Databucket::isCacheHMSet($vin, 'option_code',$option_code);

					Databucket::isCacheHMSet($vin, 'exterior_color_desc',$val1->exterior_color_desc);
					Databucket::isCacheHMSet($vin, 'exterior_color_code',$val1->exterior_color_code);
					Databucket::isCacheHMSet($vin, 'drive_type',$val1->drive_type);
					Databucket::isCacheHMSet($vin, 'towing_capacity',$val1->towing_capacity);
					Databucket::isCacheHMSet($vin, 'transmission_desc',$val1->transmission_desc);
					Databucket::isCacheHMSet($vin, 'transmission_type',$val1->transmission_type);
					Databucket::isCacheHMSet($vin, 'interior_fabric',$val1->interior_fabric);
					Databucket::isCacheHMSet($vin, 'city_mpg',$val1->city_mpg);
					Databucket::isCacheHMSet($vin, 'hwy_mpg',$val1->hwy_mpg);
					Databucket::isCacheHMSet($vin, 'internetPrice',$val1->internetPrice);
					Databucket::isCacheHMSet($vin, 'msrp',$val1->msrp);
					Databucket::isCacheHMSet($vin, 'photo_URL','');
					Databucket::isCacheHMSet($vin, 'dealer_code',$val1->dealer_code);

					Databucket::isCacheHMSet($vin, 'engine_horse_power',$val1->engine_horse_power);
					Databucket::isCacheHMSet($vin, 'eng_desc',$val1->eng_desc);
					Databucket::isCacheHMSet($vin, 'wheel_base',$val1->wheel_base);
					Databucket::isCacheHMSet($vin, 'engine_displacement',$val1->engine_displacement);
					Databucket::isCacheHMSet($vin, 'seating_capacity',$val1->seating_capacity);

					Databucket::isCacheHMSet($vin, 'upper_level_pkg_cd',$val1->upper_level_pkg_cd);
					Databucket::isCacheHMSet($vin, 'body_style',$val1->body_style);
				}
			}


		}
	 }catch(\Exception $ex){
				\Log::info($ex->getMessage());
		}


	}


	 /**
	 * SNI - Filters created based on MAKE and VEHICLE TYPES.
	 */
	public function PackageAndOptionChunk($type, $make, $year, $model){
		Databucket::ExecuteSettings();
		$newarray = array();
		$limit_init = config('databucket.limit_size');
		$have_chunk = config('databucket.have_chunk');
		if($make == 'alfa_romeo') $make = 'ALFA ROMEO';
		if($model!='') $model = Databucket::customIBModel($model);

		$result = Vehicle::select('vin','option_desc_raw')
										->where('vehicle_type','=',$type)
										->where('year','=',$year)
										->where('make','=',$make)
										->where('model','=',$model)
										->distinct($this->tableNameInput.'.vin');

		if(strtolower($type) == 'cpo' || strtolower($type) == 'used'){
					$result = $result->where($this->tableNameInput.'.internetPrice','>',0)
							->where($this->tableNameInput.'.trim_desc','<>','');
		}else{
			 $result->where($this->tableNameInput.'.internetPrice','>',0) ->where($this->tableNameInput.'.trim_code','<>','');
		}

				$tcount = $result->count();
				for($i=0; $i<$tcount; $i+=$limit_init){
					$newarray[] =  $result->offset($i)->limit($limit_init)->get();
				}

		return $newarray;
	}
	/***
	Miles By Dealers
	*/
	public function MilesByDealers($zipcode=00000, $radius=25){

				$latlan_zipcode = Databucket::makeCache('cord:zip:latlan:'.$zipcode);
				  if(!Databucket::isCacheExists($latlan_zipcode)){
					 Databucket::LatLongZipcode($zipcode);
					 $latlan_zipcode = Databucket::isCacheGet('cord:zip:latlan:'.$zipcode);
					 $getCordinates = explode("@",$latlan_zipcode);
				  }else{
					 $latlan_zipcode = Databucket::isCacheGet('cord:zip:latlan:'.$zipcode);
					 $getCordinates = explode("@",$latlan_zipcode);
				  }


		if(Arr::has($getCordinates, '0')){
		$lat=$getCordinates[0];
		$lon=$getCordinates[1];
	}else $lat = $lon = 0;

		$lat= (empty($lat)) ? 0 : $lat;
		$lon= (empty($lon)) ? 0 : $lon;

		return DB::select("SELECT distinct(dlr.dlr_code),dlr_dba_name, SQRT(POWER(69.1 * (dlr.dlr_shw_lat - ".$lat."), 2) + POWER(69.1 * (".$lon." - dlr.dlr_shw_long) * COS(dlr.dlr_shw_lat / 57.3), 2)) AS `distance_in_miles`
							FROM `fca_ore_dealer_info` AS dlr 
							HAVING (`distance_in_miles` <= ".$radius." AND  `distance_in_miles` > 0)
							ORDER BY `distance_in_miles` ASC;");
	}

	
	/**
	 * Miles Calculation By Query
	 */
	public function MilesByquery($lat=42.90140, $lon=-70.80580, $zipcode=65775, $radius=25,$make, $model, $year, $vehicle_type){
		Databucket::ExecuteSettings();


		$make = str_replace("_"," ", $make); $make = str_replace("-"," ", $make);
		if($model!='') $model = Databucket::customIBModel($model);

		if($model == 'all new ram 1500') $model = "all-new ram 1500";

		Databucket::resolveModel($model);

		if(strtolower( $vehicle_type) == 'new') { 
		$lat= (empty($lat)) ? 0 : $lat;
		$lon= (empty($lon)) ? 0 : $lon;
			
		 return DB::select("SELECT distinct(dlr.dlr_code),dlr_dba_name, SQRT(POWER(69.1 * (dlr.dlr_shw_lat - ".$lat."), 2) + POWER(69.1 * (".$lon." - dlr.dlr_shw_long) * COS(dlr.dlr_shw_lat / 57.3), 2)) AS `distance_in_miles`
							FROM `fca_ore_dealer_info` AS dlr, `".$this->tableNameInput."` AS inp
							WHERE dlr.dlr_code NOT IN (SELECT dlr_code FROM fca_ore_dealer_eliminate where status=0) AND dlr.dlr_code = inp.dealer_code 
									AND inp.make='".$make."'
									AND inp.model='".$model."'
									AND inp.year=".$year."		 
									AND inp.vehicle_type='".$vehicle_type."' 
							HAVING (`distance_in_miles` <= ".$radius." AND  `distance_in_miles` > 0)
							ORDER BY `distance_in_miles` ASC");
		}else{

			//  return DB::select("SELECT distinct(dlr.dlr_code),dlr_dba_name, SQRT(POWER(69.1 * (dlr.dlr_shw_lat - ".$lat."), 2) + POWER(69.1 * (".$lon." - dlr.dlr_shw_long) * COS(dlr.dlr_shw_lat / 57.3), 2)) AS `distance_in_miles`
			// 				FROM `fca_ore_dealer_info` AS dlr, `".$this->tableNameInput."` AS inp
			// 				WHERE dlr.dlr_code NOT IN (SELECT dlr_code FROM fca_ore_dealer_eliminate where status=0) AND
			// 				dlr.dlr_code = inp.dealer_code 
			// 						AND inp.make='".$make."'
			// 						AND inp.model='".$model."'
			// 						AND inp.year<=".$year."		 
			// 						AND inp.vehicle_type='".$vehicle_type."' 
			// 				HAVING (`distance_in_miles` <= ".$radius." AND  `distance_in_miles` > 0)
			// 				ORDER BY `distance_in_miles` ASC;");
		}
	}


 	/**
	* Dealer find with specific RADIUS using LAT and LONG
	*
    */
   public function sniFindDealer($lat, $lng, $radius){
		Databucket::ExecuteSettings();
		$dealerArray = Databucket::radius($lat, $lng, $radius);
		try{
			$dealerArray = Dealer::distance($lat, $lng, $radius)->get()->toJson();
		}catch(\Exception $ex){
				dd($ex->getMessage());
		}
		return $dealerArray;
	}

	public function singleVehicle(){

		return Vehicle::select('vin','dealer_code')
										->where('vehicle_type', '=', 'new')
										->where('dealer_code', '!=' ,'')
										->where('vin', '!=' ,'')
										->get();

	}

public function vendor_experience_query($ref){
			Databucket::ExecuteSettings();
			$currentSession = $ref;

			return DB::select("select b.session_id,b.lead_id,b.source_id,b.lead_source,
	(select first_name from ".$this->tableNameStages." where first_name is not null and first_name<>'' and session_id = a.id order by id desc LIMIT 1) first_name, 

	(select last_name from ".$this->tableNameStages." where last_name is not null and last_name<>'' and session_id = a.id order by id desc LIMIT 1) last_name,

	(select email from ".$this->tableNameStages." where email is not null and email<>'' and session_id = a.id order by id desc LIMIT 1) email,

	(select phone from ".$this->tableNameStages." where phone is not null and phone<>'' and session_id = a.id order by id desc LIMIT 1) phone,

	(select streetline1 from ".$this->tableNameStages." where streetline1 is not null and streetline1<>'' and session_id = a.id order by id desc LIMIT 1) streetline1,

	(select streetline2 from ".$this->tableNameStages." where streetline2 is not null and streetline2<>'' and session_id = a.id order by id desc LIMIT 1) streetline2,

	(select apartment from ".$this->tableNameStages." where apartment is not null and apartment<>'' and session_id = a.id order by id desc LIMIT 1) apartment,

	(select city from ".$this->tableNameStages." where city is not null and city<>'' and session_id = a.id order by id desc LIMIT 1) city,

	(select state from ".$this->tableNameStages." where state is not null and state<>'' and session_id = a.id order by id desc LIMIT 1) state,

	(select zip from ".$this->tableNameStages." where zip is not null and zip<>'' and session_id = a.id order by id desc LIMIT 1) zip,

	(select comments from ".$this->tableNameStages." where comments is not null and comments<>'' and session_id = a.id order by id desc LIMIT 1) comments,
	
	(select dealer_code from ".$this->tableNameStages." where dealer_code is not null and dealer_code<>'' and session_id = a.id order by id desc LIMIT 1) dealer_code,
	
	(select dealer_name from ".$this->tableNameStages." where dealer_name is not null and dealer_name<>'' and session_id = a.id order by id desc LIMIT 1) dealer_name,

	(select vehicle_year from ".$this->tableNameStages." where vehicle_year is not null and vehicle_year<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_year,

	(select vehicle_make from ".$this->tableNameStages." where vehicle_make is not null and vehicle_make<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_make,

	(select vehicle_model from ".$this->tableNameStages." where vehicle_model is not null and vehicle_model<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_model,

	(select vehicle_vin from ".$this->tableNameStages." where vehicle_vin is not null and vehicle_vin<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_vin,

	(select vehicle_trim from ".$this->tableNameStages." where vehicle_trim is not null and vehicle_trim<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_trim,

	(select vehicle_stock from ".$this->tableNameStages." where vehicle_stock is not null and vehicle_stock<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_stock,

	(select vehicle_doors from ".$this->tableNameStages." where vehicle_doors is not null and vehicle_doors<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_doors,

	(select vehicle_bodystyle from ".$this->tableNameStages." where vehicle_bodystyle is not null and vehicle_bodystyle<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_bodystyle,

	(select vehicle_transmission from ".$this->tableNameStages." where vehicle_transmission is not null and vehicle_transmission<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_transmission,

	(select vehicle_interiorcolor from ".$this->tableNameStages." where vehicle_interiorcolor is not null and vehicle_interiorcolor<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_interiorcolor,

	(select vehicle_exteriorcolor from ".$this->tableNameStages." where vehicle_exteriorcolor is not null and vehicle_exteriorcolor<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_exteriorcolor,

	(select vehicle_preference from ".$this->tableNameStages." where vehicle_preference is not null and vehicle_preference<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_preference,

	(select vehicle_imagetag from ".$this->tableNameStages." where vehicle_imagetag is not null and vehicle_imagetag<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_imagetag,

	(select vehicle_price from ".$this->tableNameStages." where vehicle_price is not null and vehicle_price<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_price,

	(select vehicle_price_comments from ".$this->tableNameStages." where vehicle_price_comments is not null and vehicle_price_comments<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_price_comments,

	(select vehicle_optionname from ".$this->tableNameStages." where vehicle_optionname is not null and vehicle_optionname<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_optionname,

	(select vehicle_manufacturercode from ".$this->tableNameStages." where vehicle_manufacturercode is not null and vehicle_manufacturercode<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_manufacturercode,

	(select vehicle_weighting from ".$this->tableNameStages." where vehicle_weighting is not null and vehicle_weighting<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_weighting,

	(select vehicle_option_method from ".$this->tableNameStages." where vehicle_option_method is not null and vehicle_option_method<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_option_method,

	(select vehicle_option_down_payment from ".$this->tableNameStages." where vehicle_option_down_payment is not null and vehicle_option_down_payment<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_option_down_payment,

	(select vehicle_option_monthly_payment from ".$this->tableNameStages." where vehicle_option_monthly_payment is not null and vehicle_option_monthly_payment<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_option_monthly_payment,

	(select vehicle_option_total_payment from ".$this->tableNameStages." where vehicle_option_total_payment is not null and vehicle_option_total_payment<>'' and session_id = a.id order by id desc LIMIT 1) vehicle_option_total_payment,

	(select tradein_year from ".$this->tableNameStages." where tradein_year is not null and tradein_year<>'' and session_id = a.id order by id desc LIMIT 1) tradein_year,

	(select tradein_make from ".$this->tableNameStages." where tradein_make is not null and tradein_make<>'' and session_id = a.id order by id desc LIMIT 1) tradein_make,

	(select tradein_model from ".$this->tableNameStages." where tradein_model is not null and tradein_model<>'' and session_id = a.id order by id desc LIMIT 1) tradein_model,

	(select tradein_vin from ".$this->tableNameStages." where tradein_vin is not null and tradein_vin<>'' and session_id = a.id order by id desc LIMIT 1) tradein_vin,

	(select tradein_units from ".$this->tableNameStages." where tradein_units is not null and tradein_units<>'' and session_id = a.id order by id desc LIMIT 1) tradein_units,

	(select tradein_unit_value from ".$this->tableNameStages." where tradein_unit_value is not null and tradein_unit_value<>'' and session_id = a.id order by id desc LIMIT 1) tradein_unit_value,

	(select finance_balance from ".$this->tableNameStages." where finance_balance is not null and finance_balance<>'' and session_id = a.id order by id desc LIMIT 1) finance_balance,

	(select type_of_mode from ".$this->tableNameStages." where type_of_mode is not null and type_of_mode<>'' and session_id = a.id order by id desc LIMIT 1) type_of_mode,

	(select tradein_zip from ".$this->tableNameStages." where tradein_zip is not null and tradein_zip<>'' and session_id = a.id order by id desc LIMIT 1) tradein_zip,

	(select tradein_vehicle_condition from ".$this->tableNameStages." where tradein_vehicle_condition is not null and tradein_vehicle_condition<>'' and session_id = a.id order by id desc LIMIT 1) tradein_vehicle_condition,

	(select estimated_owed from ".$this->tableNameStages." where estimated_owed is not null and estimated_owed<>'' and session_id = a.id order by id desc LIMIT 1) estimated_owed,

	(select tradein_value from ".$this->tableNameStages." where tradein_value is not null and tradein_value<>'' and session_id = a.id order by id desc LIMIT 1) tradein_value,

	(select timeframe_desc from ".$this->tableNameStages." where timeframe_desc is not null and timeframe_desc<>'' and session_id = a.id order by id desc LIMIT 1) timeframe_desc,

	(select timeframe_earliestdate from ".$this->tableNameStages." where timeframe_earliestdate is not null and timeframe_earliestdate<>'' and session_id = a.id order by id desc LIMIT 1) timeframe_earliestdate,

	(select timeframe_latestdate from ".$this->tableNameStages." where timeframe_latestdate is not null and timeframe_latestdate<>'' and session_id = a.id order by id desc LIMIT 1) timeframe_latestdate,

	(select service_protection from ".$this->tableNameStages." where service_protection is not null and service_protection<>'' and session_id = a.id order by id desc LIMIT 1) service_protection,

	(select inbound_id from ".$this->tableNameStages." where inbound_id is not null and inbound_id<>'' and session_id = a.id order by id desc LIMIT 1) inbound_id,

	(select additional_details from ".$this->tableNameStages." where additional_details is not null and additional_details<>'' and session_id = a.id order by id desc LIMIT 1) additional_details,

	(select lead_status from ".$this->tableNameStages." where lead_status is not null and lead_status<>'' and session_id = a.id order by id desc LIMIT 1) lead_status,

	(select created_at from ".$this->tableNameStages." where created_at is not null and created_at<>'' and session_id = a.id order by id desc LIMIT 1) created_at,

	(select updated_at from ".$this->tableNameStages." where updated_at is not null and updated_at<>'' and session_id = a.id order by id desc LIMIT 1) updated_at

	from ".$this->tableNameStages." b inner join ".$this->tableNameLeadsessions." a on a.id=b.session_id where b.session_id='".$currentSession."' and a.flag<>8 group by b.session_id order by b.id asc");
	}

	public function oreSession_flagupdate_query($currentSession){
			Databucket::ExecuteSettings();
			  DB::select("UPDATE ".$this->tableNameLeadsessions." AS a
					INNER JOIN ".$this->tableNameLeads." AS b ON a.ore_session = b.session_id
					SET a.flag = 8 where a.ore_session='".$currentSession."'");
					return true;
		}


	public function routeone_randnumber_query(){
	$currentSession = \Ore::getSessionID();
			Databucket::ExecuteSettings();
			return DB::select("SELECT * FROM ".$this->tableNameStages." AS s 
			INNER JOIN ".$this->tableNameLeadsessions." AS l ON s.session_id = l.id AND s.source_id=2 where ore_session='".$currentSession."'");
	}
	public function flagupdate_query(){
			$currentSession = \Ore::getSessionID();
			Databucket::ExecuteSettings();
			return DB::select("update ".$this->tableNameLeadsessions." set flag=flag + 1 where ore_session='".$currentSession."'");
	}
	public function autopopulation_query(){
	       $currentSession = \Ore::getSessionID();
			Databucket::ExecuteSettings();
			return DB::select("SELECT * FROM ".$this->tableNameStages." a INNER JOIN ".$this->tableNameLeadsessions." b ON a.session_id = b.id where b.ore_session='".$currentSession."' order by a.id asc limit 1");
	}

	public function mdoca_query(){
		Databucket::ExecuteSettings();
		return Mdoca::get();
	}

	/*
	* Autoload Query - whomever submitted inital form and close the browser/system, those data sends lead
	*/
	public function autolead_query(){

			Databucket::ExecuteSettings();
			$interval_time = config('ore.lead.autolead_interval_time');
			$auto_lead_query = "SELECT ore_session, updated_at
								FROM leadsessions
								WHERE id NOT IN(
								SELECT session_id
								FROM stages) 
								AND updated_at <= NOW() - INTERVAL ".$interval_time." MINUTE
								AND DATE(updated_at) = CURDATE()";

			return DB::select($auto_lead_query);
	}

	public function SqlFiltergroups($vin_info, $financeoption){

		$level4 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'], 'make' => $vin_info['make'], 'model_year' => $vin_info['year'],'model' => $vin_info['model'],'trim' => $vin_info['trim_desc'] ]);

		$count_level4 = $level4->count();

		if($count_level4 == 0){

				$level3 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'], 'make' => $vin_info['make'], 'model_year' => $vin_info['year'],'model' => $vin_info['model']  ]);
				$count_level3 = $level3->count();

				if($count_level3 == 0){

				$level2 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'],'make' => $vin_info['make'], 'model_year' => $vin_info['year']]);

					$count_level2 = $level2->count();

					if($count_level2 == 0){

							$level1 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'], 'make' => $vin_info['make'] ]);
							$count_level1 = $level1->count();

							if($count_level1 == 0){ $output = []; } else {$output = $level1->get(); }

					}else $output = $level2->get();

				} else $output = $level3->get();
		}else $output = $level4->get();

		####Include Vins

		if(count($output) == 0){
			return $output;
		}else{
			return json_decode($output, true);
		}



	}

	public function SqlDlrmgdiscount($VinNumber, $transcation){
		$now = date('Y-m-d');

		if($transcation == 'lease') $financeoption =1; else $financeoption=2;

		//$discounts = \App\Dlrmgdiscountfinance::where('finance_option',$financeoption)->select('discount_id')->get();



		  /*  $list_discount  = \DB::table('dlrmgdiscounts as d')
							->join('dlrmgdiscountfinances as f','f.discount_id','=','d.id')
							->where('d.vin',$VinNumber)
							->where('d.start_date', '<=', \DB::raw('now()'))
							->where('d.end_date', '>=',  \DB::raw('now()'))
							->where('f.finance_option','=', $financeoption)
							->select('d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','percent_offer',
								\DB::raw('DATE_FORMAT(d.start_date,"%m/%d/%Y") AS discount_start_date'),\DB::raw('DATE_FORMAT(d.end_date,"%m/%d/%Y") AS discount_end_date'),'d.discount_saved as saved_discount')
								->get(); */

				 $list_discount  =  \DB::table('discounts as d')
									 ->join('vindiscounts as v','v.discount_id','=','d.id')
									 ->join('financediscounts as f','f.discount_id','=','d.id')
									 ->where(['v.vin' => $VinNumber,'f.finance_option' => $financeoption])
									 ->where('d.start_date', '<=', $now)->where('d.end_date', '>=', $now)
									 ->select('d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','d.percent_offer','d.start_date as discount_start_date','d.end_date as discount_end_date','d.discount_saved as saved_discount','f.finance_option as payment_mode')
									 ->get();

			 return json_decode($list_discount, true);

				/* return  \App\Dlrmgdiscount::where('vin',$VinNumber)
									->where('start_date', '<=', $now)
									->where('end_date', '>=', $now)
									->whereIn('id',$discount_ids)
									->select('id as discount_id','uuid','discount_name as name_of_discount','flat_rate','percent_offer','start_date as discount_start_date','end_date as discount_end_date','discount_saved as saved_discount')
									->get();  */

	}


	public function SqlVinActivate($dealercode, $vin=null){
		 if($dealercode == 'empty'){
				$allvins1 = \App\Vinactivation::select(DB::raw("GROUP_CONCAT(DISTINCT CONCAT(vins)) AS `allvins`"));
				if($allvins1->count() > 0){
					$allvins2 = $allvins1->first()->toArray();
				 }else{ return 	$allvins = []; }

				if(Arr::has($allvins2, 'allvins'))	$allvins = explode(',',$allvins2['allvins']); else 	$allvins = [];
				return $allvins;
		 }else{
			 $allvins1 = \App\Vinactivation::select('vins AS allvins')->where(['dealer_code' => $dealercode]);

			if($allvins1->count() > 0){
				$allvins2 =  $allvins1->first()->toArray();
			 }else{
				 return 'active';
			 }


			if(Arr::has($allvins2, 'allvins')){
				$pos = strpos($allvins2['allvins'],$vin);
				if($pos === false )	return 'active';	else return "deactive";
			}else{
				return 'active';
			}

		 }
	}


	/*
	* Incentive Bonus Cash
	* CalcualtorController
	*/
	public function sqlIncentivesBonusCash($make,$dealer_code){
		Databucket::ExecuteSettings(); 
		
		$list_incentive = \DB::table('fca_ore_incentives_bonus_cash AS ibc')
					->where(['ibc.dealer_code' => $dealer_code])->where('ibc.expire_date', '>=',\DB::raw('CURDATE()'))
					->orWhere(['ibc.dealer_code' => intval($dealer_code)])
					->where('ibc.expire_date', '>=',\DB::raw('CURDATE()'))
					->select('ibc.dealer_code','ibc.vin','ibc.incentive_label','ibc.discount_amount','ibc.expire_date','ibc.program_id','ibc.is_lease','ibc.is_finance','ibc.is_cash')
					->get();

		return $list_incentive;
   		 //return \App\Incentivebonus::where('expire_date','>=',\DB::raw('CURDATE()'))->select('dealer_code','vin','incentive_label','discount_amount','expire_date','program_id','is_lease','is_finance','is_cash')->get();
	}


	/*
	* Payment Method Allocation
	* CalcualtorController
	* Ally or CCAP
	*/
	public function sqlPaymentMethodAllocation(){
		return \App\PaymentMethodAllocation::all();
	}

	/*
	* Get Dealer's zipcode
	* CalcualtorController
	* $dealer_code
	*/
	public function sqlZipcodeAllDealers(){
		return \App\Clist::select('dlr_code','dlr_shw_zip')->get();
	}

	/*
	* Get mdoca_alternate_dealers
	* CalcualtorController
	* $dealer_code
	*/
	public function sqlMdocaAlternateDealers(){
		return \App\Mdocaalternatedealers::all();
	}

	/*
	* Empty Inventory for specific dealer.
	*/
	public function DealerInventoryMonitoring(){

		return [];
	}

	/*
	* GET Current audit row of IB File
	*/
	public function IBFileAuditMonitoring(){
		//$audit_list  =  \DB::table('fca_ore_audit')->where('varchar_col_1', '=', 'IB')->where('date_sid', '>=',\DB::raw('SUBDATE(CURDATE(),1)'))->orderBy('date_sid','desc')->first();
		$audit_list  =  \DB::table('fca_ore_audit')->whereIn('varchar_col_1', array('IB','Dealer'))->where('date_sid', '>=',\DB::raw('CURDATE()'))->select('date_sid','varchar_col_2','varchar_col_1','integer_col_2','integer_col_3','integer_col_4','integer_col_5','integer_col_6','integer_col_7')->orderBy('date_sid','desc')->get();
		return json_decode(json_encode($audit_list),true);
	}





/***************************   CPO / USED **********************************************/

/**
	 * Miles Calculation By Query
	 */
	public function MilesByCPODealer($lat=42.90140, $lon=-70.80580, $zipcode=65775, $radius=25,$make, $vehicle_type){
		Databucket::ExecuteSettings();
		$make = str_replace("_"," ", $make);
		$make = str_replace("-"," ", $make);
 

		$lat= (empty($lat)) ? 0 : $lat;
		$lon= (empty($lon)) ? 0 : $lon;
		$query = "SELECT distinct(dlr.dlr_code),dlr_dba_name, SQRT(POWER(69.1 * (dlr.dlr_shw_lat - ".$lat."), 2) + POWER(69.1 * (".$lon." - dlr.dlr_shw_long) * COS(dlr.dlr_shw_lat / 57.3), 2)) AS `distance_in_miles`
FROM `fca_ore_dealer_info` AS dlr, `".$this->tableNameInput."` AS inp WHERE dlr.dlr_code NOT IN (SELECT dlr_code FROM fca_ore_dealer_eliminate where status=0) AND inp.make='".$make."' AND inp.vehicle_type='".$vehicle_type."' AND inp.trim_desc <> ''		 
HAVING (`distance_in_miles` <= ".$radius." AND  `distance_in_miles` > 0) ORDER BY `distance_in_miles` ASC";


		return DB::select($query);
	}

	/**
	 * Right Side Dealer Count for MAx Price and Year Calcualtion
	 */
	public function SQLRightUsedCpoDealers($zipcode=65775, $radius=25,$make, $vehicle_type, $model, $year){
		Databucket::ExecuteSettings();
		$make = str_replace("_"," ", $make);
		$make = str_replace("-"," ", $make);
		$latlan_zipcode = Databucket::makeCache('cord:zip:latlan:'.$zipcode);
				  if(!Databucket::isCacheExists($latlan_zipcode)){
					 Databucket::LatLongZipcode($zipcode);
					 $latlan_zipcode = Databucket::isCacheGet('cord:zip:latlan:'.$zipcode);
					 $getCordinates = explode("@",$latlan_zipcode);
				  }else{
					 $latlan_zipcode = Databucket::isCacheGet('cord:zip:latlan:'.$zipcode);
					 $getCordinates = explode("@",$latlan_zipcode);
				  }

	if(Arr::has($getCordinates, '0')){
		$lat=$getCordinates[0];
		$lon=$getCordinates[1];
	}else $lat = $lon = 0;

		$lat= (empty($lat)) ? 0 : $lat;
		$lon= (empty($lon)) ? 0 : $lon;

		$query = "SELECT distinct(dlr.dlr_code),dlr_dba_name, SQRT(POWER(69.1 * (dlr.dlr_shw_lat - ".$lat."), 2) + POWER(69.1 * (".$lon." - dlr.dlr_shw_long) * COS(dlr.dlr_shw_lat / 57.3), 2)) AS `distance_in_miles`
FROM `fca_ore_dealer_info` AS dlr, `".$this->tableNameInput."` AS inp WHERE dlr.dlr_code NOT IN (SELECT dlr_code FROM fca_ore_dealer_eliminate where status=0) AND dlr.dlr_code = inp.dealer_code AND inp.make='".$make."' AND inp.model='".$model."' AND inp.vehicle_type='".$vehicle_type."' AND inp.trim_desc <> '' and inp.internetPrice > 0 		 
HAVING (`distance_in_miles` <= ".$radius." AND  `distance_in_miles` > 0) ORDER BY `distance_in_miles` ASC";
$alldealercodes = DB::select($query);
$resultArray = json_decode(json_encode($alldealercodes), true);
		return $resultArray;
	}


	public function SQLRightUsedCpoDealersPrices($zipcode=65775, $radius=25,$make, $vehicle_type, $model, $year){
		Databucket::ExecuteSettings();
		$make = str_replace("_"," ", $make);
		$make = str_replace("-"," ", $make);
		$latlan_zipcode = Databucket::makeCache('cord:zip:latlan:'.$zipcode);
				  if(!Databucket::isCacheExists($latlan_zipcode)){
					 Databucket::LatLongZipcode($zipcode);
					 $latlan_zipcode = Databucket::isCacheGet('cord:zip:latlan:'.$zipcode);
					 $getCordinates = explode("@",$latlan_zipcode);
				  }else{
					 $latlan_zipcode = Databucket::isCacheGet('cord:zip:latlan:'.$zipcode);
					 $getCordinates = explode("@",$latlan_zipcode);
				  }

		

		
if(Arr::has($getCordinates, '0')){
		$lat=$getCordinates[0];
		$lon=$getCordinates[1];
}else{
	$lat = 0; $lon = 0; 

}
		$query = "SELECT distinct(dlr.dlr_code),dlr_dba_name, SQRT(POWER(69.1 * (dlr.dlr_shw_lat - ".$lat."), 2) + POWER(69.1 * (".$lon." - dlr.dlr_shw_long) * COS(dlr.dlr_shw_lat / 57.3), 2)) AS `distance_in_miles`
FROM `fca_ore_dealer_info` AS dlr, `".$this->tableNameInput."` AS inp WHERE  dlr.dlr_code NOT IN (SELECT dlr_code FROM fca_ore_dealer_eliminate where status=0) AND  dlr.dlr_code = inp.dealer_code AND inp.make='".$make."' AND inp.model='".$model."' AND inp.year<='".$year."'  AND inp.vehicle_type='".$vehicle_type."' AND inp.trim_desc <> '' and inp.internetPrice > 0 		 
HAVING (`distance_in_miles` <= ".$radius." AND  `distance_in_miles` > 0) ORDER BY `distance_in_miles` ASC";
$alldealercodes = DB::select($query);
$resultArray = json_decode(json_encode($alldealercodes), true);
		return $resultArray;
	}


	/**
	 * Right Side Dealer Count for MAx Price and Year Calcualtion
	 */
	public function SQLRightUsedCpoMaxMin($dealer,$make, $vehicle_type, $model, $year){
		Databucket::ExecuteSettings();
		$make = str_replace("_"," ", $make);
		$make = str_replace("-"," ", $make);

		$model = $this->splReg($model);

		$query = Vehicle::select(DB::raw('MAX(`year`) as MaxYear'), DB::raw('MIN(`year`) as MinYear'))
										->where($this->tableNameInput.'.vehicle_type','=',$vehicle_type)
										//->where($this->tableNameInput.'.year','=',$year)
										->where($this->tableNameInput.'.make','=',$make)
										->where($this->tableNameInput.'.model','=',$model)
										->where($this->tableNameInput.'.trim_desc','<>','')
										->whereIn($this->tableNameInput.'.dealer_code', $dealer)
										->where($this->tableNameInput.'.internetPrice','>',0)
										->get();
		 return $query;
	}
	public function SQLRightUsedCpoMaxMinPrices($dealer,$make, $vehicle_type, $model, $year){
		Databucket::ExecuteSettings();
		$model = $this->splReg($model);
		$make = str_replace("_"," ", $make);
		$make = str_replace("-"," ", $make);

		$query = Vehicle::select(DB::raw('MAX(`internetPrice`) as MaxPrice'), DB::raw('MIN(`internetPrice`) as MinPrice'))
										->where($this->tableNameInput.'.vehicle_type','=',$vehicle_type)
										->where($this->tableNameInput.'.year','<=',$year)
										->where($this->tableNameInput.'.make','=',$make)
										->where($this->tableNameInput.'.model','=',$model)
										->where($this->tableNameInput.'.trim_desc','<>','')
										->whereIn($this->tableNameInput.'.dealer_code', $dealer)
										->where($this->tableNameInput.'.internetPrice','>',0)
										->get();
		 return $query;
	}

	/* public function MoparPlans(){
		//$qry = Mopar::where()

		\DB::table('fca_ore_mopar_plans')->where('varient', 'plan')->get();
			\Databucket::isCacheSet($serviceprotectionCachkey, json_encode($qr
	}

	public function MoparPlansHeader(){
		$qry = \DB::table('fca_ore_mopar_plans')->where('varient', 'plan')->get();
			\Databucket::isCacheSet($serviceprotectionCachkey, json_encode($qr
	} */


	function mergeCollections($collection)
    {
        $allItems = [];
        $result = [];
        foreach($collection as $collect){
            foreach($collect as $one){
                $allItems[] = $one;
            }
        }
        $allItems = collect($allItems);
        $result[]= $allItems;
        return $result;
    }

    public function getTime(){
	    return \Carbon\Carbon::now();
    }

    public function timeDiff($first, $second)
    {
        return $first->diffInSeconds($second);
    }

	public function splReg($value){
		$value = str_ireplace('®','&reg;',$value);
		$value = str_ireplace('â','',$value);
		
		return $value;
	}

} /* Finally Trait Closed*/

