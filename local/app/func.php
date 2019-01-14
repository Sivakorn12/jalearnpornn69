<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class func extends Model
{
    public function __construct()
    {
        date_default_timezone_set("Asia/Bangkok");
    }

    public static function queryData($data, $condition) {
        $dataRoom = DB::table('meeting_room')
            ->join('meeting_type', 'meeting_room.meeting_type_ID', '=', 'meeting_type.meeting_type_ID')
            ->select('meeting_name', 'meeting_size', 'meeting_pic', 'meeting_buiding', 'meeting_status', 'meeting_type_name')
            ->where($condition, $data)
            ->get();

        $tableHTML = "<table class='table table-hover showroom'>";
        foreach($dataRoom as $key => $room) {
            $tableHTML = $tableHTML."
                                    <tr>
                                        <td>
                                            <img src='".url ("asset/rooms/".$room->meeting_pic)."' width='100'>
                                        </td>
                                        <td>
                                            ".$room->meeting_name."
                                        </td>
                                        <td>
                                            ".$room->meeting_size."
                                        </td>
                                        <td>
                                            ".$room->meeting_type_name."
                                        </td>
                                        <td>
                                            ".$room->meeting_buiding."
                                        </td>
                                        <td>
                                            ";
                                            if($room->meeting_status == 1) {
                                                $tableHTML = $tableHTML."<i class='fa fa-check-circle fa-lg' style='color: green' aria-hidden='true'></i>";
                                            }
                                            else $tableHTML = $tableHTML."<i class='fa fa-ban fa-lg' aria-hidden='true'></i>";
        }

        $tableHTML = $tableHTML."</td></tr></table>";

        return $tableHTML;
    }

    public static function selectReserve ($data) {
        $check_reserve = DB::table('meeting_room')
                                ->where('meeting_ID', $data)
                                ->where('meeting_status', '=', '1')
                                ->first();

        if (isset($check_reserve)) {
            return $check_reserve;
        } else {
            return null;
        }
    }

    public static function Getequips ($id) {
        $dataEquips = DB::table('equipment_in')
                            ->where('meeting_ID', $id)
                            ->get();
        return $dataEquips;
    }

    public static function GetSection () {
        $dataSection = DB::table('section')
                            ->get()->toArray();
        return $dataSection;
    }

    public static function GetDepartment () {
        $data = DB::table('department')
                        ->get()->toArray();
        return $data;
    }

    public static function GetFaculty () {
        $data = DB::table('faculty')
                            ->get()->toArray();
        return $data;
    }

    public static function GET_EQUIPMENT () {
        $dataEquipment = DB::table('equipment')
                            ->get();
        return $dataEquipment;
    }

    public static function GET_TIMEUSE ($date_select, $times, $checkTimeuse, $id) {
        $datatimes = DB::table('detail_booking')
                            ->join('booking', 'detail_booking.booking_ID', '=', 'booking.booking_ID')
                            ->where(DB::Raw('SUBSTRING(detail_timestart, 1, 10)'), $date_select)
                            ->where('meeting_ID', $id)
                            ->OrderBy('detail_timestart')
                            ->get();

        if (isset($datatimes)) {
            foreach ($datatimes as $reserves) {
                for ($index = 0; $index < sizeof($times); $index++) {
                    if (substr($reserves->detail_timestart, -8, 5) == $times[$index] && ($reserves->status_ID == 1 || $reserves->status_ID == 3)) {
                        $hour = substr($reserves->detail_timeout, -8, 2) - substr($reserves->detail_timestart, -8, 2);
                        for ($inner = 0; $inner < $hour; $inner++) {
                            $checkTimeuse[$inner + $index] = 1;
                        }
                    }
                }
            }
        }
        return $checkTimeuse;
    }

    public static function CHECK_TIME_REMAIN ($id, $time_reserve, $time_select) {
        $formatter_day_en = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        $temp_date = explode('-', $time_select);
        $date_select = ($temp_date[2] - 543).'-'.$temp_date[1].'-'.$temp_date[0];

        $temp_day = date_create($date_select);
        $temp_day = date_format($temp_day, 'r');
        $day_select = substr($temp_day, 0, 3);
        $index_day_start = array_search($day_select, $formatter_day_en);

        $data_openExtra = DB::table('meeting_open_extra')
                                ->where(DB::Raw('SUBSTRING(extra_start, 1, 10)'), $date_select)
                                ->first();

        $data_open_over_time = DB::table('meeting_over_time')
                                    ->where([
                                        [DB::Raw('SUBSTRING(start_date, 1, 10)'), '<=', $date_select],
                                        [DB::Raw('SUBSTRING(end_date, 1, 10)'), '>=', $date_select]
                                    ])
                                    ->first();

        $room_open_time = DB::table('room_open_time')
                            ->select('open_time', 'close_time')
                            ->where('meeting_ID', $id)
                            ->where('day_id', $index_day_start + 1)
                            ->where('open_flag', 1)
                            ->get();

        $time_start = (int)$room_open_time[0]->open_time;
        $time_end = (int)$room_open_time[0]->close_time;
        $times = array();
        $checkTimeuse = array();
        $checktimeReserve = array();

        if (isset($data_openExtra)) {
            $time_start = substr($data_openExtra->extra_start, -8, 2);
            $time_end = substr($data_openExtra->extra_end, -8, 2);
        }

        if(isset($data_open_over_time)) {
            $time_start = substr($data_open_over_time->start_date, -8, 2);
            $time_end = substr($data_open_over_time->end_date, -8, 2);
        }

        for ($index = $time_start; $index < $time_end; $index++) {
            array_push($checkTimeuse, 0);
            array_push($checktimeReserve, 0);
            if (strlen($index) < 2) {
                array_push($times, '0'.$index.':00');
            } else {
                array_push($times, $index.':00');
            }
        }
        
        $datatimes = DB::table('detail_booking')
                            ->join('booking', 'detail_booking.booking_ID', '=', 'booking.booking_ID')
                            ->where(DB::Raw('SUBSTRING(detail_timestart, 1, 10)'), $date_select)
                            ->where('meeting_ID', $id)
                            ->OrderBy('detail_timestart')
                            ->get();

        if (isset($datatimes)) {
            foreach ($datatimes as $reserves) {
                if (substr($reserves->detail_timestart, 0, 10) == $date_select) {
                    for ($index = 0; $index < sizeof($times); $index++) {
                        if (substr($reserves->detail_timestart, -8, 5) == $times[$index] && ($reserves->status_ID == 1 || $reserves->status_ID == 3)) {
                            $hour = substr($reserves->detail_timeout, -8, 2) - substr($reserves->detail_timestart, -8, 2);
                            for ($inner = 0; $inner < $hour; $inner++) {
                                $checkTimeuse[$inner + $index] = 1;
                            }
                        }
                    }
                }
            }
        }

        if (sizeof($time_reserve) > 1) {
            for ($index = 0; $index < sizeof($times); $index++) {
                if ($time_reserve[0] == $times[$index] && $times[$index] <= $time_reserve[1]) {
                    $hour = ((int)$time_reserve[1] - (int)$time_reserve[0]) + 1;
                    for ($inner = 0; $inner < $hour; $inner++) {
                        $checktimeReserve[$inner + $index] = 1;
                    }
                }
            }
        } else {
            for ($index = 0; $index < sizeof($times); $index++) {
                if ($time_reserve[0] == $times[$index]) {
                    $checktimeReserve[$index] = 1;
                }
            }
        }

        $bookingStart = array();
        $bookingEnd = array();
        $count = 0;

        for ($index = 0; $index < sizeof($checkTimeuse); $index++) {
            if (sizeof($time_reserve) == 1) {
                if (($checkTimeuse[$index] == 0 && $checktimeReserve[$index] == 1)) {
                    array_push($bookingStart, $times[$index]);
                    array_push($bookingEnd, $times[$index + 1]);
                    break;
                }
            }

            if (sizeof($time_reserve) == 2) {
                if (($checkTimeuse[$index] == 0 && $checktimeReserve[$index] == 1) && $count == 0) {
                    array_push($bookingStart, $times[$index]);
                    $count++;
                } else if (($checkTimeuse[$index] == 1 && $checktimeReserve[$index] == 0) && $count != 0) {
                    array_push($bookingEnd, $times[$index]);
                    $count = 0;
                } else if (($checkTimeuse[$index] == 1 && $checktimeReserve[$index] == 1) && $count != 0) {
                    array_push($bookingEnd, $times[$index]);
                    $count = 0;
                } else if (($checkTimeuse[$index] == 0 && $checktimeReserve[$index] == 0) && $count != 0) {
                    array_push($bookingEnd, $times[$index]);
                    $count = 0;
                } else if (($checkTimeuse[$index] == 0 && $checktimeReserve[$index] == 1) && ($index == sizeof($checkTimeuse) - 1)) {
                    array_push($bookingEnd, $time_end.':00');
                    $count = 0;
                }
                if (sizeof($time_reserve) < 2 && (sizeof($bookingStart) > 0 && $count == 1)) {
                    if ($index != sizeof($checkTimeuse) - 1) {
                        array_push($bookingEnd, $times[$index + 1]);
                    } else {
                        if (strlen($time_end) < 2) {
                            array_push($bookingEnd, '0'.$time_end.':00');
                        } else {
                            array_push($bookingEnd, $time_end.':00');
                        }
                    }
                    break;
                }
            }
        }

        return array($bookingStart, $bookingEnd);
    }

    public static function GET_EXTRATIME () {
        $date_now = date('Y-m-d');
        $data_openExtra = DB::table('meeting_open_extra')
                                ->where(DB::Raw('SUBSTRING(extra_start, 1, 10)'), $date_now)
                                ->first();
        return $data_openExtra;
    }

    public static function SET_DATA_BOOKING ($req, $time_start, $time_out, $status=3, $flag_date_range = false, $flag_date_range_with_time = false) {
        $data_meetingroom = DB::table('meeting_room')
                                ->where('meeting_ID', $req->meeting_id)
                                ->first();

        $estimate_link = $data_meetingroom->estimate_link.'#responses';
        $id_insert = array();
        
        if ($flag_date_range) {
          $time_range = func::GET_TIME_OPEN_ROOM_RANGE($req->meeting_id, $req->start_range, $req->end_range);
          $checkInDate = func::GET_CHECKIN_DATE_RANGE($req->start_range, $req->end_range);
          for ($index = 0; $index < sizeof($time_range); $index++) {
            $id = DB::table('booking')
                            ->insertGetId([
                                'status_ID' => $status,
                                'section_ID' => $req->section_id ?? null,
                                'department_ID' => $req->department_id ?? null,
                                'faculty_ID' => $req->faculty_id ?? null,
                                'user_ID' => $req->user_id,
                                'booking_name' => $req->contract_name ?? $req->user_name,
                                'booking_phone' => isset($req->user_tel)? $req->user_tel : null,
                                'booking_date' => date('Y-m-d H:i:s'),
                                'checkin' => $checkInDate[$index]
                            ]);
            DB::table('detail_booking')
                    ->insert([
                        'booking_ID' => $id,
                        'meeting_ID' => $req->meeting_id,
                        'detail_topic' => $req->detail_topic,
                        'detail_timestart' => $checkInDate[$index].' '.$time_range[$index][0],
                        'detail_timeout' => $checkInDate[$index].' '.$time_range[$index][1],
                        'detail_count' => $req->detail_count,
                        'link' => $estimate_link
                    ]);
            array_push($id_insert, $id);
          }


        } else {
          if ($flag_date_range_with_time) {
            $timeSelect = json_decode($req->reserve_time);
            $temp_date = explode('-', $req->time_select);
            $date_select = $temp_date[2].'-'.$temp_date[1].'-'.($temp_date[0] + 543);
            $temp_end_date_select = explode('-', $req->reserve_date_end);

            $time_remain = func::CHECK_TIME_REMAIN ($req->meeting_id, $timeSelect, $date_select);

            $booking_startTime = array();
            $booking_endTime = array();

            $date_checkin = array();

            for ($index = $temp_date[2]; $index <= $temp_end_date_select[0]; $index++) {
              for ($inner = 0; $inner < sizeof($time_remain[0]); $inner++) {
                array_push($booking_startTime, ($temp_date[0].'-'.$temp_date[1].'-'.$index.' '.$time_remain[0][$inner]));
                array_push($booking_endTime, ($temp_date[0].'-'.$temp_date[1].'-'.$index.' '.$time_remain[1][$inner]));
                array_push($date_checkin, ($temp_date[0].'-'.$temp_date[1].'-'.$index));
              }
            }
            
            for ($index = 0; $index < sizeof($booking_startTime); $index++) {
                $id = DB::table('booking')
                                ->insertGetId([
                                    'status_ID' => $status,
                                    'section_ID' => $req->section_id ?? null,
                                    'department_ID' => $req->department_id ?? null,
                                    'faculty_ID' => $req->faculty_id ?? null,
                                    'user_ID' => $req->user_id,
                                    'booking_name' => $req->contract_name ?? $req->user_name,
                                    'booking_phone' => $req->user_tel ?? null,
                                    'booking_date' => date('Y-m-d H:i:s'),
                                    'checkin' => $date_checkin[$index]
                                ]);
                DB::table('detail_booking')
                        ->insert([
                            'booking_ID' => $id,
                            'meeting_ID' => $req->meeting_id,
                            'detail_topic' => $req->detail_topic,
                            'detail_timestart' => $booking_startTime[$index],
                            'detail_timeout' => $booking_endTime[$index],
                            'detail_count' => $req->detail_count,
                            'link' => $estimate_link
                        ]);
                array_push($id_insert, $id);
            }
          } else {
            for ($index = 0; $index < sizeof($time_start); $index++) {
                $id = DB::table('booking')
                                ->insertGetId([
                                    'status_ID' => $status,
                                    'section_ID' => $req->section_id ?? null,
                                    'department_ID' => $req->department_id ?? null,
                                    'faculty_ID' => $req->faculty_id ?? null,
                                    'user_ID' => $req->user_id,
                                    'booking_name' => $req->contract_name ?? $req->user_name,
                                    'booking_phone' => isset($req->user_tel)? $req->user_tel : null,
                                    'booking_date' => date('Y-m-d H:i:s'),
                                    'checkin' => $req->time_select
                                ]);
                DB::table('detail_booking')
                        ->insert([
                            'booking_ID' => $id,
                            'meeting_ID' => $req->meeting_id,
                            'detail_topic' => $req->detail_topic,
                            'detail_timestart' => $time_start[$index],
                            'detail_timeout' => $time_out[$index],
                            'detail_count' => $req->detail_count,
                            'link' => $estimate_link
                        ]);
                array_push($id_insert, $id);
            }
          }
        }
        return $id_insert;
    }

    public static function UPDATE_DATA_BOOKING ($req, $time_start, $time_out) {
        $id_insert = DB::table('booking')
                        ->where('booking_ID', $req->booking_id)
                        ->update([
                            'section_ID' => isset($req->section_id)? $req->section_id : null,
                            'booking_phone' => isset($req->user_tel)? $req->user_tel : null,
                            'booking_date' => date('Y-m-d H:i:s'),
                        ]);
        DB::table('detail_booking')
                ->where('booking_ID', $req->booking_id)
                ->update([
                    'detail_topic' => $req->detail_topic,
                    'detail_timestart' => $time_start,
                    'detail_timeout' => $time_out,
                    'detail_count' => $req->detail_count
                ]);

        return $id_insert;
    }

    public static function SET_DATA_BORROW ($id_equipment, $count_equipment, $id_insert_booking, $req, $reduce_equipment_now = false, $accept_borrow= false, $flag_date_range = false) {
        $id_borrow_booking = array();
        $borrow_status = ($accept_borrow)?1:3;

        if ($flag_date_range) {
          $checkInDate = func::GET_CHECKIN_DATE_RANGE($req->start_range, $req->end_range);

          if (is_array($id_insert_booking)) {
              for ($index = 0; $index < sizeof($id_insert_booking); $index++) {
                  $id = DB::table('borrow_booking')
                                          ->insertGetId([
                                              'booking_ID' => $id_insert_booking[$index],
                                              'borrow_date' => $checkInDate[$index],
                                              'borrow_status' => $borrow_status
                                          ]);
                  array_push($id_borrow_booking, $id);
              }
      
              for($index = 0; $index < sizeof($id_borrow_booking); $index++) {
                  for($inner = 0 ; $inner < sizeof($count_equipment); $inner++){
                      DB::table('detail_borrow')
                          ->insert([
                              'borrow_ID' => $id_borrow_booking[$index],
                              'equiment_ID' => $id_equipment[$inner],
                              'borrow_count' => $count_equipment[$inner]
                          ]);
                      if($reduce_equipment_now){
                          $eq = DB::table('equipment')->where('em_ID', $id_equipment[$inner])->first();
                          DB::table('equipment')->where('em_ID', $id_equipment[$inner])
                          ->update([
                              'em_count' => ($eq->em_count-$count_equipment[$inner])
                          ]);
                      }
                  }
              }
          } else {
              /*$id = DB::table('borrow_booking')
                                          ->insertGetId([
                                              'booking_ID' => $id_insert_booking[$index],
                                              'borrow_date' => $time_select,
                                              'borrow_status' => 3
                                          ]);
  
              for($inner = 0 ; $inner < sizeof($count_equipment); $inner++){
                  DB::table('detail_borrow')
                      ->insert([
                          'borrow_ID' => $id,
                          'equiment_ID' => $id_equipment[$inner],
                          'borrow_count' => $count_equipment[$inner]
                      ]);
              }*/
          }
        } else {
            if (is_array($id_insert_booking)) {
              for ($index = 0; $index < sizeof($id_insert_booking); $index++) {
                  $id = DB::table('borrow_booking')
                                          ->insertGetId([
                                              'booking_ID' => $id_insert_booking[$index],
                                              'borrow_date' => $req->time_select,
                                              'borrow_status' => $borrow_status
                                          ]);
                  array_push($id_borrow_booking, $id);
              }
      
              for($index = 0; $index < sizeof($id_borrow_booking); $index++) {
                  for($inner = 0 ; $inner < sizeof($count_equipment); $inner++){
                      DB::table('detail_borrow')
                          ->insert([
                              'borrow_ID' => $id_borrow_booking[$index],
                              'equiment_ID' => $id_equipment[$inner],
                              'borrow_count' => $count_equipment[$inner]
                          ]);
                      if($reduce_equipment_now){
                          $eq = DB::table('equipment')->where('em_ID', $id_equipment[$inner])->first();
                          DB::table('equipment')->where('em_ID', $id_equipment[$inner])
                          ->update([
                              'em_count' => ($eq->em_count-$count_equipment[$inner])
                          ]);
                      }
                  }
              }
          } else {
              $id = DB::table('borrow_booking')
                                          ->insertGetId([
                                              'booking_ID' => $id_insert_booking,
                                              'borrow_date' => $req->time_select,
                                              'borrow_status' => 3
                                          ]);

              for($inner = 0 ; $inner < sizeof($count_equipment); $inner++){
                  DB::table('detail_borrow')
                      ->insert([
                          'borrow_ID' => $id,
                          'equiment_ID' => $id_equipment[$inner],
                          'borrow_count' => $count_equipment[$inner]
                      ]);
              }
          }
        }
    }

    public static function UPDATE_DATA_BORROW ($id_equipment, $count_equipment, $id_booking, $time_select, $borrow_id) {
        if ($borrow_id != null) {
            $dataBorrow = DB::table('borrow_booking')
                            ->join('detail_borrow', 'borrow_booking.borrow_ID', '=', 'detail_borrow.borrow_ID')
                            ->where('borrow_booking.borrow_ID', $borrow_id)
                            ->get();

            $get_equipID = array();
            $get_equipCount = array();
            foreach ($dataBorrow as $key => $value) {
                $get_equipID[$key] = $value->equiment_ID;
                $get_equipCount[$key] = $value->borrow_count;
            }

            for ($index = 0; $index < sizeof($get_equipID); $index++) {
                for ($inner = 0; $inner < sizeof($id_equipment); $inner++) {
                    if ($id_equipment[$inner] != $get_equipID[$index]) {
                        if (sizeof($get_equipID) > sizeof($id_equipment)) {
                            DB::table('detail_borrow')
                            ->where('borrow_ID', $borrow_id)
                            ->where('equiment_ID', $get_equipID[$index])
                            ->update(['borrow_count' => 0]);
                        } else if (sizeof($get_equipID) < sizeof($id_equipment)) {
                            DB::table('detail_borrow')
                            ->insert([
                                'borrow_ID' => $borrow_id,
                                'equiment_ID' => $id_equipment[$inner],
                                'borrow_count' => $count_equipment[$inner]
                            ]);
                        } else if (sizeof($get_equipID) == sizeof($id_equipment)) {
                            DB::table('detail_borrow')
                            ->where('borrow_ID', $borrow_id)
                            ->where('equiment_ID', $get_equipID[$index])
                            ->update(['borrow_count' => 0]);

                            DB::table('detail_borrow')
                            ->insert([
                                'borrow_ID' => $borrow_id,
                                'equiment_ID' => $id_equipment[$inner],
                                'borrow_count' => $count_equipment[$inner]
                            ]);
                        }
                    } else if ($id_equipment[$inner] == $get_equipID[$index] && $count_equipment[$inner] != $get_equipCount[$index]) {
                        DB::table('detail_borrow')
                            ->where('borrow_ID', $borrow_id)
                            ->where('equiment_ID', $get_equipID[$index])
                            ->update([
                                'borrow_count' => $count_equipment[$inner]
                            ]);
                    }
                }
            }
        } else {

        }
    }

  public static function CHECK_TODAY($dateSelect) {
    $dateNow = date('Y-m-d');
    if ($dateNow > $dateSelect) {
      return true;
    } else {
      return false;
    }
  }

  public static function CHECK_ROOM_OPEN($roomId, $start_date, $end_date = '') {
    $formatter_day_en = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $formatter_day_th = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];

    if (strlen($end_date) == 0) {
      $temp_day_start = date_create($start_date);
      $temp_day_start = date_format($temp_day_start, 'r');
      $day_start = substr($temp_day_start, 0, 3);
      $index_of_day = array_search($day_start, $formatter_day_en);

      $data_room_open_time = DB::table('room_open_time')
                              ->where('meeting_ID', $roomId)
                              ->where('day_id', $index_of_day + 1)
                              ->first();

      if ($data_room_open_time->open_flag == 0) {
        return false;
      } else {
        return true;
      }
    } else {
      $temp_day = date_create($start_date);
      $temp_day = date_format($temp_day, 'r');
      $day_start = substr($temp_day, 0, 3);
      $index_day_start = array_search($day_start, $formatter_day_en);

      $temp_end_day = date_create($end_date);
      $temp_end_day = date_format($temp_end_day, 'r');
      $day_end = substr($temp_end_day, 0, 3);
      $index_day_end = array_search($day_end, $formatter_day_en);

      $room_open_time = DB::table('room_open_time')
                              ->where('meeting_ID', $roomId)
                              ->where([
                                  ['day_id', '>=', $index_day_start + 1],
                                  ['day_id', '<=', $index_day_end + 1]
                              ])
                              ->where('open_flag', 0)
                              ->get();
      
      if (sizeof($room_open_time) == 0) {
        return true;
      } else {
        return false;
      }
    }
  }
  
  public static function CHECK_ROOM_CLOSE($roomId, $start_date, $end_date = '') {
    $formatter_day_en = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $formatter_day_th = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];

    if (strlen($end_date) == 0) {
      $temp_day_start = date_create($start_date);
      $temp_day_start = date_format($temp_day_start, 'r');
      $day_start = substr($temp_day_start, 0, 3);
      $index_of_day = array_search($day_start, $formatter_day_en);
      $viewHTML = 'ไม่สามารถจองห้องได้ เนื่องจากห้องถูกปิดในวัน'.$formatter_day_th[$index_of_day];

      return $viewHTML;
    } else {
      $temp_day = date_create($start_date);
      $temp_day = date_format($temp_day, 'r');
      $day_start = substr($temp_day, 0, 3);
      $index_day_start = array_search($day_start, $formatter_day_en);

      $temp_end_day = date_create($end_date);
      $temp_end_day = date_format($temp_end_day, 'r');
      $day_end = substr($temp_end_day, 0, 3);
      $index_day_end = array_search($day_end, $formatter_day_en);

      $room_open_time = DB::table('room_open_time')
                              ->where('meeting_ID', $roomId)
                              ->where([
                                  ['day_id', '>=', $index_day_start + 1],
                                  ['day_id', '<=', $index_day_end + 1]
                              ])
                              ->where('open_flag', 0)
                              ->get();

      $viewHTML = 'ไม่สามารถจองห้องได้ เนื่องจากห้องถูกปิดใน';
      for ($index = 0; $index < sizeof($room_open_time); $index++) {
        $viewHTML .= 'วัน'.$formatter_day_th[$room_open_time[$index]->day_id - 1].' ';
      }
      return $viewHTML;
    }
  }

  public static function CHECK_HOLIDAY($date_select) {
    $dataHolidays = DB::table('holiday')
                            ->where('holiday_start', '<=', $date_select)
                            ->where('holiday_end', '>=', $date_select)
                            ->first();

    if (isset($dataHolidays)) {
      return true;
    } else {
      return false;
    }
  }

  public static function CHECK_TIME_OPEN_ROOM($roomId, $dateSelect) {
    $formatter_day_en = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $temp_day = date_create($dateSelect);
    $temp_day = date_format($temp_day, 'r');
    $day = substr($temp_day, 0, 3);
    $index_of_day = array_search($day, $formatter_day_en);

    $data_room_open_time = DB::table('room_open_time')
                            ->where('meeting_ID', $roomId)
                            ->where('day_id', $index_of_day + 1)
                            ->first();

    return $data_room_open_time;
  }

  public static function GET_TIME_OPEN_ROOM_RANGE($roomId, $startDate, $endDate) {
    $formatter_day_en = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    $tmp_start_date = explode('-', $startDate);
    $tmp_end_date = explode('-', $endDate);
    $time_range = array();

    $count = 0;

    for ($index = $tmp_start_date[0]; $index <= $tmp_end_date[0]; $index++) {
      $convert_date = ($tmp_start_date[2] - 543).'-'.$tmp_start_date[1].'-'.$index;
      $temp_day = date_create($convert_date);
      $temp_day = date_format($temp_day, 'r');
      $isDay = substr($temp_day, 0, 3);
      $index_day = array_search($isDay, $formatter_day_en);

      $room_open_time = DB::table('room_open_time')
                            ->where('meeting_ID', $roomId)
                            ->where('day_id', $index_day + 1)
                            ->first();

      $time_range[$count][0] = $room_open_time->open_time;
      $time_range[$count][1] = $room_open_time->close_time;
      $count++;
    }

    return $time_range;
  }

  public static function GET_CHECKIN_DATE_RANGE($startDate, $endDate) {
    $tmp_start_date = explode('-', $startDate);
    $tmp_end_date = explode('-', $endDate);

    $date_checkin = array();

    for ($index = $tmp_start_date[0]; $index <= $tmp_end_date[0]; $index++) {
      $convert_date = ($tmp_start_date[2] - 543).'-'.$tmp_start_date[1].'-'.$index;
      array_push($date_checkin, $convert_date);
    }

    return $date_checkin;
  }

  public static function CHECK_IS_RESERVE_ROOM($roomId, $startDate, $endDate, $timeSelect) {
    $tmpStartDate = explode('-', $startDate);
    $startDateSelectFormatter = ($tmpStartDate[2] - 543).'-'.$tmpStartDate[1].'-'.$tmpStartDate[0];

    $tmpEndDate = explode('-', $endDate);
    $endDateSelectFormatter = ($tmpEndDate[2] - 543).'-'.$tmpEndDate[1].'-'.$tmpEndDate[0];

    $isReserveEqual = array();

    for ($index = $tmpStartDate[0]; $index <= $tmpEndDate[0]; $index++) {
        $tempDateCheck = ($tmpEndDate[2] - 543).'-'.$tmpEndDate[1].'-'.$index;

        $isReseve = DB::table('detail_booking')
                    ->where('meeting_ID', $roomId)
                    ->where([
                      [DB::Raw('detail_timestart'), '>=', $tempDateCheck.' '.$timeSelect[0]],
                      [DB::Raw('detail_timestart'), '<=', $tempDateCheck.' '.$timeSelect[1]],
                    ])
                    ->join('booking', 'booking.booking_ID', '=', 'detail_booking.booking_ID')
                    ->whereIn('booking.status_ID', [1, 3])
                    ->get();
             
        array_push($isReserveEqual, $isReseve);
    }
    
    for ($index = 1; $index < sizeof($isReserveEqual); $index++) {
        $sizeCheck = sizeof($isReserveEqual[0]);
        
        if ($sizeCheck != sizeof($isReserveEqual[$index])) {
            return true;
            break;
        }
    }
    return false;
  }

  public static function CHECK_IS_MATCH_ROOM_OPEN($roomId, $start_date, $end_date = '') {
    $formatter_day_en = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $formatter_day_th = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];

    $temp_day = date_create($start_date);
    $temp_day = date_format($temp_day, 'r');
    $day_start = substr($temp_day, 0, 3);
    $index_day_start = array_search($day_start, $formatter_day_en);

    $temp_end_day = date_create($end_date);
    $temp_end_day = date_format($temp_end_day, 'r');
    $day_end = substr($temp_end_day, 0, 3);
    $index_day_end = array_search($day_end, $formatter_day_en);

    $room_open_time = DB::table('room_open_time')
                            ->select('open_time', 'close_time')
                            ->where('meeting_ID', $roomId)
                            ->where([
                                ['day_id', '>=', $index_day_start + 1],
                                ['day_id', '<=', $index_day_end + 1]
                            ])
                            ->where('open_flag', 1)
                            ->get();

    for ($index = 0; $index < sizeof($room_open_time); $index++) {
        $tmpTimeStart = $room_open_time[0]->open_time;
        $tmpTimeEnd = $room_open_time[0]->close_time;

        if ($tmpTimeStart != $room_open_time[$index]->open_time || $tmpTimeEnd != $room_open_time[$index]->close_time) {
        return true;
        }
    }
    return false;
  }

  public static function getEmailDomainToArray(){
      $arr = array();
      $domains = DB::table('type_email')->get();
      foreach($domains as $dm){
          array_push($arr,$dm->Name_Type);
      }
      return  $arr;
  }

  public static function viewBooking($id){
    $booking = DB::table('booking')
    ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
    ->leftjoin('users','booking.user_ID','=','users.id')
    ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
    ->where('booking.booking_ID',$id)
    ->first();

    $equips = DB::table('booking')
                    ->select(
                        'eq.em_name',
                        DB::raw('sum(dbr.borrow_count) as borrow_count')
                    )
                    ->join('borrow_booking as bbr','booking.booking_ID','=','bbr.booking_ID')
                    ->join('detail_borrow as dbr','bbr.borrow_ID','=','dbr.borrow_ID')
                    ->join('equipment as eq','dbr.equiment_ID','=','em_ID')
                    ->groupBy('eq.em_name')
                    ->where('booking.booking_ID',$id)->get();

    $dateCheckIn = explode("-", $booking->checkin);
    $dateTHCheckIn = $dateCheckIn[2].'-'.$dateCheckIn[1].'-'.($dateCheckIn[0] + 543);
    
    $resultBooking = date("d-m-Y", strtotime(explode(" ", $booking->booking_date)[0]));
    $bookingDate = explode("-", $resultBooking);
    $bookingDate = $bookingDate[0].'-'.$bookingDate[1].'-'.($bookingDate[2] + 543).' ';
    $bookingTHTime = explode(" ", date("d-m-Y H:i",strtotime($booking->booking_date)))[1];
    
    $html = '<table cellpadding=3>
            <tr>
                <td width="100"><b>ห้อง</b></td>
                <td>'.$booking->meeting_name.'</td>
            </tr>
            <tr>
                <td width="100"><b>วันที่</b></td>
                <td>'.$dateTHCheckIn.'</td>
            </tr>
            <tr>
                <td width="100"><b>เวลา</b></td>
                <td>'.substr($booking->detail_timestart, -8,5) .' - '. substr($booking->detail_timeout, -8,5).'</td>
            </tr>
            <tr>
                <td width="100"><b>ผู้จอง</b></td>
                <td>'.((isset($booking->user_name))?$booking->user_name:'-').'</td>
            </tr>
            <tr>
                <td width="100"><b>วันที่จอง</b></td>
                <td>'.$bookingDate.'</td>
            </tr>
            <tr>
                <td width="100"><b>วันเวลาที่ใช้งาน</b></td>
                <td>'.$bookingTHTime.'</td>
            </tr>
            <tr>
                <td valign="top" width="100"><b>การยืมอุปกรณ์</b></td>
                <td>';
    foreach($equips as $eq){
        $html .= '<span>'.$eq->em_name.' x '.$eq->borrow_count.'</span><br>'  ;
    }
                
    $html .=  '</td></tr>';
    $html .= '<tr>
                <td width="100"><b>หมายเหตุ</b></td>
                <td>'.$booking->comment.'</td>
    </tr></table>';
    return $html;
    }
}
