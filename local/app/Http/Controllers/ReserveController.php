<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\func as func;
use Illuminate\Support\Facades\Validator;

class ReserveController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
        date_default_timezone_set("Asia/Bangkok");
    }

    public function index() {
        $dataRoom = DB::table('meeting_room')
            ->join('meeting_type', 'meeting_room.meeting_type_ID', '=', 'meeting_type.meeting_type_ID')
            ->select('meeting_ID', 'meeting_name', 'meeting_size', 'meeting_pic', 'meeting_buiding', 'meeting_status', 'meeting_type_name')
            ->get();

        $data = array(
            'rooms' => $dataRoom
        );
        return view('ReserveRoom/index', $data);
    }

    public function ReservrRoom($id) {
        $resultData = func::selectReserve($id);

        $data = array(
            'rooms' => $resultData
        );
        return view('ReserveRoom/reserveOnID', $data);
    }

    public function reserveForm($id, $timeReserve, $timeSelect) {
        $temp_date = explode('-', $timeSelect);
        $date_select = ($temp_date[2] - 543).'-'.$temp_date[1].'-'.$temp_date[0];

        $dataRoom = DB::table('meeting_room')
            ->where('meeting_ID', $id)
            ->first();
        
        $dataTimeReserve = DB::table('detail_booking')
            ->where('meeting_ID', $id)
            ->get();
        
        $time_reamain = func::CHECK_TIME_REAMAIN ($id, $timeReserve, $timeSelect);

        $data = array(
            'room' => $dataRoom,
            'time_reserve' => $timeReserve.':00',
            'time_remain' => $time_reamain,
            'time_select' => $date_select
        );
        return view('ReserveRoom/reserveForm', $data);
    }

    public function submitReserve(Request $req) {
        $msg = [
        'detail_topic.required' => "กรุณาระบุหัวข้อการประชุม",
        "detail_count.required" => "กรุณาระบุจำนวนผู้เข้าประชุม",
        'user_tel.required' => "กรุณาระบุเบอร์โทรติดต่อ"
        ];
    
        $rule = [
        'detail_topic' => 'required|alpha',
        'detail_count' => 'required|numeric',
        'user_tel' => 'required|numeric'
        ];

        $validator = Validator::make($req->all(),$rule,$msg);

        if ($validator->passes()) {
            if (is_numeric($req->user_tel) && is_string($req->detail_topic) && is_numeric($req->detail_count)) {
                $time_start = $req->time_select.' '.$req->time_reserve.':00';
                $time_out = $req->time_select.' '.(substr($req->time_reserve, 0, 2) + $req->time_use).':00';

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
                                        'checkin' => $req->time_select
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
            } else {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput($req->input());
            }
        } else {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput($req->input());
        }
    }

    public function CHECK_DATE_RESERVE (Request $req) {
        $temp_date = explode('-', $req->date);
        $date_select = ($temp_date[2] - 543).'-'.$temp_date[1].'-'.$temp_date[0];
        $check_weekend = date_create($date_select);
        $check_weekend = date_format($check_weekend, 'r');
        $date_now = date('Y-m-d');
        $constant_cancel_timeuse = ['1', '1', '1', '1', '1', '1', '1', '1'];
        $dataHolidays = DB::table('holiday')
                    ->where('holiday_start', '<=', $date_select)
                    ->where('holiday_end', '>=', $date_select)
                    ->first();

        if ($date_now > $date_select) {
            return response()->json(['error'=> 'ไม่สามารถจองห้องได้', 'constant_time' => $constant_cancel_timeuse]);
        } else {
            if (substr($check_weekend, 0, 3) == 'Sat' || substr($check_weekend, 0, 3) == 'Sun' || isset($dataHolidays)) {
                return response()->json(['error'=> 'ไม่สามารถจองห้องในวันหยุดได้', 'constant_time' => $constant_cancel_timeuse]);
            } else {
                $time_use = func::GET_TIMEUSE ($date_select, $req->roomid);
                return response()->json(['time_use'=> $time_use]);
            }
        }
    }
}
