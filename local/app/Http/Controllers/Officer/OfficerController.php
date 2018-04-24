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

class OfficerController extends Controller
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
                   ->join('users','booking.user_ID','=','users.id')
                   ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
                   ->orderBy('booking_date','desc')
                   ->get();
        //dd($booking);
        $data = array(
            'bookings' => $booking
        );
        return view('officer/reservation/index',$data);
    }

    public function viewReservation($id){
        $booking = DB::table('booking')
                    ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
                    ->join('users','booking.user_ID','=','users.id')
                    ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
                    ->where('booking.booking_ID',$id)
                    ->first();
        $html = '<table cellpadding=3>
                    <tr>
                        <td width="120"><b>รหัสการจอง</b></td>
                        <td>'.$booking->booking_ID.'</td>
                    </tr>
                    <tr>
                        <td width="100"><b>ห้อง</b></td>
                        <td>'.$booking->meeting_name.'</td>
                    </tr>
                    <tr>
                        <td width="100"><b>วันที่</b></td>
                        <td>'.$booking->checkin.'</td>
                    </tr>
                    <tr>
                        <td width="100"><b>เวลา</b></td>
                        <td>'.substr($booking->detail_timestart, -8,5) .' - '. substr($booking->detail_timeout, -8,5).'</td>
                    </tr>
                    <tr>
                        <td width="100"><b>ผู้จอง</b></td>
                        <td>'.$booking->user_name.'</td>
                    </tr>
                    <tr>
                        <td width="100"><b>วันเวลาที่จอง</b></td>
                        <td>'.$booking->booking_date.'</td>
                    </tr>
                </table>';
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
                   ->join('users','booking.user_ID','=','users.id')
                   ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
                   ->orderBy('booking_date','desc')
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
