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
                   ->orderBy('booking.booking_date','desc')
                   ->get();
        //dd($booking);
        $booking = $this->map_equipment($booking);
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
        DB::table('borrow_booking')
            ->where('booking_ID', $id)
            ->update([
                'borrow_status' => 1
            ]);
       return response()->json(['id'=>$id]);
    }
    
    public function cancelReservation(Request $req){
        $stateChange = DB::table('booking')
                     ->where('booking_ID', $req->id)
                     ->update([
                        'status_ID' => 2,
                        'approve_date' => date('Y-m-d H:i:s'),
                        'comment' => $req->comment
                    ]);
        return response()->json(['id'=>$req->id]);
    }

    public function fetchTbBooking(){
        $bookings = DB::table('booking')
                   ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
                   ->leftjoin('users','booking.user_ID','=','users.id')
                   ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
                   ->orderBy('booking.booking_date','desc')
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
        foreach( $booking as $key => &$bk){
            $brw = DB::table('borrow_booking')->where('booking_ID',$bk->booking_ID)->first();
            $bk->eqiupment_list = '';
            if(isset($brw)){
                $eqm = DB::table('detail_borrow')
                    ->select(
                        'equipment.em_name',
                        DB::raw('sum(detail_borrow.borrow_count) as borrow_count')
                        )
                    ->join('equipment','equipment.em_ID','=','detail_borrow.equiment_ID')
                    ->where('detail_borrow.borrow_ID',$brw->borrow_ID)
                    ->groupBy('equipment.em_name')
                    ->get();
                $t = '';
                $tmp = '';
                
                foreach($eqm as $key => $eq){
                    if($key != 0)$tmp.=',';
                        $tmp.=$eq->em_name.' x '.$eq->borrow_count;
                }
                $bk->eqiupment_list = $tmp;
            }
            
            
        }
        
        return $booking;
    }

    
}
