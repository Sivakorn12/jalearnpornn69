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
                            ->get();
        return $dataSection;
    }

    public static function GET_TIMEUSE ($date_select, $times, $checkTimeuse, $id) {
        $datatimes = DB::table('detail_booking')
                            ->where(DB::Raw('SUBSTRING(detail_timestart, 1, 10)'), $date_select)
                            ->where('meeting_ID', $id)
                            ->OrderBy('detail_timestart')
                            ->get();

        if (isset($datatimes)) {
            $timeStart = array();
            foreach ($datatimes as $reserves) {
                for ($index = 0; $index < sizeof($times); $index++) {
                    if (substr($reserves->detail_timestart, -8, 5) == $times[$index]) {
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

    public static function CHECK_TIME_REAMAIN ($id, $time_reserve, $time_select) {
        $temp_date = explode('-', $time_select);
        $date_select = ($temp_date[2] - 543).'-'.$temp_date[1].'-'.$temp_date[0];
        
        $data_openExtra = DB::table('meeting_open_extra')
                                ->where(DB::Raw('SUBSTRING(extra_start, 1, 10)'), $date_select)
                                ->first();

        $time_start = 8;
        $time_end = 16;
        $times = array();
        $checkTimeuse = array();

        if (isset($data_openExtra)) {
            $time_start = substr($data_openExtra->extra_start, -8, 2);
            $time_end = substr($data_openExtra->extra_end, -8, 2);
        }

        for ($index = $time_start; $index < $time_end; $index++) {
            array_push($checkTimeuse, 0);
            if (strlen($index) < 2) {
                array_push($times, '0'.$index.':00');
            } else {
                array_push($times, $index.':00');
            }
        }

        $time_reserve = $time_reserve.':00';
        $count_time = 0;
        $pos_timeuse = array_search($time_reserve, $times);
        
        $datatimes = DB::table('detail_booking')
                            ->where(DB::Raw('SUBSTRING(detail_timestart, 1, 10)'), $date_select)
                            ->where('meeting_ID', $id)
                            ->OrderBy('detail_timestart')
                            ->get();

        if (isset($datatimes)) {
            $timeStart = array();
            foreach ($datatimes as $reserves) {
                if (substr($reserves->detail_timestart, 0, 10) == $date_select) {
                    for ($index = 0; $index < sizeof($times); $index++) {
                        if (substr($reserves->detail_timestart, -8, 5) == $times[$index]) {
                            $hour = substr($reserves->detail_timeout, -8, 2) - substr($reserves->detail_timestart, -8, 2);
                            for ($inner = 0; $inner < $hour; $inner++) {
                            $checkTimeuse[$inner + $index] = 1;
                            }
                        }
                    }
                }
            }
        }

        for ($index = $pos_timeuse; $index < sizeof($checkTimeuse); $index++) {
            if ($checkTimeuse[$index] == 0) {
                $count_time++;
            } else break;
        }
        return $count_time;
    }

    public static function GET_EXTRATIME () {
        $date_now = date('Y-m-d');
        $data_openExtra = DB::table('meeting_open_extra')
                                ->where(DB::Raw('SUBSTRING(extra_start, 1, 10)'), $date_now)
                                ->first();
        return $data_openExtra;
    }
}
