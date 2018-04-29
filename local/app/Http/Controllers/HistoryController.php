<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;

class HistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        date_default_timezone_set("Asia/Bangkok");
    }

    public function index ()
    {
        $date_now = date('Y-m-d');
        $dataHistory = DB::table('booking')
            ->join('detail_booking', 'booking.booking_ID', '=', 'detail_booking.booking_ID')
            ->join('status_room', 'booking.status_ID', '=', 'status_room.status_ID')
            ->join('meeting_room', 'detail_booking.meeting_ID', '=', 'meeting_room.meeting_ID')
            ->where('booking.user_ID', Auth::user()->id)
            ->OrderBy('checkin', 'desc')
            ->get();
        
        $time_th = $time_start = $time_out = $check_dateCheckin = array();
        for ($index = 0; $index < sizeof($dataHistory); $index++) {
            $time_th[$index] = str_replace(date('Y'), date('Y') + 543, $dataHistory[$index]->booking_date);
            $time_start[$index] = substr($dataHistory[$index]->detail_timestart, -8, 5);
            $time_out[$index] = substr($dataHistory[$index]->detail_timeout, -8, 5);
            
            if ($dataHistory[$index]->checkin >= $date_now) {
                if ($dataHistory[$index]->status_ID == 3) {
                    $check_dateCheckin[$index] = 1;
                } else if ($dataHistory[$index]->status_ID == 1) {
                    $check_dateCheckin[$index] = 2;
                } else {
                    $check_dateCheckin[$index] = 0;
                }
            } else {
                $check_dateCheckin[$index] = 3;
            }
        }

        $data = array(
            'historys' => $dataHistory,
            'years_th' => $time_th,
            'time_start' => $time_start,
            'time_out' => $time_out,
            'check_date' => $check_dateCheckin
        );
        return view('History_user/index', $data);
    }
}
