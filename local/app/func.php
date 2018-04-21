<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class func extends Model
{
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
                                            <img src='".$room->meeting_pic."' width='100'>
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
        if (count($check_reserve) == 0) {
            return null;
        } else {
            return $check_reserve;
        }
    }

    public static function Getequips ($id) {
        $dataEquips = DB::table('equipment_in')
                            ->where('meeting_ID', $id)
                            ->get();
        return $dataEquips;
    }

    public static function queryReserveTime ($id) {
        $checktime = DB::table('detail_booking')
                            ->where('meeting_ID', $id)
                            ->first();
        return $checktime;
    }
    
}
