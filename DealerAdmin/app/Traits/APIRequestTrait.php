<?php

namespace App\Traits;

use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CacheTrait;

trait APIRequestTrait
{
	private $StatusCode = 1000;
	private $Message = "Success";

	public function getMakeAbbrevationName($MakeCode){
		if($MakeCode == 'Y'){
			$makeAbbrevationName = "alfa romeo";
		}else if($MakeCode ==  'C'){
			$makeAbbrevationName =  "chrysler";
		}else if($MakeCode == 'D'){
			$makeAbbrevationName =  "dodge";
		}else if($MakeCode == 'X'){
			$makeAbbrevationName =  "fiat";
		}else if($MakeCode == 'J'){
			$makeAbbrevationName =  "jeep";
		}else if($MakeCode == 'T'){
			$makeAbbrevationName =  "ram";
		}else{
			$makeAbbrevationName =  "alfa romeo";
		}
		return $makeAbbrevationName;
	}

	public function traitFilterMakes($data, $NameOfAPI, $DealerCode, $cacheName){

		$array = array();
		$array["NameOfAPI"] 	= $NameOfAPI;
		$array["StatusCode"] 	= $this->StatusCode;
		$array["Message"] 		= $this->Message;
		$array["DealerCode"] 	= $DealerCode;
		$array["data"] = array();
		$array["data"]['make'] = array();
		$make = array();
		$data2 = $data;
		foreach($data2 as $key => $value){
			if(strtolower($value) == 'alfa romeo'){
				$make['MakeCode'] = 'Y'; $make['MakeName'] = $value;
			}else if(strtolower($value) == 'chrysler'){
				$make['MakeCode'] = 'C'; $make['MakeName'] = $value;
			}else if(strtolower($value) == 'dodge'){
				$make['MakeCode'] = 'D'; $make['MakeName'] = $value;
			}else if(strtolower($value) == 'fiat'){
				$make['MakeCode'] = 'X'; $make['MakeName'] = $value;
			}else if(strtolower($value) == 'jeep'){
				$make['MakeCode'] = 'J'; $make['MakeName'] = $value;
			}else if(strtolower($value) == 'ram'){
				$make['MakeCode'] = 'T'; $make['MakeName'] = $value;
			}else{
				$make['MakeCode'] = 'Y'; $make['MakeName'] = $value;
			}
			array_push($array["data"]['make'],$make);
		}
		asort($array);
		$this->isCacheHMSet($cacheName, $DealerCode, json_encode($array));

		return $array;
	}


	public function traitModelYear($dataloop, $NameOfAPI, $DealerCode, $MakeCode, $cacheName){
			$array = array();
			$array["NameOfAPI"] 	= $NameOfAPI;
			$array["StatusCode"] 	= $this->StatusCode;
			$array["Message"] 		= $this->Message;

			$array["data"]["DealerCode"]	= $DealerCode;
			$array["data"]["MakeCode"]		= $MakeCode;
			sort($dataloop);
			$array["data"]["ModelYears"] = $dataloop;
			//$dataloop2 = json_decode($dataloop, true);
			/*$dataloop2 = $dataloop;
			//$dataloop2 = array_column($dataloop2, 'year');
			\Log::info($dataloop2);
			foreach($dataloop2 as $key => $value){
				array_push($array["data"]["ModelYears"], $value);
			}*/

			$this->isCacheHMSet($cacheName, $DealerCode, json_encode($array));

			return $array;
	}


	public function traitGetModel($dataloop, $NameOfAPI, $DealerCode, $MakeCode, $ModelYear, $cacheName){
			$array = array();
			$array["NameOfAPI"] 	= $NameOfAPI;
			$array["StatusCode"] 	= $this->StatusCode;
			$array["Message"] 		= $this->Message;

			$array["data"]["DealerCode"]	= $DealerCode;
			$array["data"]["MakeCode"]		= $MakeCode;
			$array["data"]["ModelYear"]		= $ModelYear;
			sort($dataloop);
			 $array["data"]["Models"] = $dataloop;
			//$dataloop2 = json_decode($dataloop, true);
			/*$dataloop2 = $dataloop;
			$dataloop2 = array_column($dataloop2, 'model');

			if(count($dataloop2) > 0){
				foreach($dataloop2 as $key => $value){
					//$array["data"]["Models"][$value] = 	$value;
					array_push($array["data"]["Models"], $value);
				}
			}*/
			$this->isCacheHMSet($cacheName, $DealerCode, json_encode($array));

			return $array;
	}

	public function traitFilterTrimSelection($dataloop, $NameOfAPI, $DealerCode, $MakeCode, $ModelYear, $Model,  $cacheName){
			$array = array();
			$array["NameOfAPI"] 	= $NameOfAPI;
			$array["StatusCode"] 	= $this->StatusCode;
			$array["Message"] 		= $this->Message;

			$array["data"]["DealerCode"]	= $DealerCode;
			$array["data"]["MakeCode"]		= $MakeCode;
			$array["data"]["ModelYear"]		= $ModelYear;
			$array["data"]["Model"]		= $Model;
			\Log::info('Data loop is below');
			\Log::debug($dataloop);
			asort($dataloop);
			 //$array["data"]["Trims"] = $dataloop;
			/*$dataloop2 = json_decode($dataloop, true);
			$dataloop2 = array_column($dataloop2, 'trim_desc');*/
		 	$array["data"]["Trims"] = array();
            $allTrims = [];
			if(count($dataloop) > 0){
				foreach($dataloop as $key => $value){

					  //'&': '&amp;','"': '&quot;',"'": '&#39;',
					$trim = str_replace('"', "&quot;", $value);
					$trim = str_replace("'", '&#39;', $trim);
					$allTrims[$key] = $trim;
				}
				\Log::info('here is all trims');
				\Log::debug($allTrims);
                $array["data"]["Trims"] = $allTrims;
			}
			\Log::info($array["data"]["Trims"]);
			$this->isCacheHMSet($cacheName, $DealerCode, json_encode($array));

			return $array;
	}


	public function traitFilterMsrpSelection($msrp_values, $NameOfAPI, $DealerCode, $MakeCode, $ModelYear, $Model, $Trim, $cacheName){
			$array = array();
			$array["NameOfAPI"] 	= $NameOfAPI;
			$array["StatusCode"] 	= $this->StatusCode;
			$array["Message"] 		= $this->Message;

			$array["data"]["DealerCode"]	= $DealerCode;
			$array["data"]["MakeCode"]		= $MakeCode;
			$array["data"]["ModelYear"]		= $ModelYear;
			$array["data"]["Model"]		= $Model;
			$array["data"]["Trim"]		= $Trim;
			$array["data"]["Msrp"] = array();
			$array["data"]["Msrp"]["Highest"]		= !empty($msrp_values) ? $msrp_values['msrp_highest'] : 0;
			$array["data"]["Msrp"]["Lowest"]		= !empty($msrp_values) ? $msrp_values['msrp_lowest'] : 0;
			$this->isCacheHMSet($cacheName, $DealerCode, json_encode($array));

			return $array;
	}


	public function traitFilterSecondarySelection($dataloop, $NameOfAPI, $DealerCode, $MakeCode, $ModelYear, $Model, $Trim, $MsrpHighest, $MsrpLowest, $cacheName){
			$array = array();
			$array["NameOfAPI"] 	= $NameOfAPI;
			$array["StatusCode"] 	= $this->StatusCode;
			$array["Message"] 		= $this->Message;

			$array["data"]["DealerCode"]	= $DealerCode;
			$array["data"]["MakeCode"]		= $MakeCode;
			$array["data"]["ModelYear"]		= $ModelYear;
			$array["data"]["Model"]		= $Model;
			$array["data"]["Trim"]		= $Trim;
			$array["data"]["MsrpHighest"]= $MsrpHighest;
			$array["data"]["MsrpLowest"]= $MsrpLowest;

			 $array["data"]["Attributes"] = array();
			//$dataloop2 = json_decode($dataloop, true);
			asort($dataloop);
			$dataloop2 = $dataloop;

			//$drive_type = array_column($dataloop2, 'drive_type');
			$drive_type = $dataloop2['drive_type'];
			$array["data"]["Attributes"]["drive_type"] = $drive_type;
			//array_push($array["data"]["Attributes"]["drive_type"], $drive_type);

			//$interior_colour_desc = array_column($dataloop2, 'interior_colour_desc');
			$interior_colour_desc = $dataloop2['exterior_color_code'];
			$array["data"]["Attributes"]["exterior_color_code"] = $interior_colour_desc;
			//array_push($array["data"]["Attributes"]["interior_colour_desc"], $interior_colour_desc);

			//$transmission_desc = array_column($dataloop2, 'transmission_desc');
			$transmission_desc = $dataloop2['transmission_desc'];
			$array["data"]["Attributes"]["transmission_desc"] = $transmission_desc;
			//array_push($array["data"]["Attributes"]["transmission_desc"], $transmission_desc);

			//$eng_desc = array_column($dataloop2, 'eng_desc');
			$eng_desc = $dataloop2['eng_desc'];
			$array["data"]["Attributes"]["eng_desc"] = $eng_desc;
			//array_push($array["data"]["Attributes"]["eng_desc"], $eng_desc);

			$this->isCacheHMSet($cacheName, $DealerCode, json_encode($array));

			return $array;
	}


	public function traitSearchByAttributes($dataloop, $NameOfAPI, $DealerCode, $MakeCode, $ModelYear, $Model, $Trim, $MsrpHighest, $MsrpLowest, $drive_type,$interior_colour_desc, $transmission_desc, $eng_desc){
			$array = array();
			$array["NameOfAPI"] 	= $NameOfAPI;
			$array["StatusCode"] 	= $this->StatusCode;
			$array["Message"] 		= $this->Message;

			$array["DealerCode"]	= $DealerCode;
			$array["MakeCode"]		= $MakeCode;
			$array["ModelYear"]		= $ModelYear;
			$array["Model"]			= $Model;
			$array["Trim"]			= $Trim;
			$array["MsrpHighest"]	= $MsrpHighest;
			$array["MsrpLowest"]	= $MsrpLowest;
			$array["DriveNames"]	= $drive_type;
			$array["ColorNames"]	= $interior_colour_desc;
			$array["TransmissionNames"]	= $transmission_desc;
			$array["EngineDescNames"]= $eng_desc;

			 $array["vehicles"]["list"] = array();
			 foreach ($dataloop as $key => $value) {
			 	$dataloop[$key]['msrp_format'] = '$' . number_format($value['msrp']);
			 }
			 $array["vehicles"]["list"] = $dataloop;
			 asort($array);
			return $array;
	}

	public function traitSearchByVIN($dataloop, $NameOfAPI, $DealerCode, $VinNumber){
			$array = array();
			$array["NameOfAPI"] 	= $NameOfAPI;
			$array["StatusCode"] 	= $this->StatusCode;
			$array["Message"] 		= $this->Message;

			$array["DealerCode"]	= $DealerCode;
			//$array["MakeCode"]		= $MakeCode;
			$array["VinNumber"]		= $VinNumber;
			foreach ($dataloop as $key => $value) {
			 	$dataloop[$key]['msrp_format'] = '$' . number_format($value['msrp']);
			 }
			$array["vehicles"]= $dataloop;
			asort($array);
			return $array;
	}
}
