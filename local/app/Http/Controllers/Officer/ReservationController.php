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
use App\Models\Md_RoomOpenTime;

class ReservationController extends Controller
{
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
            if (substr($check_weekend, 0, 3) == 'Sun' || isset($dataHolidays)) {
                return response()->json(['error'=> 'ไม่สามารถจองห้องในวันหยุดได้', 'constant_time' => $constant_cancel_timeuse]);
            } else {
                $time_use = func::GET_TIMEUSE ($date_select, $req->roomid);
                return response()->json(['time_use'=> $time_use]);
            }
        }
    }

    public function reserveForm(Request $req){
        $temp_date = explode('-', $req->dateSelect);
        $date_select = ($temp_date[2] - 543).'-'.$temp_date[1].'-'.$temp_date[0];
        $temp_date_now = explode('-', date('Y-m-d'));
        $date_now = $temp_date_now[2].'-'.$temp_date_now[1].'-'.($temp_date_now[0] + 543);
        
        $isReserveRoom = false;

        $timeSelect = json_decode($req->timeSelect);

        if (isset($req->endDateSelect)) {
            $isReserveRoom = func::CHECK_IS_RESERVE_ROOM($req->meetingId, $req->dateSelect, $req->endDateSelect, $timeSelect);
        }

        if ($isReserveRoom) {
            return redirect('control/reservation/')
                    ->with('errorMessage', 'ไม่สามารถจองห้องได้ เนื่องจากมีคนจองห้องก่อนหน้า ทำให้เวลาว่างของห้องไม่เท่ากัน');
        }

        $dataRoom = DB::table('meeting_room')
            ->where('meeting_ID', $req->meetingId)
            ->first();
        
        $dataequipment = DB::table('equipment')
                                ->get();
        
        if (!is_null($req->timeSelect)) {
        $time_remain = func::CHECK_TIME_REMAIN ($req->meetingId, $timeSelect, $req->dateSelect);

        $data = array(
          'room' => $dataRoom,
          'time_start' => $time_remain[0],
          'time_end' => $time_remain[1],
          'time_select' => $date_select,
          'reserve_time' => $req->timeSelect,
          'date_reserve' => $date_now,
          'timeTH_select' => $req->dateSelect,
          'timeTH_select_end' => $req->endDateSelect,
          'data_equipment' => $dataequipment,
          'sections' => func::GetSection(),
          'dept' => func::GetDepartment(),
          'faculty' => func::GetFaculty()
        );
        } else {
        $data = array(
          'room' => $dataRoom,
          'time_start' => $req->dateSelect,
          'time_end' => $req->endDateSelect,
          'time_select' => $date_select,
          'reserve_time' => '',
          'date_reserve' => $date_now,
          'timeTH_select' => $req->dateSelect,
          'data_equipment' => $dataequipment,
          'sections' => func::GetSection(),
          'dept' => func::GetDepartment(),
          'faculty' => func::GetFaculty()
        );
        }

        return view('officer/reservation/Form', $data);
    }

    public function confirm(Request $req){
        $msg = [
            'detail_topic.required' => "กรุณาระบุหัวข้อการใช้งาน",
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

            if (!is_null($req->reserve_time)) {
                $timeSelect = json_decode($req->reserve_time);
                $temp_date = explode('-', $req->time_select);
                $date_select = $temp_date[2].'-'.$temp_date[1].'-'.($temp_date[0] + 543);

                $time_remain = func::CHECK_TIME_REMAIN ($req->meeting_id, $timeSelect, $date_select);
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

                    $id_insert_booking = array();
                    if (!is_null($req->reserve_date_end)) {
                      $id_insert_booking = func::SET_DATA_BOOKING($req, $booking_startTime, $booking_endTime, 1, false, true);
                    } else {
                      $id_insert_booking = func::SET_DATA_BOOKING($req, $booking_startTime, $booking_endTime, 1);
                    }
                    $reduce_equipment_now = true;
                    $accept_borrow = true;
                    func::SET_DATA_BORROW($data_id_equipment, $data_count_equipment, $id_insert_booking, $req, $reduce_equipment_now,$accept_borrow);
                } else {
                  if (!is_null($req->reserve_date_end)) {
                    $id_insert_booking = func::SET_DATA_BOOKING($req, $booking_startTime, $booking_endTime, 1, false, true);
                  } else {
                    $id_insert_booking = func::SET_DATA_BOOKING($req, $booking_startTime, $booking_endTime, 1);
                  }
                }
    
                // check have file
                $files = $req->file('contract_file');
                if(!empty($req->file('contract_file'))){
                    foreach($files as $key => $file){
                        $fileType = explode('.',$file->getClientOriginalName());
                        $fileType = $fileType[count($fileType)-1];
                        $fileFullName = date('U').'-doc'.($key+1).".".$fileType;
                        Storage::disk('document')->put($fileFullName, file_get_contents($file));
                        for ($index = 0; $index < sizeof($id_insert_booking); $index++) {
                            DB::table('document')->insert([
                                'section_ID' => isset($req->section_id)? $req->section_id : null,
                                'document_file' => $fileFullName,
                                'booking_id' => $id_insert_booking[$index]
                            ]);
                        }
                    }
                }
                return redirect('control/reservation/')
                            ->with('successMessage','จองห้องเรียบร้อย');
            } else {
                if (isset($req->hdnEq)) {
                    for($index = 0 ; $index < count($req->hdnEq); $index++){
                        $temp = explode(",",$req->hdnEq[$index]);
                        $data_em = DB::table('equipment')
                                    ->where('em_name', $temp[0])
                                    ->first();
  
                        $data_id_equipment[$index] = $data_em->em_ID;
                        $data_count_equipment[$index] = $temp[1];
                    }
  
                    $id_insert_booking = func::SET_DATA_BOOKING($req, '', '', 1, true);
                    $reduce_equipment_now = true;
                    $accept_borrow = true;
                    func::SET_DATA_BORROW($data_id_equipment, $data_count_equipment, $id_insert_booking, $req, $reduce_equipment_now, $accept_borrow, true);
                  } else {
                    $id_insert_booking = func::SET_DATA_BOOKING($req, '', '', 1, true);
                  }
                  // check have file
                $files = $req->file('contract_file');
                if(!empty($req->file('contract_file'))){
                    foreach($files as $key => $file){
                        $fileType = explode('.',$file->getClientOriginalName());
                        $fileType = $fileType[count($fileType)-1];
                        $fileFullName = date('U').'-doc'.($key+1).".".$fileType;
                        Storage::disk('document')->put($fileFullName, file_get_contents($file));
                        for ($index = 0; $index < sizeof($id_insert_booking); $index++) {
                            DB::table('document')->insert([
                                'section_ID' => isset($req->section_id)? $req->section_id : null,
                                'document_file' => $fileFullName,
                                'booking_id' => $id_insert_booking[$index]
                            ]);
                        }
                    }
                }
                return redirect('control/reservation/')
                            ->with('successMessage','จองห้องเรียบร้อย');
            }
          } else {
            return redirect()->back()->withInput($req->input())->withErrors($validator);
          }
    }

    public function choose_adayinweek($id =''){
        $resultData = func::selectReserve($id);
        $imgs_room = array();
        $imgs_room = explode(',', $resultData->meeting_pic);

        $data = array(
            'rooms' => $resultData,
            'imgs' => $imgs_room
        );
        return view('officer/reserveADayinWeek/reserveOnID', $data);
    }

    public function checkDayReserve(Request $req){
        //dd($req->all());
        $day_reserve_arr = array();
        $time_reserve = array();

        
        $day_id = date("N", strtotime($req->date_st));
        //dd($day_id);
        $day_id = ($day_id+1)%7;
        $room_open = Md_RoomOpenTime::where('meeting_ID',$req->meeting_id)
                                    ->where('day_id',$day_id)
                                    ->first()->toArray();
        if($room_open['open_flag'] != 1 ){
            return response()->json([
                'success' => 0,
                'message' => 'ไม่สามารถจองวันที่เลือกได้เนื่องจากห้องไม่ได้เปิดใช้งาน'
            ]);
        }
        else{
            $btn_start_time = substr($room_open['open_time'],0,2);
            $btn_end_time = substr($room_open['close_time'],0,2);
            $time_btn = array();
            for($n = $btn_start_time ;$n <= $btn_end_time;$n++){
                array_push($time_btn,[
                    "index" => str_pad($n, 2, "0", STR_PAD_LEFT),
                    "time" => str_pad($n.":00", 5, "0", STR_PAD_LEFT),
                    "can_book" => 1
                ]);
            }
            $date_point = date("Y-m-d", strtotime($req->date_st));
            $date_end = date("Y-m-d", strtotime($req->date_end));
            $cnt = 0;
            while($date_point <= $date_end){
                
                //echo $date_point."<br>";
                
                array_push($day_reserve_arr,$date_point);
                $chk_room_open = officer::isRoomOpenExtra($req->meeting_id,$date_point.' '.$room_open['open_time'],$date_point.' '.$room_open['close_time']);
                if($chk_room_open == false){
                    array_push($time_reserve,[$room_open['open_time'],$room_open['close_time']]);
                } 
                if($chk_room_open != false){
                    array_push($time_reserve,[substr($chk_room_open->extra_start,-8),substr($chk_room_open->extra_end,-8)]);
                    $ex_start = substr($chk_room_open->extra_start,-8,2);
                    $ex_end = substr($chk_room_open->extra_end,-8,2);
                    $time_btn = officer::setDataBtnReserve($time_btn,$ex_start,$ex_end,'intersect');
                }

                $reserve_info = officer::isHasReserveRoom($req->meeting_id,$date_point);
                if($reserve_info != false){
                    foreach($reserve_info  as $key => $res_inf){
                        $ex_start = substr($res_inf->detail_timestart,-8,2);
                        $ex_end = substr($res_inf->detail_timeout,-8,2);
                        $time_btn = officer::setDataBtnReserve($time_btn,$ex_start,$ex_end,'except');
                        
                    }
                    // return response()->json([
                    //     'success' => 0,
                    //     'message' => 'ไม่สามารถจองวันที่เลือกได้เนื่องจากห้องถูกจองเเล้ว'
                    // ]);
                }
                //dd($time_btn);
                if(officer::isHoliday($date_point)){
                    return response()->json([
                        'success' => 0,
                        'message' => 'ไม่สามารถจองวันที่เลือกได้มีวันที่ตรงกับวันหยุด'
                    ]);
                }
                $tmp = date('Y-m-d',strtotime("+7 day", strtotime($date_point)));
                $date_point = $tmp;
            }

            return response()->json([
                'success' => 1,
                'message' => '',
                'date_reserve' =>$day_reserve_arr,
                'time_reserve' => $time_reserve,
                'time_btn' => $time_btn,
                'time_start' => $room_open['open_time'],
                'time_end' => $room_open['close_time']
            ]);


        }      
        //if($room_open[])
    }

    public function Form_adayinweek(Request $req){
        $room = DB::table('meeting_room')->where('meeting_ID',$req->meeting_ID)->first();
        $data_reserve = json_decode($req->data_reserve);
        $day_id = date("N", strtotime($data_reserve[0]))+1;
        $room_open = Md_RoomOpenTime::where('meeting_ID',$req->meeting_ID)
                                    ->where('day_id',$day_id)
                                    ->first();
        $time_reserve = json_decode($req->time_reserve);
        if(sizeof($time_reserve[0])==1){
            $time_reserve[0][1]= $time_reserve[0][0];
        }
        $time_reserve_arr = array();
        foreach($time_reserve as $key=>$trs){
            array_push($time_reserve_arr,[ $trs[0].':00',str_pad($trs[1]+1, 2, "0", STR_PAD_LEFT).':00']);
        }
        $data = array(
            'room' => $room,
            'date_reserve' => json_decode($req->data_reserve),
            'time_reserve' => $time_reserve_arr,
            'sections' => func::GetSection(),
            'dept' => func::GetDepartment(),
            'faculty' => func::GetFaculty()
        );
        return view('officer/reserveADayinWeek/Form', $data);


    }
    
    public function reserve_adayinweek(Request $req){
        //dd($req->all());
        $msg = [
            'detail_topic.required' => "กรุณาระบุหัวข้อการใช้งาน",
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
            try{
            DB::beginTransaction();
            $date_reserve = json_decode($req->data_reserve);
            $time_reserve = json_decode($req->time_reserve);
            $estimate_link = officer::getEstimateLink($req->meeting_id);
            $id_insert_booking = array();
            if (isset($req->hdnEq)) {
                for($index = 0 ; $index < count($req->hdnEq); $index++){
                    $temp = explode(",",$req->hdnEq[$index]);
                    $data_em = DB::table('equipment')
                                ->where('em_name', $temp[0])
                                ->first();

                    $data_id_equipment[$index] = $data_em->em_ID;
                    $data_count_equipment[$index] = $temp[1];
                }
            }

            
            for($i = 0 ; $i < sizeof($date_reserve); $i++){
                for($cnt_t = 0;$cnt_t < sizeof($time_reserve);$cnt_t++){
                    $id = DB::table('booking')
                                ->insertGetId([
                                    'status_ID' => 1,
                                    'section_ID' => $req->section_id ?? null,
                                    'department_ID' => $req->department_id ?? null,
                                    'faculty_ID' => $req->faculty_id ?? null,
                                    'user_ID' => $req->user_id,
                                    'booking_name' => $req->contract_name ?? $req->user_name,
                                    'booking_phone' => $req->user_tel ?? null,
                                    'booking_date' => date('Y-m-d H:i:s'),
                                    'checkin' => $date_reserve[$i]
                                ]);
                    array_push($id_insert_booking,$id);
                    DB::table('detail_booking')
                            ->insert([
                                'booking_ID' => $id,
                                'meeting_ID' => $req->meeting_id,
                                'detail_topic' => $req->detail_topic,
                                'detail_timestart' => date('Y-m-d H:i:s',strtotime($date_reserve[$i].' '.$time_reserve[$cnt_t][0])),
                                'detail_timeout' => date('Y-m-d H:i:s' ,strtotime($date_reserve[$i].' '.$time_reserve[$cnt_t][1])),
                                'detail_count' => $req->detail_count,
                                'link' => $estimate_link
                            ]);
                    $reduce_equipment_now = true;
                    $accept_borrow = false;
                    $flag_date_range = true;
                    if (isset($req->hdnEq)){
                        $borrow_booking_id = DB::table('borrow_booking')
                                                ->insertGetId([
                                                    'booking_ID' => $id,
                                                    'borrow_date' => $date_reserve[$i],
                                                    'borrow_status' => 3
                                                ]);
                                                    
                        for($inner = 0 ; $inner < sizeof($data_count_equipment); $inner++){
                            DB::table('detail_borrow')
                                ->insert([
                                    'borrow_ID' => $borrow_booking_id,
                                    'equiment_ID' => $data_id_equipment[$inner],
                                    'borrow_count' => $data_count_equipment[$inner]
                                ]);
                                $eq = DB::table('equipment')->where('em_ID', $data_id_equipment[$inner])->first();
                                DB::table('equipment')->where('em_ID', $data_id_equipment[$inner])
                                ->update([
                                    'em_count' => ($eq->em_count-$data_count_equipment[$inner])
                                ]);
                        }
                    }
                }
                //dd('insert borrow success');

            }
            //dd('insert booking success');
            // check have file
            $files = $req->file('contract_file');
            if(!empty($req->file('contract_file'))){
                foreach($files as $key => $file){
                    $fileType = explode('.',$file->getClientOriginalName());
                    $fileType = $fileType[count($fileType)-1];
                    $fileFullName = date('U').'-doc'.($key+1).".".$fileType;
                    Storage::disk('document')->put($fileFullName, file_get_contents($file));
                    for ($index = 0; $index < sizeof($id_insert_booking); $index++) {
                        DB::table('document')->insert([
                            'section_ID' => isset($req->section_id)? $req->section_id : null,
                            'document_file' => $fileFullName,
                            'booking_id' => $id_insert_booking[$index]
                        ]);
                    }
                }
            }
            
            DB::commit();
            return redirect('control/reservation/')
                        ->with('successMessage','จองห้องเรียบร้อย');
        
            }catch (Exception $e) {
            DB::rollBack();
            //dd('failed');
            return redirect('control/reservation/')
                    ->with('errorMesaage',$e);
            }
          }else{
            return redirect()->back()->withInput($req->input())->withErrors($validator);
          }
        
    }
}
