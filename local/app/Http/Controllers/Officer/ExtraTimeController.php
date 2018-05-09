<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use DB;
use \Input as Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Officer as officer;

class ExtraTimeController extends Controller
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
        $ex = DB::table('meeting_open_extra')
                 ->get();
        $data = array(
            'exs' => $ex
        );
        return view('officer/extratime/index',$data);
    }

    public function add(Request $request){
        //dd(officer::dateFormatDB($request->date_start)." ".$request->ex_start.":00:00");
        if(!isset($request->id)){
            DB::table('meeting_open_extra')->insert([
                "extra_start" =>officer::dateFormatDB($request->date_start)." ".$request->ex_start.":00:00",
                "extra_end" =>officer::dateFormatDB($request->date_start)." ".$request->ex_end.":00:00",
            ]);
            return redirect('control/extratime/')
                    ->with('successMessage','เพิ่มอุปกรณ์สำเร็จ');
        }
        else{
            DB::table('meeting_open_extra')
                    ->where('extra_ID',$request->id)
                    ->update([
                        "extra_start" =>officer::dateFormatDB($request->date_start)." ".$request->ex_start.":00:00",
                        "extra_end" =>officer::dateFormatDB($request->date_start)." ".$request->ex_end.":00:00",
                ]);
            return redirect('control/extratime/')
                ->with('successMessage','เพิ่มอุปกรณ์สำเร็จ');
        }

    }
    public function delete($id){
        DB::table('meeting_open_extra')->where('extra_ID',$id)->delete();
        return redirect('control/extratime');
    }
}
