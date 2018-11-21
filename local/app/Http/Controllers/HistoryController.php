<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Officer as officer;

class HistoryController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        date_default_timezone_set("Asia/Bangkok");
    }

    public function index () {
        $date_now = date('Y-m-d');
        $dataHistory = DB::table('booking')
                            ->join('detail_booking', 'booking.booking_ID', '=', 'detail_booking.booking_ID')
                            ->join('status_room', 'booking.status_ID', '=', 'status_room.status_ID')
                            ->join('meeting_room', 'detail_booking.meeting_ID', '=', 'meeting_room.meeting_ID')
                            ->where('booking.user_ID', Auth::user()->id)
                            ->where('booking.status_ID', '!=', 4)
                            ->OrderBy('booking_date', 'desc')
                            ->get();

        $dataBorrow = $this->GET_HISTORY_BORROW();
        
        $time_th = $time_start = $time_out = $check_dateCheckin = $checkin_date = $checkin_borrow = array();
        for ($index = 0; $index < sizeof($dataHistory); $index++) {
            $time_th[0][$index] = str_replace(date('Y'), date('Y') + 543, substr($dataHistory[$index]->booking_date, 0, 16));
            $temp_date = explode(" ", $dataHistory[$index]->booking_date);
            $temp_date = explode("-", $temp_date[0]);
            $time_th[0][$index] = $temp_date[2]."-".$temp_date[1]."-".($temp_date[0] + 543)." ".substr($dataHistory[$index]->booking_date, 11, 5);
            $time_start[$index] = substr($dataHistory[$index]->detail_timestart, -8, 5);
            $time_out[$index] = substr($dataHistory[$index]->detail_timeout, -8, 5);
            $temp = explode('-', $dataHistory[$index]->checkin);
            $checkin_date[0][$index] = $temp[2].'-'.$temp[1].'-'.($temp[0] + 543);
            
            if ($dataHistory[$index]->checkin >= $date_now) {
                if ($dataHistory[$index]->status_ID == 1) {
                    $check_dateCheckin[0][$index] = 1;
                } else if ($dataHistory[$index]->status_ID == 2) {
                    $check_dateCheckin[0][$index] = 2;
                } else if ($dataHistory[$index]->status_ID == 3) {
                    $check_dateCheckin[0][$index] = 3;
                }
            } else {
                $check_dateCheckin[0][$index] = 4;
            }
        }

        for ($index = 0; $index < sizeof($dataBorrow); $index++) {
            $temp_checkin = explode('-', $dataBorrow[$index]->checkin);
            $temp_borrow_date = explode('-', $dataBorrow[$index]->borrow_date);
            $checkin_date[1][$index] = $temp_checkin[2].'-'.$temp_checkin[1].'-'.($temp_checkin[0] + 543);
            $checkin_borrow[$index] = $temp_borrow_date[2].'-'.$temp_borrow_date[1].'-'.($temp_borrow_date[0] + 543);
            
            if ($dataBorrow[$index]->checkin >= $date_now) {
                if ($dataBorrow[$index]->borrow_status == 1) {
                    $check_dateCheckin[1][$index] = 1;
                } else if ($dataBorrow[$index]->borrow_status == 2) {
                    $check_dateCheckin[1][$index] = 2;
                } else if ($dataBorrow[$index]->borrow_status == 3) {
                    $check_dateCheckin[1][$index] = 3;
                }
            } else {
                $check_dateCheckin[1][$index] = 4;
            }
        }

        $data = array(
            'historys' => $dataHistory,
            'years_th' => $time_th,
            'time_start' => $time_start,
            'time_out' => $time_out,
            'checkin_date' => $checkin_date,
            'check_date' => $check_dateCheckin,
            'history_borrow' => $dataBorrow,
            'checkin_borrow' => $checkin_borrow
        );
        return view('History_user/index', $data);
    }

    public function DELETE_RESERVE (Request $req) {
        $data_borrow = DB::table('borrow_booking')
            ->select('borrow_ID')
            ->where('booking_ID', $req->data_booking)
            ->first();

        DB::table('booking')
            ->where('booking_ID', $req->data_booking)
            ->update(['status_ID' => 4]);

        if (isset($data_borrow)) {
            DB::table('borrow_booking')
                ->where('booking_ID', $req->data_booking)
                ->update(['borrow_status' => 4]);

            return response()->json(['message' => 'ลบรายการจองสำเร็จ']);
        } else {
            return response()->json(['message' => 'ลบรายการจองสำเร็จ']);
        }
    }

    public function DELETE_BORROW (Request $req) {
        DB::table('borrow_booking')
            ->where('booking_ID', $req->data_booking)
            ->update(['borrow_status' => 4]);

        return response()->json(['message' => 'ลบรายการจองสำเร็จ']);
    }

    public function GET_QRCODE (Request $req) {
        if (isset($req->id)) {
            $data = $req->id;
            $data_qr = officer::genQR_code($data);
            return response()->json(['html'=> $data_qr]);
        }
    }

    public function GET_HISTORY_BORROW () {
        $dataBorrow = DB::table('borrow_booking')
                            ->join('detail_borrow', 'borrow_booking.borrow_ID', '=', 'detail_borrow.borrow_ID')
                            ->join('equipment', 'equipment.em_ID', '=', 'detail_borrow.equiment_ID')
                            ->join('booking', 'borrow_booking.booking_ID', '=', 'booking.booking_ID')
                            ->join('detail_booking', 'booking.booking_ID', '=', 'detail_booking.booking_ID')
                            ->join('meeting_room', 'detail_booking.meeting_ID', '=', 'meeting_room.meeting_ID')
                            ->where('booking.user_ID', Auth::user()->id)
                            ->where('borrow_booking.borrow_status', '!=', '4')
                            ->where('detail_borrow.borrow_count', '!=', '0')
                            ->OrderBy('checkin', 'desc')
                            ->get();
        return $dataBorrow;
    }
}
