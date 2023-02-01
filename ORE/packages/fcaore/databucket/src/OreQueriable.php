<?php namespace Fcaore\Databucket;
use Fcaore\Databucket\Databucket;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Log;

trait OreQueriable
{
     protected function custom_import($data = null)
    {
		$this->_map = $data;
		$this->_baseContents = $this->_map;
		return true;
	}
     protected function get_kargs_value($data,$key){
		return implode(",",$data[$key]);
	}
    /**
     *
     *
     * @return $this
     * @throws ConditionNotAllowedException
     */
    public function partial_match($vinArray,$exact_match=array()){
		return array_diff(array_unique(array_reduce($vinArray, 'array_merge', array())),$exact_match);
	}

public function resolveModel(&$model)
    {
        $unResolved = config('ore.etl.unResolved_models');
        foreach($unResolved as $key=>$resolve){
            if($model==$key){
                $model = $resolve;
            }
        }
    }

    public function collectionHasKey($item, $collection)
    {
        foreach($collection as $key=>$val){
            if($key == $item) return true;
        }
        return false;
    }

    public function mergeDublicate($grouped,$first='RAM 1500',$second="ALL-NEW RAM 1500",$sortBy='year')
    {
        $arrayCollection = json_decode($grouped, true);
        foreach($arrayCollection as $key=>$values)
        {
            if($key == $second){
                $arrayCollection[$first] = array_merge($arrayCollection[$first],$arrayCollection[$second]);
            }
        }
        unset($arrayCollection[$second]);
        $arrayCollection[$first] = $this->arraySortByVal($arrayCollection[$first],$sortBy);
        return $arrayCollection;
    }

    private function arraySortByVal($array,$sort='year')
    {
        do{
            $swapped = false;
            foreach($array as $key=>$val){
                if(isset($val[$sort])){
                    if(isset($array[$key+1])){

                        $currentGrade = $val[$sort];
                        $nextGrade = $array[$key+1][$sort];
                        if($nextGrade < $currentGrade){
                            //swap values
                            $swapped = true;
                            $temp = $array[$key];
                            $array[$key] = $array[$key+1];
                            $array[$key+1] = $temp;
                        }
                    }
                }
            }
        }while($swapped == true);
        return $array;
    }

    public function exact_match($vinArray){
        if(count($vinArray) == 1){
			return $vinArray[0];
		}
        else{
			//return array_flatten($vinArray);
			return array_unique(array_values(call_user_func_array("array_intersect", $vinArray)));
		}

	}

	private $str;

	public function subcat_where($subcat){
		if(isset($subcat)){
			$this->str ="->from('datarow')->whereIn('subcat_id','".implode(",",$subcat)."')->get()";
			return $this->str;
		}

	}
	public function getVinFromTrait($params_vin, $params_make, $params_vechType,$params_year,$params_model){

		$vehicleParamsCacheKey = Databucket::makeCache('vehicle_table_'.$params_make.$params_vechType.'_'.$params_year.$params_model);
		$dddd = json_decode(Databucket::isCacheGet($vehicleParamsCacheKey), true);
		$data['datarow'] = $dddd;


 		//$data["datarow"] = json_decode(Databucket::isCacheGet('vehicle_table_'.$params_make.$params_vechType),true);
		$Obj_DBucket = new Databucket($data);
		$out = $Obj_DBucket->from('datarow')->where('vin', '=', $params_vin)->get();
		if(count($out)>1){
			foreach($out as $key=>$val) return $val;
		}else{
			return $out;
		}
		dd($out);

	}

	public function subcat_id_condition($subcat_id){
		if(!empty($subcat_id))
			return $this->whereIn('subcat_id',$subcat_id);
		else
			return $this;
	}

	public function cat_veh_id_condition($cat_veh_id){
		if(!empty($cat_veh_id))
			return $this->orWhereIn('cat_veh_id',$cat_veh_id);
		else
			return $this;
	}

    public function get_match_rows($data,$kargs=null){
		$Obj_DBucket = new Databucket($data);
		//filtered trims
        if(is_null($kargs)){
             $all_vin_no=array_column($data['datarow'],'vin');
        }else{
			if(!empty($kargs['cat_veh_id'])){ //when Trim is selected

				$alltrim=$allsubCat=array();
			//	\Log::info(' ===== > Filtered Trims');
			//	\Log::info($kargs['cat_veh_id']);
				$alltrim= $Obj_DBucket->from('datarow')
									  ->whereIn('cat_veh_id',$kargs['cat_veh_id'])
									  ->get();

				$alltrimVins=array_column($alltrim, 'vin');
				$all_vin_no=array(implode("", $alltrimVins));
			 //	\Log::info(' ===== > [ and ] brackers');
			// 	\Log::info($alltrimVins);
				$all_vin_no = str_replace('][', ',', $all_vin_no);
				  $all_vin_no = str_replace('[', '', $all_vin_no);
				  $all_vin_no = str_replace(']', '', $all_vin_no);
			 	//  \Log::info(' ===== > [ and ] brackers after filter');
			 //	 \Log::info($all_vin_no);
				if(!empty($kargs['subcat_id'])){
					$subCat_DBucket = new Databucket($data);
					$allsubCat = $subCat_DBucket->from('datarow')->subcat_id_condition($kargs['subcat_id'])->get();
					$allsubCatarray=array_column($allsubCat, 'vin');
					$all_vin_no =array_merge($all_vin_no,$allsubCatarray);
				}

			}else{ //if only sub cat selected

				$output = $Obj_DBucket->from('datarow')
									->subcat_id_condition($kargs['subcat_id'])
									->get();
				$all_vin_no=array_column($output,'vin');
			}
        }
        $find = ['[',']']; $replace =['',''];
		foreach($all_vin_no as $key => $v){
		   $all_vin_no[$key] = array_map('trim',array_filter(explode(',',str_replace($find,$replace,$v))));
          // $all_vin_no[$key] = explode(',',$group);
		  //	$string = preg_split("/\,/",preg_replace('/\[\d+|\]\[+|\[+|\s+|\]/', '', $v));

		}

		return $all_vin_no;
	}

	 public function SNI_Left_Attributes($data){
			$checkFilter = array("trim", "drive", "color","EngDesc","dealers","Transmission");
			$filters =  array();

			foreach($checkFilter as $key => $val){
					$dropHtml = '<ul>';
					if(in_array($val, array_keys($data))){
						$names =$data[$val];
						foreach($names as $vkey => $vvalue){
							$dropHtml .="<li><label class='customCheckBox'>";
						 	$dropHtml .= "<input type='checkbox' id='".$val."Code[]' name='".$val."Code[]' class='FilterSelectEvent' data-filter-type='".strtolower($vvalue)."'  value='".$vkey."' ";

							if(array_key_exists($val.'_tier1', $data)){
								if($data[$val.'_tier1']!='') {
									if(strtolower($vvalue) == strtolower($data[$val.'_tier1'])){
											$dropHtml .= " checked = 'checked' ";
									}
								}
							}
							$dropHtml .= "/>";
							$dropHtml .= "<span>".ucwords($vvalue)."</span></label></li>";
						}
					}else{
						$dropHtml .="<li>No ".$val." Available.</li>";
					}
					$dropHtml .= '</ul>';

					$filters[$val] = $dropHtml;

			}


			return $filters;

	 }

    public function SNI_inital_results($data,$kargs=null){
		$Obj_DBucket = new Databucket($data);
		$output = $Obj_DBucket->from('datarow')->where('vehicle_type', '=', $kargs['vehicle_type'])->where('year', '=', 2018)
			->where('subcat_id', '=', 196)->orWhere('ref_id', '=', 196)
			->implode('vin');
			$output = str_replace("[","",$output);
			$output = str_replace("]","",$output);

		$all_vins=array_unique(array_map('trim',array_filter(explode(",",$output))));

		return array_slice($all_vins, 0, 10);
	}


	public function getFilterRows($data, $params_vechType, $params_year, $params_catid, $params_subcatid){


			$Obj_DBucket = new Databucket($data);

		$trim = $Obj_DBucket->from('attributes')
							->where('vehicle_type', '=', strtoupper($params_vechType))
							->where('year', '=', $params_year)
							->where('cat_id', '=', $params_catid)
							->where('subcat_id', '=', $params_subcatid)
							->get();
			$output=array();

			$output["trim"]=array_column($trim,'options','cat_veh_id');


			$myarray=['drive','color','EngDesc','Transmission','towing'];
			$Obj_DBucket2 = $Obj_DBucket->copy();

			foreach($myarray as $v){
					$output[$v] = array_column(
									$Obj_DBucket2->reset()->from('attributes')
									->where('vehicle_type', '=', strtoupper($params_vechType))
									->where('year', '=', $params_year)
									->where('cat_title', '=', $v)
									->where('ref_id', '=', $params_subcatid)
									->get(),
									'sub_title',
									'subcat_id');
			}

			return $output;
    }


    public function getFilterRows_cpoused($data, $params_vechType, $params_year, $params_catid, $params_subcatid){
				$Obj_DBucket = new Databucket($data);
				$output=array();
				$output["trim"]=[];
				$myarray=['drive','color','EngDesc','Transmission','towing'];
				 $Obj_DBucket2 = $Obj_DBucket->copy();
				foreach($myarray as $v){
						$output[$v] =  array_filter( array_unique(array_column(
										$Obj_DBucket2->reset()->from('attributes')
										->where('vehicle_type', '=', strtoupper($params_vechType))
										->where('year', '>=', $params_year)
										->where('cat_title', '=', $v)
										->get(),
										'sub_title',
										'subcat_id')), 'strlen' );
				}
			return $output;
    }

	public function get_max_price($data,$vins){

		$Obj_DBucket = new Databucket($data);

		$maxPrice = $Obj_DBucket->from('datarow')
						->whereIn('vin', $vins)
						 ->max('msrp');

		return $maxPrice;

	}

	public function msrp_condition($priceRange, $msrp_verbiage,$matches){

		if($priceRange!=''){
			list($start, $end) = explode(",", $priceRange);

			if($msrp_verbiage == 'internetPrice') {
				if($matches=='p')
						return $this;
				else
					return $this->where('internetPrice','>=',$start-1)->where('internetPrice','<=',$end+1);
			}else {
				if($matches=='p') {
					return $this;
				}else{
                    $result = $this->where('msrp','>=',$start)->where('msrp','<=',$end);
                    return $result;
				}
			}
		}
		else
			return $this;
	}

	public function dealer_condition($dealers){
		if(!empty($dealers))
			return $this->whereIn('dealer_code', $dealers);
		else
			return $this;
	}

	public function towing_condition($towing){
		if($towing>0 && $towing!='')
			return $this->where('towing_capacity','>=',$towing);
		else
			return $this;
	}

	public function sortBy_condition($sortBy_param,$dealers, $msrp_verbiage){
		if($sortBy_param==0)
			return $this->sortByMiles($dealers);
		else if($sortBy_param==1)
			return $this->sortBy($msrp_verbiage,'asc');
		else if($sortBy_param==2)
			return $this->sortBy($msrp_verbiage,'desc');
		else
			return $this;
	}



	public function get_vehicle_details($data,$matched_vins,$dealers,$priceRange,$towing,$secondaySort,$sortBy_param,$params_vechType,$tier,$matches){

		$Obj_DBucket = new Databucket($data);
		$sortedArray=array();

		if(strtolower($params_vechType) == 'new') $msrp_verbiage = "msrp";
		else if(strtolower($params_vechType) == 'cpo') $msrp_verbiage = "internetPrice";
		else if(strtolower($params_vechType) == 'used') $msrp_verbiage = "internetPrice";
		else $msrp_verbiage = "internetPrice"; 
		if($secondaySort=='yes'){ //Seconday Sorting
								 
									if(count($dealers) == 0){ 	
										$exact_or_partial_match = [];
									}else{
										if(strtolower($params_vechType) == 'new'){
													if($tier=='t3'){
														$exact_or_partial_match = $Obj_DBucket->from('datarow')
																	->dealer_condition($dealers)
																	->msrp_condition($priceRange, $msrp_verbiage,$matches)
																	->sortBy_condition($sortBy_param,$dealers, $msrp_verbiage)
																	->get();
													}else{
														$exact_or_partial_match = $Obj_DBucket->from('datarow')
																	->dealer_condition($dealers)
																	//->sortByMiles($dealers)
																	->msrp_condition($priceRange, $msrp_verbiage,$matches)
																	->sortBy_condition($sortBy_param,$dealers, $msrp_verbiage)
																	->get();
													}
											} 
									}		
		}else{ //primary Sorting  
											if($tier=='t3'){

														$exact_or_partial_match = $Obj_DBucket->from('datarow')
																->dealer_condition($dealers)
																->msrp_condition($priceRange, $msrp_verbiage,$matches)
																->sortBy('drive_type', 'asc')
																->sortBy('eng_desc', 'asc')
																->sortBy('trim_desc', 'asc')
																->sortBy($msrp_verbiage,'asc')
																->get();
													}else{
														if(count($dealers) > 0){ 
																$exact_or_partial_match = $Obj_DBucket->from('datarow')
																->dealer_condition($dealers)
																->msrp_condition($priceRange, $msrp_verbiage,$matches)
																->sortBy('drive_type', 'asc')
																->sortBy('eng_desc', 'asc')
																->sortBy($msrp_verbiage,'asc')
																->sortBy('trim_desc', 'asc')
																->sortByMiles($dealers)
																->get();
														}else{
															$exact_or_partial_match = [];
														}
													} 
		}

		//Selecting Vin info only
		$sortedArray['sortedrow']=$exact_or_partial_match;

		$Obj_DBucket2 = new Databucket($sortedArray);
		$result = $Obj_DBucket2->from('sortedrow')
								->whereIn('vin', $matched_vins)
								->get();
		return $result;
	}



	 /**
     * Expand Radius Calculation: if found any dealer then return array at specific loop or else
     * search continue till 150miles.
	 *
	 * Author: SATHISH KUMAR<sathisha@v2soft.com>
	 * DATE  : 10-May-2019
     * @var Illuminate\Http\Request $request
     *
     * @return array
     *
     */
	public function expand_radius($params_zipcode,$params_radius,$params_make, $params_model, $params_year, $params_vechType){
		$getDealers['radius']=$params_radius;
		$getDealers['dealers'] = array();
		 for($i=$params_radius; $i<=150; $i+=25){
				$getDealers['params_zipcode']=$params_zipcode;
				$getDealers['radius']=$i;
				$getDealers['dealers'] = Databucket::getDealersByZipcodeRadius($params_zipcode,$i,$params_make, $params_model, $params_year, $params_vechType);
				if(count($getDealers['dealers']) > 0){ return $getDealers;	break; exit;}
		}

		return $getDealers;
	}

	/**
     *  Get Dealer list two array value of (DealerCode and DealerName) & (DealerCode and MilesAway)
	 *  based on given Zipcode
	 *
	 *	@params:  Zipcode, Radius, Name of the Make, Name of the Model, Year, Vehicle Type [New, CPO]
	 *
	 *  radiusByQuery() -> Fcaore\Databucket\OreQueriable.php
	 *
	 * 	Author: SATHISH KUMAR<sathisha@v2soft.com>
	 * 	DATE  : 30-APR-2019
     * 	@var Illuminate\Http\Request $request
     *
     * 	@return array
     *
     */
	public function getDealersByZipcodeRadius($params_zipcode,$params_radius, $make="jeep", $model="cherokee", $year=2019, $vehicle_type="New", $dealer_specific_cache=array()){

			   return Databucket::radiusByQuery($params_zipcode, $params_radius, $make, $model, $year, $vehicle_type);
			   exit;
	}

	/**
     *  SubFunction: Get Dealer list two array value of (DealerCode and DealerName) & (DealerCode *    and MilesAway)
	 *  based on given Zipcode.
	 *
	 *	@params:  Zipcode, Radius, Name of the Make, Name of the Model, Year, Vehicle Type [New, CPO]
	 *
	 *  getLatLonByZipcode() -> Fcaore\Databucket\GeoLocation.php
	 *	MilesByquery() 		 -> Fcaore\Databucket\SqlQueries.php
	 *
	 * Author: SATHISH KUMAR<sathisha@v2soft.com>
	 * DATE  : 30-APR-2019
     * @var Illuminate\Http\Request $request
     *
     * @return array
     *
     */
	public function radiusByQuery($zipcode, $radius,$make, $model, $year, $vehicle_type){


		$cacheName = 'radiusByQuery:'.$zipcode.':'.$radius.':'.str_replace(" ","_",$make).':'.str_replace(" ","_",$model).':'.$year.':'.strtolower($vehicle_type);
		
		$cacheNameMiles = 'radiusByMiles:'.$zipcode.':'.$radius.':'.str_replace(" ","_",$make).':'.str_replace(" ","_",$model).':'.$year.':'.strtolower($vehicle_type);

		if(Databucket::isCacheExists($cacheName)){
			$array_walk = json_decode(Databucket::isCacheGet($cacheName), true);
		} else{
			$getCordinates = Databucket::getLatLonByZipcode($zipcode);
			$allDealers = Databucket::MilesByquery($getCordinates[1],$getCordinates[2],$zipcode, $radius,$make, $model, $year, $vehicle_type);
			
			$array_walk = array_column($allDealers,'dlr_dba_name','dlr_code');
			$distance_in_miles = array_column($allDealers,'distance_in_miles','dlr_code');

			Databucket::isCacheSet($cacheName, json_encode($array_walk));
			Databucket::isCacheSet($cacheNameMiles, json_encode($distance_in_miles));
			$array_walk = json_decode(Databucket::isCacheGet($cacheName), true);
		}

		 return $array_walk;
	}

	/**
     *  SubFunction: Get Dealer list two array value of (DealerCode and DealerName) & (DealerCode *    and MilesAway)
	 *  based on given Zipcode.
	 *
	 *	@params:  Zipcode, Radius, Name of the Make, Name of the Model, Year, Vehicle Type [New, CPO]
	 *
	 *  getLatLonByZipcode() -> Fcaore\Databucket\GeoLocation.php
	 *	MilesByquery() 		 -> Fcaore\Databucket\SqlQueries.php
	 *
	 * Author: SATHISH KUMAR<sathisha@v2soft.com>
	 * DATE  : 30-APR-2019
     * @var Illuminate\Http\Request $request
     *
     * @return array
     *
     */
	public function radiusCPOByQuery($make,$vehicle_type,$params_zipcode, $radius){


		$cacheName = 'radiusCPOByDealer:'.$params_zipcode.':'.$radius.':'.str_replace(" ","_",$make).':'.strtolower($vehicle_type);
		 if(Databucket::isCacheExists($cacheName)){
				$array_walk = json_decode(Databucket::isCacheGet($cacheName), true);
		} else{
				$latlan_zipcode = Databucket::makeCache('cord:zip:latlan:'.$params_zipcode);
				  if(!Databucket::isCacheExists($latlan_zipcode)){
					 Databucket::LatLongZipcode($params_zipcode);
					 $latlan_zipcode = Databucket::isCacheGet('cord:zip:latlan:'.$params_zipcode);
					 $getCordinates = explode("@",$latlan_zipcode);
				  }else{
					 $latlan_zipcode = Databucket::isCacheGet('cord:zip:latlan:'.$params_zipcode);
					 $getCordinates = explode("@",$latlan_zipcode);
				  }

				$allDealers = Databucket::MilesByCPODealer($getCordinates[0],$getCordinates[1],$params_zipcode, $radius,$make,   $vehicle_type);

				$distance_in_miles = array_column($allDealers,'distance_in_miles','dlr_code');


				Databucket::isCacheSet($cacheName, json_encode($distance_in_miles));
				$array_walk = json_decode(Databucket::isCacheGet($cacheName), true);
		}

		 return $array_walk;
	}

	public function getCordinatesUsingZipcode($byzip){

		$dealers_table = config('databucket.isCacheWithDate') ? 'alldealers:'.\Carbon\Carbon::today()->toDateString() : 'alldealers:';


		$getLatByZip = json_decode(Databucket::isCacheGet($dealers_table), true);

			$allradius= collect($getLatByZip)->mapToGroups(function ($item, $key){
				return [$item['dlr_shw_zip'] => ['dlr_shw_lat'=>$item['dlr_shw_lat'], 'dlr_shw_long'=>$item['dlr_shw_long']] ];
			});

		if(isset( $allradius[$byzip]))
			$finalArray = array('lat' => $allradius[$byzip][0]['dlr_shw_lat'], 'lon' => $allradius[$byzip][0]['dlr_shw_long']);
		else
			$finalArray = array();


		return $finalArray;

	}




	public function dealerCollection(){
		try {

			//$cacheName = 'alldealers:'.\Carbon\Carbon::today()->toDateString();
			$cacheName = config('databucket.isCacheWithDate') ? 'alldealers:'.\Carbon\Carbon::today()->toDateString() : 'alldealers:';

			 if(!Databucket::isCacheExists($cacheName)){
				$alldealers = \App\Clist::select('dlr_code','dlr_shw_lat','dlr_shw_long','dlr_shw_zip','dlr_dba_name','dlr_shw_addr1','dlr_shw_addr2','dlr_shw_city','dlr_shw_state','dlr_shw_zip','dlr_shw_phone','dlr_web_addr','dlr_email_dlr')
									->whereNotIn('dlr_code',function($query) {
											$query->select('dlr_code')->from('fca_ore_dealer_eliminate')->where('fca_ore_dealer_eliminate.status','=',0);
									})->get();

				Databucket::isCacheSet($cacheName, json_encode($alldealers));
			}
			return Databucket::isCacheGet($cacheName);

		} catch (Exception $e) {
			report($e);
			return false;
		}
	}

	public function dealerInfoByDealerCode($dealerCode){
			$dealerData = Databucket::dealerCollection();
			$collection_dealers['dealers']=json_decode($dealerData,true);
			$getSpecificDealerObject = new Databucket($collection_dealers);
			return $getSpecificDealerObject->from('dealers')->where('dlr_code',"=",$dealerCode)->first();
	 }




	public function radius( $lat=42.90140, $lon=-70.80580, $zipcode=65775, $radius=25){
			//$dealers_table = 'alldealers:'.\Carbon\Carbon::today()->toDateString();
			//$cacheNameZipDealerRadius = "alldealers:".\Carbon\Carbon::today()->toDateString().':'.$zipcode.':'.$radius;

			$dealers_table = config('databucket.isCacheWithDate') ? 'alldealers:'.\Carbon\Carbon::today()->toDateString() : 'alldealers:';

			$cacheNameZipDealerRadius = config('databucket.isCacheWithDate') ? "alldealers:".\Carbon\Carbon::today()->toDateString().':'.$zipcode.':'.$radius : "alldealers:".$zipcode.':'.$radius;

			 if(!Databucket::isCacheExists($dealers_table)){
				$alldealers = \App\Clist::select('dlr_code','dlr_shw_lat','dlr_shw_long','dlr_shw_zip','dlr_dba_name','dlr_shw_addr1','dlr_shw_addr2','dlr_shw_city','dlr_shw_state','dlr_shw_zip','dlr_shw_phone','dlr_web_addr','dlr_email_dlr')->whereNotIn('dlr_code',function($query) {
				   $query->select('dlr_code')->from('fca_ore_dealer_eliminate')->where('fca_ore_dealer_eliminate'.'.status','=',0);
				})->get();
				Databucket::isCacheSet($dealers_table, json_encode($alldealers));
			}


			if(!Databucket::isCacheExists($cacheNameZipDealerRadius)){
					$allradius =  $array = $dealr = $zfinder = array();
					foreach((json_decode(Databucket::isCacheGet($dealers_table), true)) as $key => $val){
						try{
					 		if(is_numeric($val['dlr_shw_lat']) && is_numeric($val['dlr_shw_long'])){
								$result = Databucket::distance($val['dlr_shw_lat'], $val['dlr_shw_long'],$lat,$lon,'N');

								if($result <= $radius){

									$dealr[$key]['dlr_shw_lat'] = $val['dlr_shw_lat'];
									$dealr[$key]['dlr_shw_long'] = $val['dlr_shw_long'];
									$dealr[$key]['dlr_shw_zip'] = $val['dlr_shw_zip'];
									$dealr[$key]['dlr_dba_name'] = $val['dlr_dba_name'];
									$dealr[$key]['miles'] = $result;
									$dealr[$key]['dlr_code'] = $val['dlr_code'];
								}
								$zfinder[$val['dlr_shw_zip']] =$val['dlr_shw_lat'].'@'.$val['dlr_shw_long'];
					 		}
						}catch(\Execption $e){

						}

					}

			array_multisort(array_column($dealr, "miles"), SORT_ASC, $dealr);

			$allradius['withmiles']= collect($dealr)->mapWithKeys(function ($item, $key){
				return [$item['dlr_shw_zip'] => ['dlr_code'=>$item['dlr_code'], 'dlr_dba_name'=>$item['dlr_dba_name'], 'miles' => $item['miles'] ] ];
			});

			$allradius['latlon']= collect($dealr)->mapWithKeys(function ($item, $key){
				return [$item['dlr_shw_zip'] => ['dlr_shw_lat'=>$item['dlr_shw_lat'], 'dlr_shw_long'=>$item['dlr_shw_long'] ] ];
			});
			$allradius['dlrcode']= collect($dealr)->mapWithKeys(function ($item, $key){
				return [$item['dlr_code'] ];
			});
			$allradius[] = array_unique($zfinder);

			$finalradius = json_decode(json_encode($allradius), true);
			Databucket::isCacheSet($cacheNameZipDealerRadius, json_encode($allradius));
		}else{
			$finalradius = json_decode(Databucket::isCacheGet($cacheNameZipDealerRadius), true);
		}



			return $finalradius;
			exit;
	}

	public function getMile($cachw,$dealer_code){

		$data = json_decode(Databucket::isCacheGet($cachw), true);
		if($data['withmiles'] != null && $data['withmiles'] != '')  {
			foreach($data['withmiles'] as $key1=>$val1){
				if($val1['dlr_code'] == $dealer_code) return $val1;
			}
		}
		return ['dlr_dba_name' => 'Does not exit', 'miles' => '0'];
	}

	public function get_package_options_array($data,$vin){
		$Obj_DBucket = new Databucket($data);
		$package_option = $Obj_DBucket->from('datarow')
									->where('vin','=',$vin)
									->get();
		$options=reset($package_option);
		$optionValues=array_filter(explode("|",$options['option_desc_raw']));

		return $optionValues;
	}

	public function zipValidation($zipcode){
        if(!Databucket::isCacheExists("cord:zip:latlan:".$zipcode)) {
            $getZipcode = \App\Zipcode::where(['zipcode' => $zipcode])->first();
            if(!empty($getZipcode)){
                $latitude 	= number_format(str_replace('+-','-',$getZipcode['latitude']),2);
                $longitude 	= number_format($getZipcode['longitude'],2);
                Databucket::isCacheSet('cord:zip:latlan:'.$zipcode, $latitude.'@'.$longitude);
                return true;
            }
            return false;
        }else{
            return true;
        }
	}
		/******************* RUNTIME OPERATION **********************************/
		public function sni_landing_list_cpo($params_vechType, $params_make, $params_zipcode,$params_radius){
			 $landingCpoParamsCacheKey = Databucket::makeCache('landing:'.$params_vechType.':'.$params_make.':'.$params_zipcode.':'.$params_radius);

			 $params_dealers =  Databucket::radiusCPOByQuery($params_make,$params_vechType,$params_zipcode,$params_radius);
			 $dealerCode = [];

			if(count($params_dealers) > 0){
				foreach($params_dealers as $key => $value ){
					$dealerCode[] = $key;
				}
			}

			if(!Databucket::isCacheExists($landingCpoParamsCacheKey)){
                 $findCpoLanding = json_encode(Databucket::CpoQuery($params_vechType, $params_make, $dealerCode));
                 $products = json_decode($findCpoLanding, true);
                 $grouped = collect($products)->mapToGroups(function ($item, $key){
                    return [$item['models'] => ['models'=>$item['models'],'year' => $item['year'], 'count' => $item['cnt'], 'cat_id' => 0, 'vehicle_type' => $item['vehicle_type'], 'subcat_id' => 0, 'msrp_price' => $item['msrp_price'], 'maxs_msrp'=>$item['maxs_msrp'], 'city_mpg' => $item['city_mpg'], 'hwy_mpg' => $item['hwy_mpg'] , 'trim_code' => $item['trim_code'], 'interior_fabric' => $item['interior_fabric'], 'exterior_color_code' => $item['exterior_color_code'], 'towing_capacity_count' => $item['towing_capacity_count'], 'upper_level_pkg_cd' => $item['upper_level_pkg_cd'], 'body_style' => $item['body_style'], 'max_year' => $item['max_year'], 'min_year' => $item['min_year'] ] ];
                 });

				 foreach($grouped as $key => $val){
						Databucket::price_pipes($val, $params_vechType, $params_make);
					}

                 Databucket::isCacheSet($landingCpoParamsCacheKey, json_encode($grouped));
             }else{
                 $grouped = Databucket::isCacheGet($landingCpoParamsCacheKey);
             }

			return $products = json_decode($grouped, true);
		}

		public function sni_landing_list($params_vechType, $params_make, $zipcode,$params_radius){
			 $landingParamsCacheKey = Databucket::makeCache('landing:'.$params_make.':'.$params_vechType.':'.$zipcode.':'.$params_radius);

			 if(!Databucket::isCacheExists($landingParamsCacheKey)){
						$land = json_encode(Databucket::sniLandingQuery($params_vechType, $params_make, $zipcode,$params_radius));

						$products = json_decode($land, true);
						$grouped = collect($products)->mapToGroups(function ($item, $key){
							return [$item['models'] => ['models'=>$item['models'],'year' => $item['year'], 'count' => $item['cnt'], 'cat_id' => $item['cat_id'], 'vehicle_type' => $item['vehicle_type'], 'subcat_id' => $item['subcat_id'], 'msrp_price' => $item['msrp_price'], 'maxs_msrp' => $item['maxs_msrp'], 'city_mpg' => $item['city_mpg'], 'hwy_mpg' => $item['hwy_mpg'] , 'trim_code' => $item['trim_code'] , 'exterior_color_code' => $item['exterior_color_code'], 'towing_capacity_count' => $item['towing_capacity_count'], 'upper_level_pkg_cd' => $item['upper_level_pkg_cd'], 'body_style' => $item['body_style']  ] ];
						});

						foreach($grouped as $key => $val){
							Databucket::price_pipes($val, $params_vechType, $params_make);
						}

						Databucket::isCacheSet($landingParamsCacheKey, json_encode($grouped));
					} else{
				$grouped = Databucket::isCacheGet($landingParamsCacheKey);
			}

			return $products = json_decode($grouped, true);
		}



	public function sni1($params_vechType, $params_make){		

					// $landingParamsCacheKey = Databucket::makeCache('landing:'.$params_make.':'.$params_vechType); 

					// $land = json_encode(Databucket::sniLandingQuery($params_vechType, $params_make));

					// $products = json_decode($land, true);
					// $grouped = collect($products)->mapToGroups(function ($item, $key){
					// 	return [$item['models'] => ['models'=>$item['models'],'year' => $item['year'], 'count' => $item['cnt'],  'msrp_price' => $item['msrp_price'], 'city_mpg' => $item['city_mpg'], 'hwy_mpg' => $item['city_mpg'] , 'trim_code' => $item['trim_code'] , 'exterior_color_code' => $item['exterior_color_code'], 'towing_capacity_count' => $item['towing_capacity_count'], 'upper_level_pkg_cd' => $item['upper_level_pkg_cd'] ] ];
					// });

					// foreach($grouped as $key => $val){
					// 	Databucket::price_pipes($val, $params_vechType, $params_make);
					// }

					// Databucket::isCacheSet($landingParamsCacheKey, json_encode($grouped));

					// return $grouped;
	}
	public function mdoca_availability($dlr_code){
		$mdocaCacheKey = Databucket::makeCache('mdoca:dealers');

		 if(!Databucket::isCacheExists($mdocaCacheKey)){
			$mdoca_db_dealers = Databucket::mdoca_query();
			$mdoca_dealers  = json_decode($mdoca_db_dealers, true);
			Databucket::isCacheSet($mdocaCacheKey, json_encode($mdoca_dealers));
		 }else{
			$mdoca_cache_dealers = Databucket::isCacheGet($mdocaCacheKey);
			$mdoca_dealers  = json_decode($mdoca_cache_dealers, true);
		 }


		 $getDealerCodeOnly = Arr::pluck($mdoca_dealers, 'dlr_code');


		 if(in_array($dlr_code, $getDealerCodeOnly)){
			 $grouped = collect($mdoca_dealers)->mapToGroups(function ($item, $key){
				return [$item['dlr_code'] => ['known_r1_id'=>$item['known_r1_id'],'batched' => $item['batched'], 'dpa' => $item['dpa'], 'no_account' => $item['no_account'], 'edpa' => $item['edpa'] ]];
			});

			 $grouped_mdoca_dealers1  = json_decode($grouped, true);
			 if(!array_key_exists($dlr_code, $grouped_mdoca_dealers1)){
			 	return ['status' => 'unavailable', 'dlr_code' => '', 'known_r1_id'=> '', 'batched' => '', 'dpa' => 0, 'no_account' => 0, 'edpa' => 0];
			 }

			$grouped_mdoca_dealers  = $grouped_mdoca_dealers1[$dlr_code];
			$known_r1_id 	= $grouped_mdoca_dealers[0]['known_r1_id'];
			$batched 		= $grouped_mdoca_dealers[0]['batched'];
			$dpa 			= $grouped_mdoca_dealers[0]['dpa'];
			$no_account 	= $grouped_mdoca_dealers[0]['no_account'];
			$edpa 			= $grouped_mdoca_dealers[0]['edpa'];

			 return ['status' => 'available', 'dlr_code' => $dlr_code, 'known_r1_id'=> $known_r1_id, 'batched' => $batched, 'dpa' => $dpa , 'no_account' => $no_account, 'edpa' => $edpa];
		 }else{
			 return ['status' => 'unavailable', 'dlr_code' => '', 'known_r1_id'=> '', 'batched' => '', 'dpa' => 0, 'no_account' => 0, 'edpa' => 0];
		 }
	}

	public function SNI2DealersCacheSystems($type, $make){
				$this->logAppend('Start', 'RUNTIMEL SNI2 DEALER BY MAKE-MODEL-YEAR for '.$type.' @'.$make);
				$lists = Databucket::makeTypeYearModel($type, $make);
				$this->ymk_type = $type;
				$this->ymk_make = $make;
				$this->VariableForYearMakeModel  = $lists;

				$cache_array = array();
				 Redis::pipeline(function ($pipe) {
									foreach($this->VariableForYearMakeModel as $lkey => $lval){
									$cache = '';

									$cache = Databucket::makeCache('dealersByModel:'.strtolower($this->ymk_type).':'.strtolower(str_replace(' ', '_',$this->ymk_make)).':'.strtolower(str_replace(' ', '_',$lval->model)).':'.$lval->year);

									 $cache_array[] = $cache;
									 $specifList = Databucket::DealerTypeYearModel($this->ymk_type, $this->ymk_make, $lval->year, $lval->model);
									 if(!Databucket::isCacheExists($cache)){
												$pipe->set($cache, json_encode($specifList));
									 }
									}
				 });
			$this->logAppend('End',  'RUNTIMEL: SNI2 DEALER BY MAKE-MODEL-YEAR for '.$type.' @'.$make);
			return true;
	}
	public function LatLongZipcode($zipcode){
				$zip_latlon = \App\Zipcode::select('latitude','longitude','zipcode')->where(['zipcode' => $zipcode])->get();

				foreach($zip_latlon as $key=>$val){
					$zipcode 	= $val['zipcode'];
					$latitude 	= number_format(str_replace('+-','-',$val['latitude']),2);
					$longitude 	= number_format($val['longitude'],2);
					if(!Databucket::isCacheExists('cord:zip:latlan:'.$zipcode)){
						Databucket::isCacheSet('cord:zip:latlan:'.$zipcode, $latitude.'@'.$longitude);
					}

				}
				return true;
	}
	public function CategoriesSubCategoryIDInititlizedCacheSystems($params_vechType, $vehicle_brands=null, $year=null, $model=null){
		try {
			//  $this->logAppend('Start', 'Runtime: Categories SubCategory Inititlized ID CacheSystems  @'.$params_vechType);
					$result =  array();
					$result = Databucket::sniCatSubCatQuery($params_vechType, $vehicle_brands, $year, $model);
					Databucket::catsubcat_pipes(json_decode($result, true), "catsubcat:", $params_vechType);
			//$this->logAppend('End', 'Runtime: Categories SubCategory Inititlized ID CacheSystems  @'.$params_vechType);
		} catch (Exception $e) {
			report($e);
			return false;
		}
			return true;
    }

	public function customModel($model){
		return strtolower(str_replace(' ','_',str_replace('-','_',$model)));

	}
	public function customIBModel($model){
		$model =  strtolower(str_replace('_',' ',str_replace('-',' ',$model)));
		$this->resolveModel($model);
		return $model;
	}

	public function dashName($name)
    {
        $find = ['_',' '];
        $replace = ['-','-'];
        $name 							= str_replace($find,$replace, $name);
        return $name;
    }
	public function mergeGroup($grouped)
    {
        foreach($this->possibleDuplicates as $key=>$value){
            $hasGroupToMerge = Databucket::collectionHasKey($key,$grouped);
            if($hasGroupToMerge) $grouped = collect( Databucket::mergeDublicate($grouped,$value[0],$value[1]));
        }
        return $grouped;
    }

	public function dealerNameAlternaation($dealerName)
    {
      
        return $dealerName;
    }

	public function AllDealersCacheSystems(){
		try {
			  $this->logAppend('Start', 'Runtime: All Dealers CacheSystems');
					Databucket::dealerCollection();
			 $this->logAppend('End', 'Runtime: All Dealers CacheSystems');
		} catch (Exception $e) {
			report($e);
			return false;
		}
		return true;
    }

	function logAppend($time, $cronName){
		$dots = '-----------------------------------------------------------------------------';
		$timings = Carbon::now('America/New_York').': *** '.$time.':  '.$cronName.' ****';

		if($time == 'Start'){
				$this->info($dots);
				Log::info($dots);
		}

		$this->info($timings);
		Log::info($timings);

		if($time == 'End'){
				$this->info($dots);
				Log::info($dots);
		}
	}
	/******************* RUNTIME OPERATION **********************************/
}
