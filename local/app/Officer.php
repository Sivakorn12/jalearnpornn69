<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Officer extends Model
{
    //*********************** Booking
    public static function getStatusBooking($id){
        $status = DB::table('status_room')
                  ->where('status_ID',$id)
                  ->first();
        return $status->status_name;
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
                <td data-toggle="modal" data-target="#booking-detail" data-id="'.$booking->booking_ID.'">'.(($chk )? 'รออนุมัติ(ยกเลิก)' :officer::getStatusBooking($booking->status_ID)).'</td>
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


    // *********************** Room 
    public static function getTypeRoom(){
        return DB::table('meeting_type')->get();
    }

    public static function deleteFile($filename){
        if(file_exists($filename)){
            unlink( $filename );
            if(file_exists($filename)) echo 'Check permission on file '.$filename;
            else echo 'Delete file complete';
        }else echo 'file not found ' . $filename;
    }

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
            "#3a87ad","#ff4d4d","#33cc33","#ffff1a",
            "#ff531a","#8600b3","#8080ff","#ff1a8c",
            "#ccffcc","#4dff4d","#85adad","#660000",
            "#ff99ff","#ffcc99","#c3c388","#3a87ad",
            "#003300","#3d5c5c","#33331a","#ff8000",
            "#3a87ad","#3a87ad","#3a87ad","#3a87ad",
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
        //dd($booking);
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
