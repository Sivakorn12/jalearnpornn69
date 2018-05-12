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

class ReturnEquipController extends Controller
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
        $borrows = DB::table('booking')
                    ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
                    ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
                    ->join('borrow_booking','borrow_booking.booking_ID','=','booking.booking_ID')
                    ->orderBy('booking_date','desc')
                    ->select(
                        "booking.booking_ID",
                        "booking.status_ID",
                        "booking.section_ID",
                        "booking.booking_name",
                        "booking.booking_phone",
                        "booking.booking_date",
                        "booking.checkin",
                        "detail_booking.detail_topic",
                        "detail_booking.detail_timestart",
                        "detail_booking.detail_timeout",
                        "meeting_room.meeting_name",
                        "borrow_booking.borrow_ID",
                        "borrow_booking.borrow_date",
                        "borrow_booking.borrow_status"
                    )
                    ->get();
        $data = array(
            'datas' => $borrows
        );
        return view('officer/return-eq/index',$data);
    }
}
