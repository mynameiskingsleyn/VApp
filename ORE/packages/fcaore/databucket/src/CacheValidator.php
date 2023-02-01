<?php namespace Fcaore\Databucket;

use Illuminate\Support\Facades\Redis;
use Fcaore\Databucket\Databucket;

trait CacheValidator
{

private $array, $tmp_key_chunk;

private $params_vechType, $params_make;

     public function makeCache($keys){ 
        return $keys;
	}

    public function isCacheExists($rKey){
		if(Redis::exists($rKey)) return true; else return false;
	}

    public function isCacheSet($key, $value){ 
        Redis::set($key, $value);
        return true;
	}

	 public function CacheSelect($index){
        Redis::select($index);
        return true;
	}

	public function swapdb($fromIndex, $toIndex){
        Redis::swapdb($fromIndex, $toIndex);
        return true;
	}

	public function CacheFlushCurrentRedisDB(){
		//if(Databucket::CacheSelect($index)){
			Redis::flushdb();
		//}
	}

	public function CacheFlushAll(){
		Redis::flushall();
	}

	public function DelKey($key){
		Redis::del($key);
	}

	public function Keys(){
		Redis::keys('*');
	}

	public function dbsize(){
		Redis::dbsize();
	}

	public function info(){
		Redis::info();
	}


	public function bulk_delete($key){
		$this->$tmp_key_chunk = $key;

		 Redis::pipeline(function ($pipe) {
                 foreach (Redis::keys($this->$tmp_key_chunk.'*') as $key) {
                     $pipe->del($key);
                 }
           });
	}

	public function iffy_Cache_set($cacheType, $params_vechType, $params_make, $param_year=null, $param_model=null, $zipcode=null, $radius=null){
		//$cache_name =  Databucket::makeCache('msrptow:'.strtolower($this->params_vechType).':'.$this->params_make.':'.strtolower(str_replace(' ','-',$val['models'])).':'.$val['year']);
		if($cacheType == 'msrptow'){
			$cache_name =  Databucket::makeCache('msrptow:'.strtolower($this->params_vechType).':'.$this->params_make.':'.strtolower(str_replace(' ','_',$param_model)).':'.$param_year);
			if(!Databucket::hexists($cache_name, 'msrp_price')){
				Databucket::isCacheHMSet($cache_name, 'msrp_price', $val['msrp_price']);
				Databucket::isCacheHMSet($cache_name, 'maxs_msrp', $val['maxs_msrp']);
				Databucket::isCacheHMSet($cache_name, 'towing_capacity_count', $val['towing_capacity_count']);
			}
		}


		/*
		*  MAXS
		*/
		if($cacheType == 'maxs'){
				$pass_model = str_replace('-',' ',$param_model);
				$pass_model = str_replace('_',' ',$pass_model);

				$maxs_cacheName 		= 'maxs:'.$zipcode.':'.$radius.':'.$params_make.':'.$params_vechType.':'.$pass_model;
				$array_maxs 			= ['MaxYear' => date('Y'), 'MinYear' => 2017, 'MinPrice' => 20000, 'MaxPrice' => 60000 ];



				$outp = Databucket::SQLRightUsedCpoDealers($zipcode, $radius,$params_make, $params_vechType, $param_model, $param_year);
				if(count($outp) > 0){
					$dealerCodes = array_column($outp, 'dlr_code');
				}else $dealerCodes = [];

				$outp2 = Databucket::SQLRightUsedCpoMaxMin($dealerCodes, $params_make, $params_vechType, $param_model, $param_year);
				if($outp2 != ''){
					list($array_maxs) = json_decode($outp2, true);
				}
				Databucket::isCacheHMSet($maxs_cacheName, 'MaxYear', $array_maxs['MaxYear']);
				Databucket::isCacheHMSet($maxs_cacheName, 'MinYear', $array_maxs['MinYear']);

				$maxs_cacheName2 		= 'maxs-p:'.$zipcode.':'.$radius.':'.$params_make.':'.$params_vechType.':'.$pass_model.':'.$param_year;
				$outp3 = Databucket::SQLRightUsedCpoDealersPrices($zipcode, $radius,$params_make, $params_vechType, $param_model, $param_year);
				if(count($outp3) > 0){
					$dealerCodes3 = array_column($outp3, 'dlr_code');
				}else $dealerCodes3 = [];

				$outp4 = Databucket::SQLRightUsedCpoMaxMinPrices($dealerCodes3, $params_make, $params_vechType, $param_model, $param_year);
				if($outp4 != ''){
					list($array_maxs4) = json_decode($outp4, true);
				}
				Databucket::isCacheHMSet($maxs_cacheName2, 'MaxPrice', $array_maxs4['MaxPrice']);
				Databucket::isCacheHMSet($maxs_cacheName2, 'MinPrice', $array_maxs4['MinPrice']);
		}

	}

	public function iffy_Cache_get($cacheType, $params_vechType, $params_make, $param_year=null, $param_model=null, $zipcode=null, $radius=null){
		/*
		*  CATID-SUBCATID
		*/
		if($cacheType == 'catsubcat'){

			$cache_name =  Databucket::makeCache('catsubcat:'.strtolower($params_vechType).':'.strtolower(str_replace('_','-',str_replace(' ','-',$params_make))).':'.Databucket::customModel($param_model).':'.$param_year);

			if(!Databucket::hexists($cache_name, 'cat_id')){
				$pass_model = str_replace('-',' ',$param_model);
				$pass_model = str_replace('_',' ',$pass_model);
				Databucket::CategoriesSubCategoryIDInititlizedCacheSystems($params_vechType,$params_make, $param_year, $pass_model);
			}
			$cat_id 	= Databucket::isCacheHMGet($cache_name, 'cat_id');
			$subcat_id	= Databucket::isCacheHMGet($cache_name, 'subcat_id');
			return array('cat_id' => $cat_id[0], 'subcat_id'=>$subcat_id[0]);
		}
		/*
		*  MSRP - MAX - MIN
		*/
		if($cacheType == 'msrptow'){
			$cache_name =  Databucket::makeCache('msrptow:'.strtolower($params_vechType).':'.strtolower(str_replace(' ','_',$params_make)).':'.Databucket::customModel($param_model).':'.$param_year);

			if(!Databucket::hexists($cache_name, 'msrp_price')){

			}else{
				$msrp_price = Databucket::isCacheHMGet($cache_name, 'msrp_price');
				$maxs_msrp = Databucket::isCacheHMGet($cache_name, 'maxs_msrp');
				$towing_capacity_count = Databucket::isCacheHMGet($cache_name, 'towing_capacity_count') ;
			}
			return array('msrp_price' => $msrp_price[0], 'maxs_msrp'=>$maxs_msrp[0], 'towing_capacity_count' => $maxs_msrp[0]);
		}
		/*
		*  RadiusByQuery: Get all Dealers ID and Dealer Name
		*/
		if($cacheType == 'radiusByQuery'){
			$radcacheName = Databucket::makeCache('radiusByQuery:'.$zipcode.':'.$radius.':'.str_replace(" ","_",$params_make).':'.str_replace(" ","_",$param_model).':'.$param_year.':'.strtolower($params_vechType));
			 return $aradcacheName = json_decode(Databucket::isCacheGet($radcacheName), true);
		}
		/*
		*  MAXS
		*/
		if($cacheType == 'maxs'){
			$maxs_cacheName 		=  Databucket::makeCache('maxs:'.$zipcode.':'.$radius.':'.$params_make.':'.$params_vechType.':'.$param_model);
			$MaxYear  = Databucket::isCacheHMGet($maxs_cacheName, 'MaxYear');
			$MinYear  =	Databucket::isCacheHMGet($maxs_cacheName, 'MinYear');
		//	$MaxPrice =	Databucket::isCacheHMGet($maxs_cacheName, 'MaxPrice');
		//	$MinPrice =	Databucket::isCacheHMGet($maxs_cacheName, 'MinPrice');

			return array('MaxYear' => $MaxYear[0], 'MinYear'=>$MinYear[0]);//, 'MaxPrice' => $MaxPrice[0], 'MinPrice' => $MinPrice[0]);
		}
		if($cacheType == 'maxs-p'){
			$maxs_cacheName_p 		=  Databucket::makeCache('maxs-p:'.$zipcode.':'.$radius.':'.$params_make.':'.$params_vechType.':'.$param_model.':'.$param_year);
		 	$MaxPrice =	Databucket::isCacheHMGet($maxs_cacheName_p, 'MaxPrice');
		    $MinPrice =	Databucket::isCacheHMGet($maxs_cacheName_p, 'MinPrice');

			return array('MaxPrice' => $MaxPrice[0], 'MinPrice' => $MinPrice[0]);
		}

	}


	public function catsubcat_pipes($array, $cache_prefix, $params_vechType){
		$this->array = $array;
		$this->prefix = $cache_prefix.strtolower($params_vechType).':';

	 if(count($this->array) > 0){
				Redis::pipeline(function ($pipe) {
					 $find = [' ','-'];
                        $replace = ['_','_'];

						/* foreach($this->array as $key => $val){
						$cache_name = $this->prefix.strtolower(str_replace(' ','_',$val['brandName'])).':'.strtolower(str_replace(' ','_',$val['modelName'])).':'.$val['year'];
								$pipe->hmset($cache_name, 'cat_id', $val['cat_id']);
								$pipe->hmset($cache_name, 'subcat_id',$val['subcat_id']);
						}  */
						foreach($this->array as $key => $val){
						$cache_name = $this->prefix.strtolower(str_replace(' ','_',$val['brandName'])).':'.strtolower(str_replace($find,$replace,$val['modelName'])).':'.$val['year'];
								$pipe->hmset($cache_name, 'cat_id', $val['cat_id']);
								$pipe->hmset($cache_name, 'subcat_id',$val['subcat_id']);
						}
				});
		}
	}

	public function price_pipes($array, $params_vechType, $params_make){
					$this->array = $array;
					$this->params_vechType = $params_vechType;
					$params_make = str_replace(' ','-',$params_make);
					$params_make = str_replace('_','-',$params_make);
					$this->params_make = strtolower($params_make);

					if(count($this->array) > 0){
							Redis::pipeline(function ($pipe) {
									foreach($this->array as $key => $val){

									 $cache_name =  Databucket::makeCache('msrptow:'.strtolower($this->params_vechType).':'.$this->params_make.':'.strtolower(str_replace(' ','_',$val['models'])).':'.$val['year']);


											if(!Databucket::hexists($cache_name, 'msrp_price')){
													$pipe->hmset($cache_name, 'msrp_price', $val['msrp_price']);
													//$pipe->hmset($cache_name, 'maxs_msrp', $val['maxs_msrp']);
													$pipe->hmset($cache_name, 'towing_capacity_count', $val['towing_capacity_count']);
											}
									}
							});
					}
	}

	// ----- WITHOUT PIPE GETTING RECORD ------

    public function isCacheGet2($key){
        $have_chunk = config('databucket.have_chunk');
		$this->tmp_key_chunk = $key;


       if($have_chunk){
			$main_array=array();
            if (strpos($key, 'vehicle_table_') !== false || strpos($key, 'package_and_options_') !== false) {
			   $cacheMemoryValue = Redis::get($key);

                $result=[];
                for($m=0; $m<$cacheMemoryValue; $m++){
                    $main_array[] = json_decode(Redis::get($key.'_chunk'.$m), true);
                }
                foreach($main_array as $array){  $result = array_merge($result, $array);   }

               return json_encode($result);
                unset($result);
            } else{
                return Redis::get($key);
            }

        }else{
            return Redis::get($key);
        }

    }


	// ----- WITH PIPE GETTING RECORD ------
	  public function isCacheGet($key){
        $have_chunk = config('databucket.have_chunk');
		$this->tmp_key_chunk = $key;

		if($have_chunk){
							$main_array=array();
							if (strpos($key, 'vehicle_table_') !== false || strpos($key, 'package_and_options_') !== false) {
								$result = Redis::pipeline(function ($pipe) {
												$cacheMemoryValue = Redis::get($this->tmp_key_chunk) ;
												for($m=0; $m<$cacheMemoryValue; $m++){
														$pipe->get($this->tmp_key_chunk.'_chunk'.$m);
												}
										});

						 			if(count($result) > 0) return $result[0]; else return $result=array();

									unset($result);
							} else{
								return Redis::get($key);
							}
        }else{
						return Redis::get($key);
        }

    }

    public function getVinInfo($vehicleModel, $requiredVin){
		dd($vehicleModel);
    }

    public function isCacheHMSet($key, $name, $value){ 
        Redis::hmset($key, $name,$value);
        return true;
    }

    public function cacheHMDel($key,$field=null)
    {
        if(!$field)
            $this->DelKey($key);
        else{
            Redis::hdel($key,$field);
        }
        ;
    }

	public function isCacheHMGet($key, $name){
        return Redis::hmget($key, $name);
    }

    public function multi(){
        Redis::multi();  return true;
    }

    public function exec(){
        Redis::exec();  return true;
    }



	public function isCacheGetAll($key){
        return Redis::hgetall($key);
    }

    public function isCacheHvals($key){
        return Redis::hvals($key);
    }

    public function isCacheHKeys($key){
        return Redis::hvals($key);
    }

    public function hexists($key, $field){
        return Redis::hexists($key, $field);
	}

	public function cacheSetValue($key,$value)
    {
        return Redis::set($key,$value);
    }

    public function cacheGetValue($key)
    {
        return json_decode(Redis::get($key));
    }


}
