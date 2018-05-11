<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Officer extends Model
{
    //* Booking
    public static function getStatusBooking($id,$type=0){
        $status = DB::table('status_room')
                  ->where('status_ID',$id)
                  ->first();
        if($type==0)
            return $status->status_name;
        else{
            if($status->status_ID==1){
                return '<span class="label label-status label-success">'.$status->status_name.'<span>';
            }
            elseif($status->status_ID==2){
                return '<span class="label label-status label-danger">'.$status->status_name.'<span>';
            }
            elseif($status->status_ID==3){
                return '<span class="label label-status label-warning">'.$status->status_name.'<span>';
            }
        }
    }

    public static function pushTableBooking($bookings,$type){
        if($type == 'all'){
                $selected_status = '';
        }
        elseif($type == 'wait'){
                $selected_status = '3';
        }
            elseif($type == 'confirmed'){
                $selected_status = '1';
        }
        $html = '<table class="table table-hover showroom" id="tb-'.$type.'">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ห้อง</th>
                            <th>วันที่</th>
                            <th>เวลา</th>
                            <th>สถานะ</th>
                            <th></th>
                        </tr>
                    </thead>
                <tbody>';
            foreach($bookings as $key => $booking){
                $selectRow = true;
                $chk = (date('Y-m-d')>=$booking->checkin and $booking->status_ID==3 and $booking->detail_timestart<date('Y-m-d H:i:s'));
                if($selected_status == ''){ $selectRow = true;}
                if($selected_status == '1'){ $selectRow = ($booking->status_ID == 1) ;}
                if($selected_status == '3'){ $selectRow = (date('Y-m-d')<$booking->checkin and $booking->status_ID==3 and $booking->detail_timestart>date('Y-m-d H:i:s')) ;}
                if($selectRow){
                $html=$html.'<tr>
                <td data-toggle="modal" data-target="#booking-detail" data-id="'.$booking->booking_ID.'"><img src="'.url("asset/rooms/".self::getAImage($booking->meeting_pic)).'" width="80"></td>
                <td data-toggle="modal" data-target="#booking-detail" data-id="'.$booking->booking_ID.'">'.$booking->meeting_name.'</td>
                <td data-toggle="modal" data-target="#booking-detail" data-id="'.$booking->booking_ID.'">'.$booking->checkin.'</td>
                <td data-toggle="modal" data-target="#booking-detail" data-id="'.$booking->booking_ID.'">'.substr($booking->detail_timestart, -8,5).' - '.substr($booking->detail_timeout, -8,5).'</td>
                <td data-toggle="modal" data-target="#booking-detail" data-id="'.$booking->booking_ID.'">'.(($chk )? '<span class="label label-status label-default">เกินระยะเวลา(ยกเลิก)</span>' :officer::getStatusBooking($booking->status_ID,1)).'</td>
                <td>';
                    if($chk){
                        $html=$html."ไม่อยู่ในช่วงเวลา";
                    }else{
                        if($booking->status_ID==3){
                        $html=$html.'<button type="button" class="btn btn-success" onclick="confirmBooking('.$booking->booking_ID.')"  ><i class="fa fa-check" aria-hidden="true"></i> อนุมัติ</button>
                        <button type="button" class="btn btn-danger" onclick="cancelBooking('.$booking->booking_ID.')"><i class="fa fa-times" aria-hidden="true"></i> ไม่อนุมัติ</button>';
                        }elseif($booking->status_ID==2){
                        $html=$html.'<i class="fa fa-ban fa-lg" aria-hidden="true" style="color: red"></i>';
                        }elseif($booking->status_ID==1){
                        $html=$html.'<i class="fa fa-check-circle fa-lg" aria-hidden="true" style="color: green"></i>';
                        }
                    $html=$html.'</td>
                    </tr>';
                    }
                }
            }   
            $html=$html.'</tbody>
            </table>';
        return $html; 
    }


    // * Room 
    public static function getTypeRoom(){
        return DB::table('meeting_type')->get();
    }

    public static function getEquips($meetingId){
        return DB::table('equipment_in')->where('meeting_ID',$meetingId)->get();
    }

    // *Reserve

    public static function deleteFile($filename){
        if(file_exists($filename)){
            unlink( $filename );
            if(file_exists($filename)) echo 'Check permission on file '.$filename;
            else echo 'Delete file complete';
        }else echo 'file not found ' . $filename;
    }

    // *ETC
    public static function getAImage($imageName){
        $images = explode(',',$imageName);
        return $images[0];
    }

    public static function GET_TIMEUSE ($date_select, $id) {
        $times = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00'];
        $checkTimeuse = ['0', '0', '0', '0', '0', '0', '0', '0'];
        
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

    public static function colorEvents(){
        return array(
            "#3a87ad","#ff4d4d","#33cc33","#ffff1a","#ff531a","#8600b3","#8080ff","#ff1a8c","#ccffcc","#4dff4d","#85adad","#660000","#ff99ff","#ffcc99","#c3c388","#3a87ad","#003300","#3d5c5c","#33331a","#ff8000",
            "#3a87ad","#3a87ad","#3a87ad","#3a87ad","#F0F8FF","#FAEBD7","#00FFFF","#7FFFD4","#F0FFFF","#F5F5DC","#FFE4C4","#000000","#FFEBCD","#0000FF","#8A2BE2","#A52A2A","#DEB887","#5F9EA0","#7FFF00","#D2691E",
            "#FF7F50","#6495ED","#FFF8DC","#DC143C","#00FFFF","#00008B","#008B8B","#B8860B","#A9A9A9","#A9A9A9","#006400","#BDB76B","#8B008B","#556B2F","#FF8C00","#9932CC","#8B0000","#E9967A","#8FBC8F","#483D8B",
            "#2F4F4F","#2F4F4F","#00CED1","#9400D3","#FF1493","#00BFFF","#696969","#696969","#1E90FF","#B22222","#FFFAF0","#228B22","#FF00FF","#DCDCDC","#F8F8FF","#FFD700","#DAA520","#808080","#808080","#008000",
            "#ADFF2F","#F0FFF0","#FF69B4","#CD5C5C","#4B0082","#FFFFF0","#F0E68C","#E6E6FA","#FFF0F5","#7CFC00","#FFFACD","#ADD8E6","#F08080","#E0FFFF","#FAFAD2","#D3D3D3","#D3D3D3","#90EE90","#FFB6C1","#FFA07A",
            "#20B2AA","#87CEFA","#778899","#778899","#B0C4DE","#FFFFE0","#00FF00","#32CD32","#FAF0E6","#FF00FF","#800000","#66CDAA","#0000CD","#BA55D3","#9370DB","#3CB371","#7B68EE","#00FA9A","#48D1CC","#C71585",
            "#191970","#F5FFFA","#FFE4E1","#FFE4B5","#FFDEAD","#000080","#FDF5E6","#808000","#6B8E23","#FFA500","#FF4500","#DA70D6","#EEE8AA","#98FB98","#AFEEEE","#DB7093","#FFEFD5","#FFDAB9","#CD853F","#FFC0CB",
            "#DDA0DD","#B0E0E6","#800080","#663399","#FF0000","#BC8F8F","#4169E1","#8B4513","#FA8072","#F4A460","#2E8B57","#FFF5EE","#A0522D","#C0C0C0","#87CEEB","#6A5ACD","#708090","#708090","#FFFAFA","#00FF7F",
            "#4682B4","#D2B48C","#008080","#D8BFD8","#FF6347","#40E0D0","#EE82EE","#F5DEB3","#FFFFFF","#F5F5F5","#FFFF00","#9ACD32"
        );
    }
    
    public static function dateFormatDB($dateHtml){
        $dateArr = explode("-",$dateHtml);
        return ($dateArr[2]-543)."-".$dateArr[1]."-".$dateArr[0];
    }

    public static function viewBooking($id){
        $booking = DB::table('booking')
        ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
        ->leftjoin('users','booking.user_ID','=','users.id')
        ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
        ->where('booking.booking_ID',$id)
        ->first();
        
        $html = '<table cellpadding=3>
                <tr>
                    <td width="120"><b>รหัสการจอง</b></td>
                    <td>'.$booking->booking_ID.'</td>
                </tr>
                <tr>
                    <td width="100"><b>ห้อง</b></td>
                    <td>'.$booking->meeting_name.'</td>
                </tr>
                <tr>
                    <td width="100"><b>วันที่</b></td>
                    <td>'.$booking->checkin.'</td>
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
                    <td width="100"><b>วันเวลาที่จอง</b></td>
                    <td>'.$booking->booking_date.'</td>
                </tr>
            </table>';
        return $html;
    }

}
