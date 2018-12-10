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
        $temp_date = explode('-', $time_select);
        $date_select = ($temp_date[2] - 543).'-'.$temp_date[1].'-'.$temp_date[0];

        $data_openExtra = DB::table('meeting_open_extra')
                                ->where(DB::Raw('SUBSTRING(extra_start, 1, 10)'), $date_select)
                                ->first();

        $data_open_over_time = DB::table('meeting_over_time')
                                    ->where([
                                        [DB::Raw('SUBSTRING(start_date, 1, 10)'), '<=', $date_select],
                                        [DB::Raw('SUBSTRING(end_date, 1, 10)'), '>=', $date_select]
                                    ])
                                    ->first();

        $time_start = 8;
        $time_end = 16;
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
            if (($checkTimeuse[$index] == 0 && $checktimeReserve[$index] == 1) && $count == 0) {
                array_push($bookingStart, $times[$index]);
                $count++;
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

        return array($bookingStart, $bookingEnd);
    }

    public static function GET_EXTRATIME () {
        $date_now = date('Y-m-d');
        $data_openExtra = DB::table('meeting_open_extra')
                                ->where(DB::Raw('SUBSTRING(extra_start, 1, 10)'), $date_now)
                                ->first();
        return $data_openExtra;
    }

    public static function SET_DATA_BOOKING ($req, $time_start, $time_out,$status=3) {
        $data_meetingroom = DB::table('meeting_room')
                                ->where('meeting_ID', $req->meeting_id)
                                ->first();

        $estimate_link = $data_meetingroom->estimate_link.'#responses';
        $id_insert = array();
        for ($index = 0; $index < sizeof($time_start); $index++) {
            $id = DB::table('booking')
                            ->insertGetId([
                                'status_ID' => $status,
                                'section_ID' => $req->section_id ?? null,
                                'department_ID' => $req->department_id ?? null,
                                'faculty_ID' => $req->faculty_id ?? null,
                                'institute_ID' => isset($req->institute_id)? $req->institute_id : null,
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
        return $id_insert;
    }

    public static function UPDATE_DATA_BOOKING ($req, $time_start, $time_out) {
        $id_insert = DB::table('booking')
                        ->where('booking_ID', $req->booking_id)
                        ->update([
                            'section_ID' => isset($req->section_id)? $req->section_id : null,
                            'institute_ID' => isset($req->institute_id)? $req->institute_id : null,
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

    public static function SET_DATA_BORROW ($id_equipment, $count_equipment, $id_insert_booking, $time_select,$reduce_equipment_now = false,$accept_borrow= false) {
        $id_borrow_booking = array();
        $borrow_status = ($accept_borrow)?1:3;
        if (is_array($id_insert_booking)) {
            for ($index = 0; $index < sizeof($id_insert_booking); $index++) {
                $id = DB::table('borrow_booking')
                                        ->insertGetId([
                                            'booking_ID' => $id_insert_booking[$index],
                                            'borrow_date' => $time_select,
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
}
