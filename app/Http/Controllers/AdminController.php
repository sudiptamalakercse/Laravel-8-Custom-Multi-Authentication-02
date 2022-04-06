<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Admin;
use App\Models\AdminVerify;
use Str;
use App\Mail\AdminEmailVerification;
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

}
