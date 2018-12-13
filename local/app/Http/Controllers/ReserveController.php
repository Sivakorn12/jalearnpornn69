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

  public function reserveForm(Request $req) {
    $temp_date = explode('-', $req->dateSelect);
    $date_select = ($temp_date[2] - 543).'-'.$temp_date[1].'-'.$temp_date[0];
    $temp_date_now = explode('-', date('Y-m-d'));
    $date_now = $temp_date_now[2].'-'.$temp_date_now[1].'-'.($temp_date_now[0] + 543);
    
    $dataRoom = DB::table('meeting_room')
        ->where('meeting_ID', $req->meetingId)
        ->first();
    
    $dataequipment = DB::table('equipment')
                            ->get();
                            
    if (!is_null($req->timeSelect)) {
      $timeSelect = json_decode($req->timeSelect);
      $time_remain = func::CHECK_TIME_REMAIN ($req->meetingId, $timeSelect, $req->dateSelect);

      $data = array(
        'room' => $dataRoom,
        'time_start' => $time_remain[0],
        'time_end' => $time_remain[1],
        'time_select' => $date_select,
        'reserve_time' => $req->timeSelect,
        'date_reserve' => $date_now,
        'timeTH_select' => $req->dateSelect,
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
      
      return view('ReserveRoom/reserveForm', $data);
  }

  public function submitReserve(Request $req) {
      $msg = [
      'detail_topic.required' => 'กรุณาระบุหัวข้อการประชุม',
      'detail_count.required' => 'กรุณาระบุจำนวนผู้เข้าประชุม',
      'user_tel.required' => 'กรุณาระบุเบอร์โทรติดต่อ',
      'contract_file.required' => "กรุณาแนบเอกสารหลักฐานการติดต่อจองห้องประชุม"
      ];
  
      $rule = [
      'detail_topic' => 'required|string',
      'detail_count' => 'required|numeric',
      'user_tel' => 'required|numeric',
      'contract_file' => 'required'
      ];

      $validator = Validator::make($req->all(),$rule,$msg);
      if(empty($req->file('contract_file'))){
        $validator->getMessageBag()->add('contract_file', 'โปรดแนบเอกสารการจอง');
      }
      if ($validator->passes()) {
          if (is_numeric($req->user_tel) && is_string($req->detail_topic) && is_numeric($req->detail_count)) {

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

                      $id_insert_booking = func::SET_DATA_BOOKING($req, $booking_startTime, $booking_endTime, 3);
                      $reduce_equipment_now = false;
                      $accept_borrow = false;
                      func::SET_DATA_BORROW($data_id_equipment, $data_count_equipment, $id_insert_booking, $req, $reduce_equipment_now, $accept_borrow);
                  } else {
                      $id_insert_booking = func::SET_DATA_BOOKING($req, $booking_startTime, $booking_endTime, 3);
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
                                    'institute_ID'=>isset($req->institute_id)? $req->institute_id : null,
                                    'section_ID' => isset($req->section_id)? $req->section_id : null,
                                    'document_file' => $fileFullName,
                                    'booking_id' => $id_insert_booking[$index]
                                ]);
                            }
                        }
                    }
                }
                return redirect('reserve')->with('message', 'จองห้องสำเร็จ');
            } else {
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

                  $id_insert_booking = func::SET_DATA_BOOKING($req, '', '', 3, true);
                  $reduce_equipment_now = false;
                  $accept_borrow = false;
                  func::SET_DATA_BORROW($data_id_equipment, $data_count_equipment, $id_insert_booking, $req, $reduce_equipment_now, $accept_borrow, true);
                } else {
                  $id_insert_booking = func::SET_DATA_BOOKING($req, '', '', 3, true);
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
                                'institute_ID'=>isset($req->institute_id)? $req->institute_id : null,
                                'section_ID' => isset($req->section_id)? $req->section_id : null,
                                'document_file' => $fileFullName,
                                'booking_id' => $id_insert_booking[$index]
                            ]);
                        }
                    }
                }
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
    $end_date_select = '';
    if (isset($req->endDate)) {
        $temp_date = explode('-', $req->endDate);
        $end_date_select = ($temp_date[2] - 543).'-'.$temp_date[1].'-'.$temp_date[0];
    }

    $isToday = func::CHECK_TODAY($date_select);
    $isHoliday = func::CHECK_HOLIDAY($date_select);
    
    if ($isToday) {
      return response()->json(['error'=> 'ไม่สามารถจองห้องย้อนหลังได้']);
    } else {
      $isRoomOpen = func::CHECK_ROOM_OPEN($req->roomid, $date_select, $end_date_select);
      $isReserveRoom = func::CHECK_IS_RESERVE_ROOM($req->roomid, $date_select, $end_date_select);

      if ($isHoliday) {
        return response()->json(['error'=> 'ไม่สามารถจองห้องในวันหยุดได้']);
      }
      if ($isReserveRoom) {
        return response()->json(['error'=> 'ไม่สามารถจองห้องได้ เนื่องจากมีคนจองก่อนหน้าแล้ว']);
      }
      if ($isRoomOpen) {
        if (strlen($end_date_select) == 0) {
          $date_now = date('Y-m-d');
          $timenow = date('H');
      
          $empty_timeuse = array();
          $data_openExtra = DB::table('meeting_open_extra')
                                  ->where(DB::Raw('SUBSTRING(extra_start, 1, 10)'), $date_select)
                                  ->first();
          
          $data_open_over_time = DB::table('meeting_over_time')
                                      ->where([
                                          [DB::Raw('SUBSTRING(start_date, 1, 10)'), '<=', $date_select],
                                          [DB::Raw('SUBSTRING(end_date, 1, 10)'), '>=', $date_select]
                                      ])
                                      ->first();
      
          $time_reserve_total = func::CHECK_TIME_OPEN_ROOM($req->roomid, $date_select);
          $time_start = (int)$time_reserve_total->open_time;
          $time_end = (int)$time_reserve_total->close_time;
          $time_reserve = array();
      
          if (isset($data_openExtra)) {
              $time_start = substr($data_openExtra->extra_start, -8, 2);
              $time_end = substr($data_openExtra->extra_end, -8, 2);
          }
      
          if(isset($data_open_over_time)) {
              $time_start = substr($data_open_over_time->start_date, -8, 2);
              $time_end = substr($data_open_over_time->end_date, -8, 2);
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
  
          $time_empty = func::GET_TIMEUSE ($date_select, $time_reserve, $empty_timeuse, $req->roomid);
          return response()->json(['time_empty'=> $time_empty, 'time_reserve' => $time_reserve]);
        } else {
          return response()->json(['time_empty'=> true]);
        }
      } else {
        $dayCloseRoom = func::CHECK_ROOM_CLOSE($req->roomid, $date_select, $end_date_select);
        return response()->json(['error'=> $dayCloseRoom]);
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

      $tmp_timeStart = substr($dataReserve->detail_timestart, -8, -3);
      $tmp_timeEnd = ((int)substr($dataReserve->detail_timeout, -8, -3) - 1).':00';
      $arrTimeReserve = array($tmp_timeStart, $tmp_timeEnd);

      $time_remain = func::CHECK_TIME_REMAIN ($dataReserve->meeting_ID, $arrTimeReserve, $dataReserve->checkin);
      $tmpDate = explode("-", $dataReserve->checkin);
      $timeTH = $tmpDate[2].'-'.$tmpDate[1].'-'.($tmpDate[0] + 543);

      $data = array(
          'room_id' => $dataReserve->meeting_ID,
          'room_name' => $dataReserve->meeting_name,
          'time_start' => $time_remain[0],
          'time_end' => $time_remain[1],
          'time_select' => $dataReserve->checkin,
          'timeTH_select' => $timeTH,
          'reserve_start' => $tmp_timeStart,
          'reserve_end' => $tmp_timeEnd,
          'dataReserve' => $dataReserve,
          'dataBorrow' => $dataBorrow,
          'section_id' =>$dataReserve->section_ID,
          'dep_id' =>$dataReserve->department_ID,
          'fac_id' =>$dataReserve->faculty_ID,
          'sections' => func::GetSection(),
          'dept' => func::GetDepartment(),
          'faculty' => func::GetFaculty()
      );

      return view('ReserveRoom/reserveFormEdit', $data);
  }

  public function SET_EDIT_DATA_RESERVE (Request $req) {
      $msg = [
      'detail_topic.required' => 'กรุณาระบุหัวข้อการประชุม',
      'detail_count.required' => 'กรุณาระบุจำนวนผู้เข้าประชุม',
      'user_tel.required' => 'กรุณาระบุเบอร์โทรติดต่อ',
      ];
  
      $rule = [
      'detail_topic' => 'required|string',
      'detail_count' => 'required|numeric',
      'user_tel' => 'required|numeric',
      ];

      $dataBorrow = json_decode($req->borrow);

      $validator = Validator::make($req->all(),$rule,$msg);

      if ($validator->passes()) {
          if (is_numeric($req->user_tel) && is_string($req->detail_topic) && is_numeric($req->detail_count)) {
              $time_start = $req->time_select.' '.$req->reserve_start;
              $time_out = $req->time_select.' '.$req->reserve_end;

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

                      func::UPDATE_DATA_BOOKING($req, $time_start, $time_out);

                      if (empty($dataBorrow[0]->borrow_ID)) {
                        $reduce_equipment_now = false;
                        $accept_borrow = false;
                          func::SET_DATA_BORROW($data_id_equipment, $data_count_equipment, $req->booking_id, $req, $reduce_equipment_now, $accept_borrow);
                      } else {
                          func::UPDATE_DATA_BORROW($data_id_equipment, $data_count_equipment, $req->booking_id, $req, $dataBorrow[0]->borrow_ID);
                      }
                  } else {
                      func::UPDATE_DATA_BOOKING($req, $time_start, $time_out);
                  }

                  $files = $req->file('contract_file');
                  if(!empty($req->file('contract_file'))) {
                    foreach($files as $key => $file){
                        $fileType = explode('.',$file->getClientOriginalName());
                        $fileType = $fileType[count($fileType)-1];
                        $fileFullName = date('U').'-doc'.($key+1).".".$fileType;
                        Storage::disk('document')->put($fileFullName, file_get_contents($file));
                        for ($index = 0; $index < sizeof($id_insert_booking); $index++) {
                            DB::table('document')
                                ->where('booking_id', $req->booking_id)
                                ->update([
                                    'institute_ID'=>isset($req->institute_id)? $req->institute_id : null,
                                    'section_ID' => isset($req->section_id)? $req->section_id : null,
                                    'document_file' => 'test.pdf'
                                ]);
                        }
                    }
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
}
