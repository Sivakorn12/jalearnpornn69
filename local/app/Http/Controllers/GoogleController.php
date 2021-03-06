<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Auth;
use DB;
use Exception;
use Session;
use Socialite;
use App\func as func;

class GoogleController extends Controller
{
    use ThrottlesLogins;
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function __construct()
    {
        date_default_timezone_set("Asia/Bangkok");
    }
    
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('google')->user();
        $user_array = explode("@", $user->email);
        $domain_arr = func::getEmailDomainToArray();
        $domain = '@'.$user_array[1];

        if (in_array($domain, $domain_arr)) {
            $check_user = DB::table("users")
                            ->where("user_email", '=', $user->email)
                            ->first();
            if (!isset($check_user)) {
               $id_insert = DB::table('users')
                    ->insertGetId([
                        'user_name' => $user->name,
                        'user_email' => $user->email
                ]);

                Auth::loginUsingId($id_insert);

            } else {
                Auth::loginUsingId($check_user->id);
            }
            if(Auth::user()->user_status=="admin"){
                return redirect("/admin");
            }
            if(Auth::user()->user_status=="superuser"){
                return redirect("/control");
            }
            if(Auth::user()->user_status=="user"){
                return redirect("/home");
            }
            
        } else {
            return redirect("/login");
        }
    }
}