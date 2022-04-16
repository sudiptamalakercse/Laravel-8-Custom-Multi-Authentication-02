<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Admin;
use App\Models\AdminVerify;
use App\Models\PasswordReset;
use Str;
use App\Mail\AdminEmailVerification;
use App\Mail\AdminPasswordReset;
use Illuminate\Support\Facades\Mail;


class AdminController extends Controller
{
    public function create()
    {
        return view('admin.register');
    }

   public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', Rules\Password::defaults()]
        ]);

        $admin=Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        //email verify related code
        $last_id=$admin->id;
        $token=$last_id.hash('sha256',Str::random(120));


        AdminVerify::create([
              'admin_id' => $last_id, 
              'token' => Hash::make($token)
            ]);
        
        //datas which will go with email
        $email_activation_link=route('admin-verify', $token);
        $email_receiver_name=$admin->name;
        $user_type='Admin';

        $email_datas= [
            'email_activation_link'=>$email_activation_link,
            'email_receiver_name'=>$email_receiver_name,
            'user_type'=>$user_type,
        ];
        //end datas which will go with email

        //send email
        Mail::to($admin->email)->send(new AdminEmailVerification($email_datas));
        //end send email

         //end email verify related code

        //login
        $credentials=[
        'email' =>$request->email ,
        'password' =>$request->password
        ];

        if (Auth::guard('admin')->attempt($credentials,false)) {

            $request->session()->regenerate();

            $url=route('dashboard-admin');

            return redirect()->intended($url)
            ->with('message', 'Your Admin Account is Registered Successfully & Please Verify Your Email Account!');
        }
        //end login 
    }

    public function login_admin_form()
    {
        return view('admin.login');
    }

    public function login_admin(Request $request)
    {
        $remember=$request->remember;
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        if($remember===null)
        {
           $remember=false; 
        }
        else
        {
          $remember=true; 
        }

        if (Auth::guard('admin')->attempt($credentials,$remember)) {

            $request->session()->regenerate();

            $url=route('dashboard-admin');

            return redirect()->intended($url)
            ->with('message', 'You Are Logged In Successfully as Admin!!');
        }
 
        return back()->withErrors([
            'email' => 'The Provided Credentials Do Not Match With Our Records.',
        ])->withInput();
    }

     public function dashboard_admin()
    {
        return view('admin.admin_dashboard');
    }


    public function verify_account($token){
                 
        $matched=false;

        $verify_admins = AdminVerify::get();

        foreach ($verify_admins as $verify_admin) {
             if(Hash::check($token,$verify_admin->token)){
                $matched=true;
                break;
            }
        }
  
        $message = 'Sorry Your Email Verification Link is Old or Incorrect. Please Try With New Email Verification Link!';
    
        if($matched==true){
            
            $admin = $verify_admin->admin;
              
            if(!$admin->is_email_verified) {

                //Authorization Check
                if($admin->id!==auth('admin')->user()->id){
                     abort(403);
                }
                //End Authorization Check
                
                $admin->is_email_verified = 1;
                $admin->save();
                $message = "Your e-mail is verified.";
            } else {
                $message = "Your e-mail is already verified.";
            }
        }

     if(auth('admin')->user()!== null){
      return redirect()->route('dashboard-admin')->with('message', $message);
     }
     else{
      return redirect()->route('login-admin')->with('message', $message);
     }
    }
    
   
    public function verify_account_notice(){
    
    if(auth('admin')->user()->is_email_verified==0){

      return view('admin.email.verify_account_notice');

    }
    else{
      return redirect()->route('dashboard-admin');
    }

    }

    public function verify_account_email_resend(Request $request){
        $admin=$request->user('admin');
        $admin_verify=$admin->AdminVerify;
        $admin_verify->delete();

        $admin_id=$admin->id;
        $token=$admin_id.hash('sha256',Str::random(120));


        AdminVerify::create([
              'admin_id' => $admin_id, 
              'token' => Hash::make($token)
            ]);

      //datas which will go with email
        $email_activation_link=route('admin-verify', $token);
        $email_receiver_name=$admin->name;
        $user_type='Admin';

        $email_datas= [
            'email_activation_link'=>$email_activation_link,
            'email_receiver_name'=>$email_receiver_name,
            'user_type'=>$user_type,
        ];
        //end datas which will go with email

        //send email
        Mail::to($admin->email)->send(new AdminEmailVerification($email_datas));
        //end send email

         //end email verify related code
        return redirect()->back()
            ->with('message', 'Verification Email Sent & Please Verify Your Email Account!');
    }

  
  public function forgot_password () {
    return view('admin.forgot_password');
  }

public function forgot_password_handle(Request $request) {
    $request->validate(['email' => 'required|email']);
    $email=$request->email;
    $admin=Admin::where('email',$email)->first();
    $mess='';
    if($admin!=null){
    
    $password_reset=PasswordReset::latest()->first();
    
    if($password_reset==null){
     $password_reset_last_id=1; 
    }
    else{
     $password_reset_last_id=$password_reset->id+1;
    }
   
    $token=$password_reset_last_id.hash('sha256',Str::random(120));
   
    PasswordReset::create([
              'email' => $email, 
              'token' => Hash::make($token),
              'user_type' =>'Admin'
            ]);
       
        //datas which will go with email
        $admin_password_reset_link=route('admin-password-reset',['token'=>$token,'email'=>$email]);
    
        $password_resetter_name=$admin->name;
        $user_type='Admin';

        $email_datas= [
            'admin_password_reset_link'=>$admin_password_reset_link,
            'password_resetter_name'=>$password_resetter_name,
            'user_type'=>$user_type,
        ];
        //end datas which will go with email

        //send email
        Mail::to($email)->send(new AdminPasswordReset($email_datas));
        //end send email

         //end email verify related code

    $mess='We Sent You A Password Reset Link to Your Email!';
    }
    else{
     $mess='We Do Not Find This Email!';
    }

    return redirect()->back()->with('message', $mess); 
}

public function reset_password($token,$email) {
    return view('admin.reset_password', ['token' => $token,'email' =>$email]);
}


public function reset_password_handle(Request $request) {
$request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);
    
    $email=$request->email;
    $password=$request->password;
    $token=$request->token;

    $mess='';
    $token_matched=false;
    $PasswordReset_id=null;
    $all_ok=false;

    $PasswordResets=PasswordReset::get(); 

    foreach ($PasswordResets as $PasswordReset) {
             if(Hash::check($token,$PasswordReset->token)){
                $token_matched=true;
                $PasswordReset_id=$PasswordReset->id;
                break;
            }
        }

    if($token_matched==true){
      $PasswordReset=PasswordReset::find($PasswordReset_id);        
      if($PasswordReset->email==$email){
         if($PasswordReset->user_type=='Admin'){
          
          $all_ok=true;

         }else{
           $mess="User Type Is Not Matched!";
         }

      }
      else{
         $mess="Email Is Not Matched!"; 
      }
    }
    else{
      $mess="Token Is Not Matched or Old!";
    }

    if($all_ok==true){

      //Update Password
      $admin=Admin::where('email',$email)->first();
      $admin->password = Hash::make($password);
      $admin->save();

      //delete reset_password_table record
      PasswordReset::where('email',$email)
                     ->where('user_type','Admin')
                     ->delete();

       //login
        $credentials=[
        'email' =>$email ,
        'password' =>$password
        ];

        if (Auth::guard('admin')->attempt($credentials,false)) {

            $request->session()->regenerate();

            $url=route('dashboard-admin');

            $request->session()->put('message_password_reset', 'Your Password Is Successfully Changed!');
            
            return redirect()->intended($url);
            
            
        }
    }
    else{
        return redirect()->back()->with('message', $mess);
    }
}


}
