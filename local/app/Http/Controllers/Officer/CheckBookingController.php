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

class CheckBookingController extends Controller
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
        return view('officer/index');
    }

    public function indexReservation(){
        $booking = DB::table('booking')
                   ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
                   ->leftjoin('users','booking.user_ID','=','users.id')
                   ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
                   ->orderBy('detail_booking.detail_timestart','desc')
                   ->get();
        //dd($booking);
        $data = array(
            'bookings' => $booking
        );
        return view('officer/checkbooking/index',$data);
    }

    public function viewReservation($id){
        
        $html = officer::viewBooking($id);
        return response()->json(['html'=>$html]);
    }

    public function confirmReservation($id){
       $stateChange = DB::table('booking')
                    ->where('booking_ID', $id)
                    ->update([
                        'status_ID' => 1,
                        'approve_date' => date('Y-m-d H:i:s')
                    ]);
       return response()->json(['id'=>$id]);
    }
    
    public function cancelReservation($id){
        $stateChange = DB::table('booking')
                     ->where('booking_ID', $id)
                     ->update([
                        'status_ID' => 2,
                        'approve_date' => date('Y-m-d H:i:s')
                    ]);
        return response()->json(['id'=>$id]);
    }

    public function fetchTbBooking(){
        $bookings = DB::table('booking')
                   ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
                   ->leftjoin('users','booking.user_ID','=','users.id')
                   ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
                   ->orderBy('detail_booking.detail_timestart','desc')
                   ->get();
        $tbHtml = array();
        $statusBooking = array("all","wait","confirmed");
        for($i = 0; $i<count($statusBooking);$i++){
            array_push($tbHtml,officer::pushTableBooking($bookings,$statusBooking[$i]));
        }
        return response()->json([
            'tball'=> $tbHtml[0],
            'tbwait'=> $tbHtml[1],
            'tbconfirmed'=> $tbHtml[2],
        ]);
        
    }

    public function resetStatus(){
        DB::table('booking')
            ->update([
            'status_ID' => 3,
        ]);
    }

    
}
