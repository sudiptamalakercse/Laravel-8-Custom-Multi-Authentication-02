<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Blogger;
use App\Models\BloggerVerify; 
use Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\BloggerEmailVerification;


class BloggerController extends Controller
{
    public function create()
    {
        return view('blogger.register');
    }

   public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:bloggers'],
            'password' => ['required', Rules\Password::defaults()]
        ]);

       $blogger=Blogger::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        //email verify related code
        $last_id=$blogger->id;
        $token=$last_id.hash('sha256',Str::random(120));


        BloggerVerify::create([
              'blogger_id' => $last_id, 
              'token' => Hash::make($token)
            ]);
        
        //datas which will go with email
        $email_activation_link=route('blogger-verify', $token);
        $email_receiver_name=$blogger->name;
        $user_type='Blogger';

        $email_datas= [
            'email_activation_link'=>$email_activation_link,
            'email_receiver_name'=>$email_receiver_name,
            'user_type'=>$user_type,
        ];
        //end datas which will go with email

        //send email
        Mail::to($blogger->email)->send(new BloggerEmailVerification($email_datas));
        //end send email

         //end email verify related code

        //login
        $credentials=[
        'email' =>$request->email ,
        'password' =>$request->password
        ];

        if (Auth::guard('blogger')->attempt($credentials,false)) {

            $request->session()->regenerate();

            $url=route('dashboard-blogger');

            return redirect()->intended($url)
            ->with('message', 'Your Blogger Account is Registered Successfully & Please Verify Your Email Account!');
        }
        //end login 

        
    }

    public function login_blogger_form()
    {
        return view('blogger.login');
    }

    public function login_blogger(Request $request)
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

        if (Auth::guard('blogger')->attempt($credentials,$remember)) {

            $request->session()->regenerate();

            $url=route('dashboard-blogger');

            return redirect()->intended($url)
            ->with('message', 'You Are Logged In Successfully as Blogger!!');
        }
 
        return back()->withErrors([
            'email' => 'The Provided Credentials Do Not Match With Our Records.',
        ])->withInput();
    }

     public function dashboard_blogger()
    {
        return view('blogger.blogger_dashboard');
    }


    public function verify_account($token){
                 
        $matched=false;

        $verify_bloggers = BloggerVerify::get();

        foreach ($verify_bloggers as $verify_blogger) {
             if(Hash::check($token,$verify_blogger->token)){
                $matched=true;
                break;
            }
        }
  
        $message = 'Sorry Your Email Verification Link is Old or Incorrect. Please Try With New Email Verification Link!';
    
        if($matched==true){
            
            $blogger = $verify_blogger->blogger;
              
            if(!$blogger->is_email_verified) {

                //Authorization Check
                if($blogger->id!==auth('blogger')->user()->id){
                     abort(403);
                }
                //End Authorization Check
                
                $blogger->is_email_verified = 1;
                $blogger->save();
                $message = "Your e-mail is verified.";
            } else {
                $message = "Your e-mail is already verified.";
            }
        }

     if(auth('blogger')->user()!== null){
      return redirect()->route('dashboard-blogger')->with('message', $message);
     }
     else{
      return redirect()->route('login-blogger')->with('message', $message);
     }
    }

    public function verify_account_notice(){
    
    if(auth('blogger')->user()->is_email_verified==0){

      return view('blogger.email.verify_account_notice');

    }
    else{
      return redirect()->route('dashboard-blogger');
    }

    }

     public function verify_account_email_resend(Request $request){
        $blogger=$request->user('blogger');
        $blogger_verify=$blogger->BloggerVerify;
        $blogger_verify->delete();

        $blogger_id=$blogger->id;
        $token=$blogger_id.hash('sha256',Str::random(120));


        BloggerVerify::create([
              'blogger_id' => $blogger_id, 
              'token' => Hash::make($token)
            ]);

      //datas which will go with email
        $email_activation_link=route('blogger-verify', $token);
        $email_receiver_name=$blogger->name;
        $user_type='Blogger';

        $email_datas= [
            'email_activation_link'=>$email_activation_link,
            'email_receiver_name'=>$email_receiver_name,
            'user_type'=>$user_type,
        ];
        //end datas which will go with email

        //send email
        Mail::to($blogger->email)->send(new BloggerEmailVerification($email_datas));
        //end send email

         //end email verify related code
        return redirect()->back()
            ->with('message', 'Verification Email Sent & Please Verify Your Email Account!');
    }


}
