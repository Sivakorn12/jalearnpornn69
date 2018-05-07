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
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $events = [];  
         $colors = officer::colorEvents();
         $booking = DB::table('booking')
                   ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
                   ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
                   ->orderBy('checkin','desc')
                   ->get();
         
         if(isset($booking)){  
            foreach ($booking as $key => $value) {  
                $events[] = Calendar::event(  
                "[".$value->meeting_name."] ".$value->detail_topic,  
                false,  
                new \DateTime($value->detail_timestart),  
                new \DateTime($value->detail_timeout),
                $key,
                [
                    'backgroundColor' =>$colors[$value->meeting_ID-1],
                    'textColor' => '#fff',
                    'description' => "Event Description",
                ]
            );  
            }  
        }

        $calendar = Calendar::addEvents($events)->setOptions([
            'timeFormat'=> 'H:mm',
            'lang'=> 'th',
        ]); 
        $data = array(
            'calendar' => $calendar
        );

        return view('home',$data);
    }
}
