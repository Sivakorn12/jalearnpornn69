<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Calendar;  
use App\Event;
use App\Officer as officer;
use App\func as func;

class HomeController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
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
                   'textColor' => '#fff',
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
       return view('home',$data);
   }

   public function viewBooking($id){
        $html = officer::viewBooking($id);
        return response()->json(['html'=>$html]);
    }

    public function getNoti(){
        $datas = DB::table('booking')
                    ->select(
                        'booking.booking_ID',
                        'booking.booking_name',
                        'booking.checkin',
                        'booking.booking_date',
                        'detail_topic',
                        'meeting_name'
                    )
                    ->join('detail_booking as db','booking.booking_ID','=','db.booking_ID')
                    ->join('meeting_room as mr','db.meeting_ID','=','mr.meeting_ID')
                    ->where('status_ID',3)
                    ->where('booking.checkin','>=',date('Y-m-d H:i:s'))
                    ->where('db.detail_timestart','>=',date('Y-m-d H:i:s'))
                    ->orderBy('booking.booking_ID')
                    ->get();
        return response()->json(['data'=>$datas]);;
    }
}
