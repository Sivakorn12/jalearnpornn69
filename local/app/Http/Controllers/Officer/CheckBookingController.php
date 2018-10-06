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
                   ->leftjoin('borrow_booking','booking.booking_ID','=','borrow_booking.booking_ID')
                   ->orderBy('detail_booking.detail_timestart','desc')
                   ->get();
        //dd($booking);
        $booking = $this->map_equipment($booking);
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
        $equips = DB::table('booking')
                    ->select(
                        'eq.em_ID',
                        'dbr.borrow_count',
                        'eq.em_count'
                    )
                    ->join('borrow_booking as bbr','booking.booking_ID','=','bbr.booking_ID')
                    ->join('detail_borrow as dbr','bbr.borrow_ID','=','dbr.borrow_ID')
                    ->join('equipment as eq','dbr.equiment_ID','=','em_ID')
                    ->where('booking.booking_ID',$id)
                    ->get();
        foreach($equips as $eq){
            DB::table('equipment')->where('em_ID', $eq->em_ID)
            ->update([
                'em_count' => ($eq->em_count-$eq->borrow_count)
            ]);
        }
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
        $bookings = $this->map_equipment($bookings);
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

    public function map_equipment($booking){
        $eqm = DB::table('borrow_booking')
                    ->select(
                        'borrow_booking.booking_ID',
                        DB::raw("GROUP_CONCAT(
                            DISTINCT CONCAT(eq.em_name,' x ',dbw.borrow_count) 
                            ) as eq_list")
                    )
                    ->join('detail_borrow as dbw','borrow_booking.borrow_ID','=','dbw.borrow_ID')
                    ->join('equipment as eq','dbw.equiment_ID','=','eq.em_ID')
                    ->groupBy('borrow_booking.booking_ID')
                    ->get();
        foreach( $booking as $key => &$bk){
            $bk->eqiupment_list = '';
            foreach($eqm as $eq){
                if($bk->booking_ID == $eq->booking_ID){
                    $bk->eqiupment_list = $eq->eq_list;
                }
            }
        }
        return $booking;
    }

    
}
