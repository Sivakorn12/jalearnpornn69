<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ReportController extends Controller
{
    //

    public function reportReservation(Request $req){
        if(isset($req->start_dt)){
            $st = $req->start_dt." 00:00:00";
            $end = $req->end_dt." 23:59:59";
            $data = DB::table('booking')
                       ->select(
                           'booking.checkin',
                           'r.meeting_ID',
                           'r.meeting_name',
                           DB::raw('count(booking.booking_ID) as total_reserve')
                       )
                       ->join('detail_booking as dtb','booking.booking_ID','=','dtb.booking_ID')
                       ->join('meeting_room as r','dtb.meeting_ID','=','r.meeting_ID')
                       ->where('booking.status_ID','<>',4)
                       ->whereBetween('dtb.detail_timestart',[$st,$end])
                       ->groupBy(
                        'booking.checkin',
                        'r.meeting_ID',
                        'r.meeting_name'
                       )
                       ->orderBy('booking.checkin')
                       ->get();
            return view('AdminControl.report.reservation',[
                'showtb' => true,
                'datas' => $data
            ]);
        }
        else{
            return view('AdminControl.report.reservation',[
                'showtb' => false
            ]);
        }
    }
}
