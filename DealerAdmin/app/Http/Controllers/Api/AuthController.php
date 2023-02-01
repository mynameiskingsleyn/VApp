<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request; 

use App\Http\Controllers\Controller; 

use App\User; 

use Illuminate\Support\Facades\Auth; 

use Validator;

class AuthController extends Controller 
{
	public $successStatus = 200;
  
	public function register(Request $request) {    
		 $validator = Validator::make($request->all(), [ 
					  'name' => 'required',
					  //'email' => 'required|email',
					  'email' => 'required',
					  'password' => 'required',  
					  'c_password' => 'required|same:password', 
			]);   
		 if ($validator->fails()) {          
			   return response()->json(['error'=>$validator->errors()], 401);                        
		}    
		 $input = $request->all();  
		 $input['password'] = bcrypt($input['password']);
		 $user = User::create($input); 
		 $success['token'] =  $user->createToken('AppName')->accessToken;
		 return response()->json(['success'=>$success], $this->successStatus); 
	}  
   
	public function login(){ 
		if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
		   $user = Auth::user(); 
		   $success['token'] =  $user->createToken('AppName')->accessToken; 
			return response()->json(['success' => $success], $this->successStatus); 
		  } else{ 
		   return response()->json(['error'=>'Unauthorised'], 401); 
		   } 
	}
	 
	public function getUser() {
		$user = Auth::user();
		return response()->json(['success' => $user], $this->successStatus); 
	}

	public function login_demo(Request $request){ 
		$response = array();
		$validator = Validator::make($request->all(), [ 
				  'email' => 'required',
				  'password' => 'required'
		]);   
		if ($validator->fails()) { 
			$response['Message'] = "Invalid Payload";
		  	$response['StatusCode'] = "1001";
		  	$error_messages = array();
	        foreach ($validator->errors()->all() as $messages) {
	            array_push($error_messages,$messages);
	        }     
		  	$response['errors'] = $error_messages;         
			return response()->json($response, 200);                        
		} 
		$input = $request->all();  
		$check_user = User::where('email',request('email'))->get();
		if($check_user->isEmpty()){
			$response['Message'] = "Dealer code is not registered";
		  	$response['StatusCode'] = "1006";
		   	return response()->json($response, 200); 
		}
		if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
			   $user = Auth::user(); 
			   $response['Message'] = "success";
			   $response['StatusCode'] = "1000";
			   $response['token'] =  $user->createToken('AppName')->accessToken; 
			   $response['DealerCode'] =  $user->email; 
			   return response()->json($response, $this->successStatus); 
		  } 
		  $response['Message'] = "Invalid Payload - Username/Password";
		  $response['StatusCode'] = "1007";
		  return response()->json($response, 200); 
	}
} 