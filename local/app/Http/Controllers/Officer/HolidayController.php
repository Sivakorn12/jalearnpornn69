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
use Calendar;  
use App\Event;  
use App\Officer as officer;

class HolidayController extends Controller
{
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
        $events = [];  
        $holidays = DB::table('holiday')
                 ->get();
       
        $data = array(
            'holidays' => $holidays
        );
        return view('officer/holiday/index',$data);
    }
    
    public function add(Request $req){
        try{
            
            DB::table('holiday')->insert([
                "holiday_name" =>$req->holiday_name,
                "holiday_detail" =>$req->holiday_detail,
                "holiday_start" =>officer::dateFormatDB($req->date_start),
                "holiday_end"=> officer::dateFormatDB($req->date_end)
            ]);
            return redirect('control/holiday/')
                    ->with('successMessage','เพิ่มวันหยุดสำเร็จ');
        }catch (Exception $e) {
            return redirect('control/holiday/')
                    ->with('errorMesaage',$e);
        }
    }

    public function delete($id){
        
        DB::table('holiday')->where('holiday_ID',$id)->delete();
        return redirect('control/holiday/')
                    ->with('successMessage','ลบวันหยุดสำเร็จ');
    }
}
