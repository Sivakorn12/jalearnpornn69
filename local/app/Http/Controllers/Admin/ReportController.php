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
                           'booking.booking_name',
                           'dtb.detail_topic',
                           DB::raw('DATE_FORMAT(dtb.detail_timestart, "%H:%i")as start_time'),
                           DB::raw('DATE_FORMAT(dtb.detail_timeout, "%H:%i")as end_time')
                       )
                       ->join('detail_booking as dtb','booking.booking_ID','=','dtb.booking_ID')
                       ->join('meeting_room as r','dtb.meeting_ID','=','r.meeting_ID')
                       ->whereIn('booking.status_ID',[1,3])
                       ->whereBetween('dtb.detail_timestart',[$st,$end])
                       ->orderBy(
                           'booking.checkin',
                           'dtb.detail_timestart',
                           'dtb.detail_timeout'
                        )
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
