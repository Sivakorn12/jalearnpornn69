<?php

namespace App\Http\Controllers\Officer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Session;
use Auth;
use DB;
use \Input as Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Officer as officer;

class EmailController extends Controller
{
    //
    //
    public function __construct(){
        
        //$this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if(empty(Auth::user())) return redirect('/');
            if(Auth::user()->user_status!="superuser"){
                return redirect('/');
            }  
            return $next($request);
        });
        date_default_timezone_set("Asia/Bangkok");
        
    }

    public function index(){
        $type_email = DB::table('type_email')
                 ->get();
        $data = array(
            'type_email' => $type_email
        );
        return view('officer/email_domain/index',$data);
    }

    public function save(Request $req){
        //dd($req->all());
        if($req->Type_Email_ID != ''){
            // update
            DB::table('type_email')
                ->where('Type_Email_ID',$req->Type_Email_ID)
                ->update([
                    'Name_Type' => $req->Name_Type
                ]);
            return redirect('control/email_domain/')
                ->with('successMessage','แก้ไขข้อมูลคณะสำเร็จ');
        }
        else{
            // new
            DB::table('type_email')
                ->insert([
                    'Name_Type' => $req->Name_Type
                ]);
            return redirect('control/email_domain/')
                ->with('successMessage','เพิ่มข้อมูลคณะสำเร็จ');
        }
    }

    public function delete($id){
        DB::table('type_email')->where('Type_Email_ID',$id)->delete();
        return redirect('control/email_domain');
        
    }
}
