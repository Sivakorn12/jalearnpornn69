<?php

namespace App\Http\Controllers\Officer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Auth;
use DB;
use \Input as Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Officer as officer;
use App\func as func;

class ReservationController extends Controller
{
    //
    public function __construct(){
        
        //$this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if(empty(Auth::user())) return redirect('/');
            if(Auth::user()->user_status!="superuser"){
                return redirect('/');
            }  
            return $next($request);
        });
        date_default_timezone_set("Asia/Bangkok");
        
    }

    public function index(){
        $dataRoom = DB::table('meeting_room')
            ->join('meeting_type', 'meeting_room.meeting_type_ID', '=', 'meeting_type.meeting_type_ID')
            ->join('building as b','meeting_room.meeting_buiding','=','b.building_id')
            ->select('meeting_ID', 'meeting_name', 'meeting_size', 'meeting_pic', 'meeting_buiding', 'meeting_status', 'meeting_type_name','b.building_name')
            ->get();

        $imgs_room = array();
        for($index = 0; $index < sizeof($dataRoom); $index++) {
            $imgs_room[$index] = explode(',', $dataRoom[$index]->meeting_pic);
        }

        $data = array(
            'rooms' => $dataRoom,
            'imgs' => $imgs_room
        );
        return view('officer/reservation/index', $data);
    }

    public function Form($id =''){
        $resultData = func::selectReserve($id);
        $imgs_room = array();
        $imgs_room = explode(',', $resultData->meeting_pic);

        $data = array(
            'rooms' => $resultData,
            'imgs' => $imgs_room
        );
        return view('officer/reservation/reserveOnID', $data);
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
            if (isset($dataHolidays)) {
                return response()->json(['error'=> 'ไม่สามารถจองห้องในวันหยุดได้', 'constant_time' => $constant_cancel_timeuse]);
            } else {
                $time_use = func::GET_TIMEUSE ($date_select, $req->roomid);
                return response()->json(['time_use'=> $time_use]);
            }
        }
    }

    public function reserveForm(Request $req){
        $timeSelect = json_decode($req->timeSelect);
        $temp_date = explode('-', $req->dateSelect);
        $date_select = ($temp_date[2] - 543).'-'.$temp_date[1].'-'.$temp_date[0];
        $temp_date_now = explode('-', date('Y-m-d'));
        $date_now = $temp_date_now[2].'-'.$temp_date_now[1].'-'.($temp_date_now[0] + 543);
        
        $dataRoom = DB::table('meeting_room')
            ->where('meeting_ID', $req->meetingId)
            ->first();
        
        $dataequipment = DB::table('equipment')
                            ->get();
        $time_remain = func::CHECK_TIME_REMAIN ($req->meetingId, $timeSelect, $req->dateSelect);
        
        $reserveEnd = false;
        if (sizeof($timeSelect) > 1) {
            $reserveEnd = true;
        }

        $data = array(
            'room' => $dataRoom,
            'time_start' => $time_remain[0],
            'time_end' => $time_remain[1],
            'time_select' => $date_select,
            'date_reserve' => $date_now,
            'reserve_start' => $timeSelect[0],
            'reserve_end' => $reserveEnd ? $timeSelect[1] : $time_remain[1][0],
            'timeTH_select' => $req->dateSelect,
            'data_equipment' => $dataequipment,
            'sections' => func::GetSection(),
            'dept' => func::GetDepartment(),
            'faculty' => func::GetFaculty()
        );
        return view('officer/reservation/Form', $data);
    }

    public function confirm(Request $req){
        $msg = [
            'detail_topic.required' => "กรุณาระบุหัวข้อการประชุม",
            'detail_count.required' => "กรุณาระบุจำนวนผู้เข้าร่วมประชุม",
            'contract_name.required' => "กรุณาระบุชื่อผู้จองห้องประชุม",
            'user_tel.required' => "กรุณาระบุเบอร์โทรศัพท์ผู้จองห้องประชุม",
            'contract_file.required' => "กรุณาแนบเอกสารหลักฐานการติดต่อจองห้องประชุม",
            'detail_count.numeric' => "จำนวนผู้เข้าร่วมประชุมต้องเป็นหมายเลข",
          ];
    
          $rule = [
            'detail_topic' => 'required',
            'detail_count' => 'required|numeric',
            'contract_name' => 'required',
            'user_tel' => 'required',
            'contract_file' => 'required',
          ];
          $validator = Validator::make($req->all(),$rule,$msg);
          if(empty($req->file('contract_file'))){
            $validator->getMessageBag()->add('contract_file', 'โปรดแนบเอกสารการจอง');
          }
          if ($validator->passes()) {
            $time_reserve = array( $req->reserve_start, $req->reserve_end );
            $temp_date = explode('-', $req->time_select);
            $date_select = $temp_date[2].'-'.$temp_date[1].'-'.($temp_date[0] + 543);

            $time_remain = func::CHECK_TIME_REMAIN ($req->meeting_id, $time_reserve, $date_select);

            $booking_startTime = array();
            $booking_endTime = array();

            for ($index = 0; $index < sizeof($time_remain[0]); $index++) {
                array_push($booking_startTime, $req->time_select.' '.$time_remain[0][$index]);
                array_push($booking_endTime, $req->time_select.' '.$time_remain[1][$index]);
            }

            if (isset($req->hdnEq)) {
                for($index = 0 ; $index < count($req->hdnEq); $index++){
                    $temp = explode(",",$req->hdnEq[$index]);
                    $data_em = DB::table('equipment')
                                ->where('em_name', $temp[0])
                                ->first();

                    $data_id_equipment[$index] = $data_em->em_ID;
                    $data_count_equipment[$index] = $temp[1];
                }

                $id_insert_booking = func::SET_DATA_BOOKING($req, $booking_startTime, $booking_endTime,1);
                $reduce_equipment_now = true;
                $accept_borrow = true;
                func::SET_DATA_BORROW($data_id_equipment, $data_count_equipment, $id_insert_booking, $req->time_select,$reduce_equipment_now,$accept_borrow);
            } else {
                $id_insert_booking = func::SET_DATA_BOOKING($req, $booking_startTime, $booking_endTime,1);
            }
            // check have file
            $files = $req->file('contract_file');
            if(!empty($req->file('contract_file'))){
                foreach($files as $key => $file){
                    $fileType = explode('.',$file->getClientOriginalName());
                    $fileType = $fileType[count($fileType)-1];
                    $fileFullName = date('U').'-doc'.($key+1).".".$fileType;
                    $file->move('asset/documents',$fileFullName);
                    for ($index = 0; $index < sizeof($id_insert_booking); $index++) {
                        DB::table('document')->insert([
                            'institute_ID'=>isset($req->institute_id)? $req->institute_id : null,
                            'section_ID' => isset($req->section_id)? $req->section_id : null,
                            'document_file' => $fileFullName,
                            'booking_id' => $id_insert_booking[$index]
                        ]);
                    }
                }
            }
            return redirect('control/reservation/')
                        ->with('successMessage','จองห้องเรียบร้อย');
          }else{
            return redirect()->back()->withInput($req->input())->withErrors($validator);
          }
    }
}
