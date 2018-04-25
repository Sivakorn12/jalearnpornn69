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
                <td data-toggle="modal" data-target="#booking-detail" data-id="'.$booking->booking_ID.'"><img src="'.url("asset/rooms/".$booking->meeting_pic).'" width="80"></td>
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

}
