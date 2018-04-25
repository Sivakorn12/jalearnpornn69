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
        $dataHistory = DB::table('booking')
            ->join('detail_booking', 'booking.booking_ID', '=', 'detail_booking.booking_ID')
            ->join('status_room', 'booking.status_ID', '=', 'status_room.status_ID')
            ->join('meeting_room', 'detail_booking.meeting_ID', '=', 'meeting_room.meeting_ID')
            ->where('booking.user_ID', Auth::user()->id)
            ->OrderBy()
            ->get();

        $data = array(
            'historys' => $dataHistory
        );
        return view('History_user/index', $data);
    }
}
