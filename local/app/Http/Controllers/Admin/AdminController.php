<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use DB;
use Calendar;  
use App\Event;
use Artisan;
use Storage;
use App\Officer as officer;
use App\func as func;

class AdminController extends Controller
{
    public function __construct() {
        $this->middleware(function ($request, $next) {
            if(empty(Auth::user())) return redirect('/');
            if(Auth::user()->user_status!="admin"){
                return redirect('/');
            }  
            return $next($request);
        });
    }

    public function index(){ 
        $events = [];  
        $colors = officer::colorEvents();
        $booking = DB::table('booking')
                  ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
                  ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
                  ->where('status_ID','<>','2')
                  ->orderBy('checkin','desc')
                  ->get();
        
        if(isset($booking)){  
           foreach ($booking as $key => $value) {  
               $events[] = Calendar::event(  
               "[".$value->meeting_name."] ".$value->detail_topic,  
               false,  
               new \DateTime($value->detail_timestart),  
               new \DateTime($value->detail_timeout),
               $value->booking_ID,
               [
                   'backgroundColor' =>$colors[$value->meeting_ID-1],
                   'textColor' => '#000',
                   'description' => "Event Description",
                   'className'=> 'moreBorder'
               ]
           );  
           }  
       } 
       $calendar = Calendar::addEvents($events)->setOptions([
           'timeFormat'=> 'H:mm',
           'lang'=> 'th',
       ])->setCallbacks([ //set fullcalendar callback options (will not be JSON encoded)
           'eventClick' => 'function(calEvent, jsEvent, view) {detailreserve(calEvent);}'
       ]); 
       $data = array(
           'calendar' => $calendar
       );
       return view('AdminControl/index',$data);
   }

   public function viewBooking($id){
        $html = officer::viewBooking($id);
        return response()->json(['html'=>$html]);
    }

    public function GET_USERS() {
        $data_user = DB::table('users')
                        ->get();

        $data = array(
            'users' => $data_user
        );

        return view('AdminControl/manageuser/index', $data);
    }

    public function GET_FORM_STATUS (Request $req) {
        if (isset($req->id)) {
            $html = '
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <form class="form-horizontal" action="'.url("admin/setstatusUser").'" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="'.$req->id.'">
                        <div class="form-group">
                            <label class="col-sm-5 control-label">สถานะผู้ใช้งาน</label>
                            <div class="col-sm-5">
                                <select class="sectionlist form-control" name="user_status">
                                    <option value="user">ผู้ใช้งานทั่วไป</option>
                                    <option value="superuser">เจ้าหน้าที่</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="_token" id="csrf-token" value="'.Session::token().'">
                        <div class="form-group">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success">ยืนยันการเปลี่ยนสถานะ</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-1"></div>
            </div>
            ';
            return response()->json(['html'=> $html]);
        }
    }

    public function SET_STATUS_USER (Request $req) {
        $data_user = DB::table('users')
                            ->where('id', $req->user_id)
                            ->first();

        if ($data_user->user_status != $req->user_status) {
            $data_user = DB::table('users')
                            ->where('id', $req->user_id)
                            ->update(['user_status' => $req->user_status]);

            return redirect('admin/manageUser')->with('message', 'เปลี่ยนแปลงสถานะสำเร็จ');
        } else {
            return redirect('admin/manageUser')->with('message', 'สถานะไม่มีการเปลี่ยนแปลง');
        }
    }

    public function backup_database(){
        Artisan::call('db:backup');
        $output = Artisan::output();
        $files_db = Storage::disk('db_backup')->allFiles();
        if(sizeof($files_db) >0){
            $filename = $files_db[sizeof($files_db)-1];
            $file = Storage::disk('db_backup')->getDriver()->getAdapter()->applyPathPrefix($filename);
            return response()->download($file ,env('DB_DATABASE').'_backup_'.$filename);
        }
    
    }
}
