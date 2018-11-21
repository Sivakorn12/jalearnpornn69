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

    /// room extratime open
    public function indexRoomExOpen(){
        $ex = DB::table('meeting_over_time')
                ->join('meeting_room as m','m.meeting_ID','=','meeting_over_time.meeting_id')
                 ->get();
        $room = DB::table('meeting_room')
                 ->get();
        $data = array(
            'room' => $room,
            'mot' => $ex
        );
        return view('officer/room_open/index',$data);
    }

    public function saveRoomExOpen(Request $req){
        $dt_arr = explode(" - ", $req->daterange);
        $st_dt = str_replace('/', '-', $dt_arr[0]);
        $end_dt = str_replace('/', '-', $dt_arr[1]);
        $st_dt = date('Y-m-d', strtotime($st_dt ));
        $end_dt = date('Y-m-d', strtotime($end_dt ));
        if(!isset($req->id)){
            DB::table('meeting_over_time')->insert([
                "meeting_id" => $req->room_id,
                "start_date" => $st_dt.' '.$req->ex_start.":00:00",
                "end_date" => $end_dt.' '.$req->ex_end.":00:00"
            ]);
            return redirect('control/room_open/')
                    ->with('successMessage','เพิ่มเวลาการใช้งานพิเศษสำเร็จ');
        }
        else{
            DB::table('meeting_over_time')
                    ->where('id',$req->id)
                    ->update([
                        "meeting_id" => $req->room_id,
                        "start_date" => $st_dt.' '.$req->ex_start.":00:00",
                        "end_date" => $end_dt.' '.$req->ex_end.":00:00"
                ]);
            return redirect('control/room_open/')
                ->with('successMessage','แก้ไขเวลาการใช้งานพิเศษสำเร็จ');
        }
        
    
    }

    public function deleteRoomExOpen($id){
        DB::table('meeting_over_time')->where('id',$id)->delete();
        return redirect('control/room_open');
    }
}
