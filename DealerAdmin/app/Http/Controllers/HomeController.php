<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Auth;
use Log;
use Validator;
use App\Model\Feedback;
use Exception;
use Mail;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $successStatus = 200;
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        \Log::info('Session All Value::');
        \Log::info(\Session::all());        
        if (\Session::exists('DealerAdmin') && \Session::exists('DealerCode')) {
            return redirect('inventory');
        }
        if (!\Session::has('dealertype')) {
          \Session::put('dealertype', 'mysql');
          \config()->set('database.default', 'mysql');
        }else{
         \config()->set('database.default', \Session::get('dealertype'));
        }
        return view('login');
    }

    public function processLogin(Request $request){
       $username = $request->get('email'); 
       $password = $request->get('password');
       $rules = array(
            'email'    => 'required|exists:users,email', // make sure the email is an actual email
            'password' => 'required' // password can only be alphanumeric and has to be greater than 3 characters
        );

        // run the validation rules on the inputs from the form
        $validator = \Validator::make($request->all(), $rules);
        //dd($validator);
        // if the validator fails, redirect back to the form
        /*if ($validator->fails()) {
            return redirect('login')
                ->withErrors($validator) // send back all errors to the login form
                ->withInput($request->except('password')); // send back the input (not the password) so that we can repopulate the form
        } */
       //dd($request->all());
      \Session::put('dealertype', 'mysql');
      \Log::info('Connected DBHOST::'.\DB::connection()->getConfig("host"));
       $check_exist = \DB::table('users')->where('email',$username)->get();
       \Log::info($check_exist);
       //dd($check_exist);
       if($check_exist->isEmpty()){
            \config()->set('database.default', 'mysql2');
            \Session::put('dealertype', 'mysql2');
            \Log::info('Connected DBHOST::'.\DB::connection()->getConfig("host"));
            $check_exist = \DB::table('users')->where('email',$username)->get();
            \Log::info($check_exist);
            if($check_exist->isEmpty()){
              return redirect('login')->withErrors([
                  'email' => 'Credentials does not match',
              ])->withInput($request->except('password'));
            }
       }
       $user = $check_exist->toArray();

       if(!Hash::check($password,$user[0]->password)){
            return redirect('login')->withErrors([
                'email' => 'Credentials does not match',
            ])->withInput($request->except('password'));
       }
        \Log::info('Logged successfully removing session values.');
       \Session::forget('logout');
        \Session::forget('DealerCode');
        \Session::forget('DealerAdmin');
        \Session::forget('DealerName');
        \Session::forget('ZipCode');
       //$DealerCode = $user[0]['email'];
        \Log::info('Set session values.');
        $DealerAdmin = array('DealerCode'=>$username);
        \Session::put('DealerCode',$username);
        \Session::put('DealerAdmin',$DealerAdmin);
        \Log::info(\Session::all());
        return redirect('inventory');
    }

    public function sso(Request $request){
            dd($request->all());
    }

    public function showChangePasswordForm(){
        return view('auth.changepassword');
    }

    public function changePassword(Request $request){
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }
        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
        }
        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
        ]);
        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();
        return redirect()->back()->with("success","Password changed successfully !");
    }

    public function helpandFaq(Request $request)
    {      
      return view('dealer-faq');
    }
          
public function Feedback(Request $request)
    {  
      $validator = Validator::make($request->all(),[
          'name' => 'required ',
          'email' => 'sometimes|required|email',
          'phone'=>'sometimes|required',
          'dealerid'=>'required ',
          'message'=>'required '
      ]);
      
      if($validator->fails()){
        $error  = collect($validator->messages())->flatten();
        return response()->json(['status' => False, 'message' => 'Validation Error', 'errors' => $error]);
      }
      $obj=new Feedback;
      $obj->name=$request->name;
      $obj->dealerid=$request->dealerid;
      $obj->email=$request->email;
      $obj->phone=$request->phone;
      $obj->message=$request->message;
      $status=$obj->save();
      if(!$status){
        $result['Message'] = 'something went wrong';
        $result['StatusCode'] = 1003;
      }else{
        $result['StatusCode'] = 1000;
        $result['Message'] = "Feedback submited";  
        if(config('emailsetting.feedbackEmail.is_enabled'))
        {
          $this->feedbackmail($request->all());
        }
      }
      return response()->json($result, $this->successStatus);
    }
  
  public function feedbackmail($data)
    {  
        \Mail::send('emails.dealer_feedback', ['data' => $data], function ($m) use ($data) {
            $m->from(config('emailsetting.feedbackEmail.from'), config('emailsetting.feedbackEmail.subject'));
            $m->to(config('emailsetting.feedbackEmail.to'))->subject(config('emailsetting.feedbackEmail.subject'));
        });
    }
}
