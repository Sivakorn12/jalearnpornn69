<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\func as func;

class ReserveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        date_default_timezone_set("Asia/Bangkok");
    }

    public function index()
    {
        $dataRoom = DB::table('meeting_room')
            ->join('meeting_type', 'meeting_room.meeting_type_ID', '=', 'meeting_type.meeting_type_ID')
            ->select('meeting_ID', 'meeting_name', 'meeting_size', 'meeting_pic', 'meeting_buiding', 'meeting_status', 'meeting_type_name')
            ->get();

        $data = array(
            'rooms' => $dataRoom
        );
        return view('ReserveRoom/index', $data);
    }

    public function ReservrRoom($id)
    {
        $resultData = func::selectReserve($id);

        $data = array(
            'rooms' => $resultData
        );
        return view('ReserveRoom/reserveOnID', $data);
    }

    public function reserveForm($id, $timeReserve)
    {
        $dataRoom = DB::table('meeting_room')
            ->where('meeting_ID', $id)
            ->first();
        
        $dataTimeReserve = DB::table('detail_booking')
            ->where('meeting_ID', $id)
            ->get();
        
        $time_reamain = func::CHECK_TIME_REAMAIN ($id, $timeReserve);

        $data = array(
            'room' => $dataRoom,
            'time_reserve' => $timeReserve.':00',
            'time_remain' => $time_reamain
        );
        return view('ReserveRoom/reserveForm', $data);
    }

    public function submitReserve(Request $req)
    {
        $time_start = date('Y-m-d'.' '.$req->time_reserve.':00');
        $time_out = date('Y-m-d'.' '.(substr($req->time_reserve, 0, 2) + $req->time_use).':00');

        if(isset($req)) {
            $id_insert = DB::table('booking')
                            ->insertGetId([
                                'status_ID' => 3,
                                'section_ID' => isset($req->section_id)? $req->section_id : null,
                                'institute_ID' => isset($req->institute_id)? $req->institute_id : null,
                                'user_ID' => $req->user_id,
                                'booking_name' => $req->user_name,
                                'booking_phone' => isset($req->user_tel)? $req->user_tel : null,
                                'booking_date' => date('Y-m-d H:i:s'),
                                'checkin' => date('Y-m-d')
                            ]);
            DB::table('detail_booking')
                    ->insert([
                        'booking_ID' => $id_insert,
                        'meeting_ID' => $req->meeting_id,
                        'detail_topic' => $req->detail_topic,
                        'detail_timestart' => $time_start,
                        'detail_timeout' => $time_out,
                        'detail_count' => $req->detail_count
                    ]);

            return redirect('reserve')->with('message', 'จองห้องสำเร็จ');
        }
        // "section_id" => "10101"
        // "detail_topic" => "testing topic"
        // "detail_count" => "20"
        // "user_tel" => "0123456789"
        // "user_id" => "2"
        // "user_name" => "Sivakorn Pranomsri"
        // "meeting_id" => "2"
        // "time_reserve" => "14:00"
        // "time_use" => "1"
    }

    public function CHECK_DATE_RESERVE (Request $req) {
        // $temp_date = explode('-', $req->date);
        // $date = ($temp_date[2] - 543).'-'.$temp_date[1].'-'.$temp_date[0];

        $time_use = func::GET_TIMEUSE (3);
        // dd($time_use);
        return response()->json(['success'=> $time_use]);
    }
}
