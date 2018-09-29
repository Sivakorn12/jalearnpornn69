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

        $imgs_room = array();
        for($index = 0; $index < sizeof($dataRoom); $index++) {
            $imgs_room[$index] = explode(',', $dataRoom[$index]->meeting_pic);
        }

        $data = array(
            'rooms' => $dataRoom,
            'imgs' => $imgs_room
        );
        return view('ReserveRoom/index', $data);
    }

    public function ReservrRoom($id) {
        $resultData = func::selectReserve($id);
        $imgs_room = array();
        $imgs_room = explode(',', $resultData->meeting_pic);

        $data = array(
            'rooms' => $resultData,
            'imgs' => $imgs_room
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

        $dataequipment = DB::table('equipment')
                                ->get();
        $time_reamain = func::CHECK_TIME_REAMAIN ($id, $timeReserve, $timeSelect);

        $data = array(
            'room' => $dataRoom,
            'time_reserve' => $timeReserve.':00',
            'time_remain' => $time_reamain,
            'time_select' => $date_select,
            'timeTH_select' => $timeSelect,
            'data_equipment' => $dataequipment
        );
        return view('ReserveRoom/reserveForm', $data);
    }

    public function submitReserve(Request $req) {
        $msg = [
        'detail_topic.required' => 'กรุณาระบุหัวข้อการประชุม',
        'detail_count.required' => 'กรุณาระบุจำนวนผู้เข้าประชุม',
        'user_tel.required' => 'กรุณาระบุเบอร์โทรติดต่อ'
        ];
    
        $rule = [
        'detail_topic' => 'required|string',
        'detail_count' => 'required|numeric',
        'user_tel' => 'required|numeric'
        ];

        $validator = Validator::make($req->all(),$rule,$msg);

        if ($validator->passes()) {
            if (is_numeric($req->user_tel) && is_string($req->detail_topic) && is_numeric($req->detail_count)) {
                $time_start = $req->time_select.' '.$req->time_reserve.':00';
                $time_out = $req->time_select.' '.(substr($req->time_reserve, 0, 2) + $req->time_use).':00';

                if(isset($req)) {
                    if (isset($req->hdnEq)) {
                        for($index = 0 ; $index < count($req->hdnEq); $index++){
                            $temp = explode(",",$req->hdnEq[$index]);
                            $data_em = DB::table('equipment')
                                        ->where('em_name', $temp[0])
                                        ->first();

                            $data_id_equipment[$index] = $data_em->em_ID;
                            $data_count_equipment[$index] = $temp[1];
                        }

                        $id_insert_booking = func::SET_DATA_BOOKING($req, $time_start, $time_out);
                        func::SET_DATA_BORROW($data_id_equipment, $data_count_equipment, $id_insert_booking, $req->time_select);
                    } else {
                        func::SET_DATA_BOOKING($req, $time_start, $time_out);
                    }
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
        $timenow = date('H');
        $empty_timeuse = array();
        $data_openExtra = DB::table('meeting_open_extra')
                                ->where(DB::Raw('SUBSTRING(extra_start, 1, 10)'), $date_select)
                                ->first();

        $time_start = 8;
        $time_end = 16;
        $time_reserve = array();

        if (isset($data_openExtra)) {
            $time_start = substr($data_openExtra->extra_start, -8, 2);
            $time_end = substr($data_openExtra->extra_end, -8, 2);
        }

        for ($index = $time_start; $index < $time_end; $index++) {
            if ($date_select > $date_now) {
                array_push($empty_timeuse, 0);
            } else {
                if ($timenow >= $index) {
                    array_push($empty_timeuse, 1);
                } else if ($timenow <= $index) {
                    array_push($empty_timeuse, 0);
                }
            }
            if (strlen($index) < 2) {
                array_push($time_reserve, '0'.$index.':00');
            } else {
                array_push($time_reserve, $index.':00');
            }
        }

        $dataHolidays = DB::table('holiday')
                            ->where('holiday_start', '<=', $date_select)
                            ->where('holiday_end', '>=', $date_select)
                            ->first();

        if ($date_now > $date_select) {
            return response()->json(['error'=> 'ไม่สามารถจองห้องได้ย้อนหลังได้']);
        } else {
            if (substr($check_weekend, 0, 3) == 'Sat' || substr($check_weekend, 0, 3) == 'Sun' || isset($dataHolidays)) {
                return response()->json(['error'=> 'ไม่สามารถจองห้องในวันหยุดได้']);
            } else {
                $time_empty = func::GET_TIMEUSE ($date_select, $time_reserve, $empty_timeuse, $req->roomid);
                return response()->json(['time_empty'=> $time_empty, 'time_reserve' => $time_reserve]);
            }
        }
    }

    public function EDIT_DATA_RESERVE ($reserveId, $timeSelect) {
        $dataReserve = DB::table('booking')
            ->join('detail_booking', 'booking.booking_ID', '=', 'detail_booking.booking_ID')
            ->join('meeting_room', 'detail_booking.meeting_ID', '=', 'meeting_room.meeting_ID')
            ->where('booking.booking_ID', $reserveId)
            ->first();

        $dataBorrow = DB::table('borrow_booking')
            ->join('detail_borrow', 'borrow_booking.borrow_ID', '=', 'detail_borrow.borrow_ID')
            ->where('borrow_booking.booking_ID', $reserveId)
            ->where('detail_borrow.borrow_count', '!=', '0')
            ->get();

        $tmp_timeStart = substr($dataReserve->detail_timestart, -8, -6);
        $tmp_timeEnd = substr($dataReserve->detail_timestart, -8, -6);
        $time_reamain = func::CHECK_TIME_REAMAIN ($dataReserve->meeting_ID, $tmp_timeStart, $dataReserve->checkin);

        $data = array(
            'room_id' => $dataReserve->meeting_ID,
            'room_name' => $dataReserve->meeting_name,
            'time_reserve' => $tmp_timeStart.':00',
            'time_remain' => $time_reamain,
            'time_select' => $dataReserve->checkin,
            'dataReserve' => $dataReserve,
            'dataBorrow' => $dataBorrow
        );

        return view('ReserveRoom/reserveFormEdit', $data);
    }

    public function SET_EDIT_DATA_RESERVE (Request $req) {
        $msg = [
        'detail_topic.required' => 'กรุณาระบุหัวข้อการประชุม',
        'detail_count.required' => 'กรุณาระบุจำนวนผู้เข้าประชุม',
        'user_tel.required' => 'กรุณาระบุเบอร์โทรติดต่อ'
        ];
    
        $rule = [
        'detail_topic' => 'required|string',
        'detail_count' => 'required|numeric',
        'user_tel' => 'required|numeric'
        ];

        $validator = Validator::make($req->all(),$rule,$msg);

        if ($validator->passes()) {
            if (is_numeric($req->user_tel) && is_string($req->detail_topic) && is_numeric($req->detail_count)) {
                $time_start = $req->time_select.' '.$req->time_reserve.':00';
                $time_out = $req->time_select.' '.(substr($req->time_reserve, 0, 2) + $req->time_use).':00';

                if(isset($req)) {
                    if (isset($req->hdnEq)) {
                        for($index = 0 ; $index < count($req->hdnEq); $index++){
                            $temp = explode(",",$req->hdnEq[$index]);
                            $data_em = DB::table('equipment')
                                        ->where('em_name', $temp[0])
                                        ->first();

                            $data_id_equipment[$index] = $data_em->em_ID;
                            $data_count_equipment[$index] = $temp[1];
                        }

                        $id_insert_booking = func::UPDATE_DATA_BOOKING($req, $time_start, $time_out);
                        func::UPDATE_DATA_BORROW($data_id_equipment, $data_count_equipment, $id_insert_booking, $req->time_select, $req->borrow_id);
                    } else {
                        func::UPDATE_DATA_BOOKING($req, $time_start, $time_out);
                    }
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

    public function testController (Request $req) {
        dd(json_decode($req->timeSelect));
    }
}
