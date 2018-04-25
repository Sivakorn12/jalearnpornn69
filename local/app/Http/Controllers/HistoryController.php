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
            ->get();

        $dataHistory = array(
            'historys' => $dataHistory
        );
        return view('History_user/index', $data);
    }
}
