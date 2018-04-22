<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Auth;
use DB;
use \Input as Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
        
    }
    public function index(){
        return view('officer/index');
    }

    public function indexReservation(){
        $booking = DB::table('booking')
                   ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
                   ->join('users','booking.user_ID','=','users.id')
                   ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
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
                </table>';
        return response()->json(['html'=>$html]);
    }

    public function confirmReservation($id){
       $stateChange = DB::table('booking')
                    ->where('booking_ID', $id)
                    ->update(['status_ID' => 1]);
       return response()->json(['id'=>$id]);
    }
    
    public function cancelReservation($id){
        $stateChange = DB::table('booking')
                     ->where('booking_ID', $id)
                     ->update(['status_ID' => 2]);
        return response()->json(['id'=>$id]);
    }

    
}
