<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Arcanedev\QrCode\QrCode;
use App\func as func;
use Session;
use App\Models\Md_RoomOpenTime;
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
            elseif($status->status_ID==4){
                return '<span class="label label-status label-default">'.$status->status_name.'<span>';
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
                            <th>อุปกรณ์</th>
                            <th>เอกสาร</th>
                            <th>สถานะการจอง</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                <tbody>';
            foreach($bookings as $key => $booking){
                $selectRow = true;
                $chk = (date('Y-m-d')>=$booking->checkin and $booking->status_ID==3 and $booking->detail_timestart<date('Y-m-d H:i:s'));
                if($selected_status == ''){ $selectRow = true;}
                if($selected_status == '1'){ $selectRow = ($booking->status_ID == 1) ;}
                if($selected_status == '3'){ $selectRow = (date('Y-m-d')<$booking->checkin and $booking->status_ID==3 and $booking->detail_timestart>date('Y-m-d H:i:s')) ;}
                $eq_list = explode(",", $booking->eqiupment_list);
                if($selectRow){
                $html=$html.'<tr>
                <td data-toggle="modal" data-target="#booking-detail" data-id="'.$booking->booking_ID.'"><img src="'.url("asset/rooms/".self::getAImage($booking->meeting_pic)).'" width="80"></td>
                <td data-toggle="modal" data-target="#booking-detail" data-id="'.$booking->booking_ID.'">'.$booking->meeting_name.'</td>
                <td data-toggle="modal" data-target="#booking-detail" data-id="'.$booking->booking_ID.'">'.self::dateDBtoBE($booking->checkin).'</td>
                <td data-toggle="modal" data-target="#booking-detail" data-id="'.$booking->booking_ID.'">'.substr($booking->detail_timestart, -8,5).' - '.substr($booking->detail_timeout, -8,5).'</td>
                <td data-toggle="modal" style="text-align:left" data-target="#booking-detail" data-id="'.$booking->booking_ID.'">
                <ul>';
                foreach($eq_list as $eq){
                  if($eq != '') $html=$html.'<li>'.$eq.'</li>';
                }
                $html=$html.'</ul>
                </td>';
                $html=$html.'<td>';
                foreach(officer::getDocumentByBooking($booking->booking_ID) as $doc){
                    $html=$html.'<p><span class="label label-status label-info" ><a href="'.url('download/file').'?filename='.$doc->document_file.'" title="ดาวน์โหลดไฟล์ '.$doc->document_file.'"><i class="fa fa-arrow-down" aria-hidden="true"></i> ดาวน์โหลดเอกสาร</a></span></p>';
                }
                $html=$html.'</td>';
                $html=$html.'<td data-toggle="modal" data-target="#booking-detail" data-id="'.$booking->booking_ID.'">'.(($chk )? '<span class="label label-status label-default">เกินระยะเวลา(ยกเลิก)</span>' :officer::getStatusBooking($booking->status_ID,1)).'</td>';
                $html=$html.'<td>';
                    if($chk){
                        $html=$html.'<i style="color:#777" class=" fa fa-clock-o fa-lg" aria-hidden="true"></i>';
                    }else{
                        if($booking->status_ID==3){
                        $html=$html.'<button type="button" class="btn btn-success" onclick="confirmBooking('.$booking->booking_ID.')"  ><i class="fa fa-check" aria-hidden="true"></i> อนุมัติ</button>
                        <button type="button" class="btn btn-danger" onclick="cancelBooking('.$booking->booking_ID.')"><i class="fa fa-times" aria-hidden="true"></i> ไม่อนุมัติ</button>';
                        }elseif($booking->status_ID==2){
                        $html=$html.'<i class="fa fa-ban fa-lg" aria-hidden="true" style="color: red"></i>';
                        }elseif($booking->status_ID==1){
                        $html=$html.'<i class="fa fa-check-circle fa-lg" aria-hidden="true" style="color: green"></i>';
                        }
                    }
                $html=$html.'</td></tr>';
                }
            }   
            $html=$html.'</tbody>
            </table>';
        return $html; 
    }

    public static function cutStr($str,$len,$tail){
        if(strlen($str) > $len){
            return substr($str,0,$len).$tail;
        }
        else return $str;
    }

    public static function getBookingIDbyBorrow($id){
        $d = DB::table('booking')
                    ->leftjoin('borrow_booking','borrow_booking.booking_ID','=','booking.booking_ID')
                    ->where('borrow_booking.borrow_ID',$id)
                    ->first();
        return $d->booking_ID;
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

    // Equipment
    public static function modalViewDetailBorrow($id,$type='borrow'){
        $html = '';
        $datas = DB::table('detail_borrow')
                ->select(
                    'equipment.em_name',
                    'equipment.em_count',
                    DB::raw('sum(detail_borrow.borrow_count) as borrow_count')
                    )
                ->join('equipment','equipment.em_ID','=','detail_borrow.equiment_ID')
                ->where('detail_borrow.borrow_ID',$id)
                ->groupBy('equipment.em_name','equipment.em_count')
                ->get();
        $html = '<p>
                    รายการยืมอุปกรณ์ หมายเลข :'.$id .(($type=='borrow') ?'<a style="float:right" id="add_borrow" class="btn btn-xs btn-success" ><i class="fa fa-plus" aria-hidden="true"></i> ยืมเพิ่ม</a>':'').
                '</p>
                <table class="table table-bordered" >
                <tr>
                    <th width="25px">ลำดับ</th>
                    <th>อุปกรณ์</th>
                    <th>จำนวน</th>
                    <th></th>
                </tr>';
        foreach($datas as $key => $data){
            $html = $html."<tr>
                            <td>".($key+1)."</td>
                            <td>".$data->em_name."</td>
                            <td>".$data->borrow_count."</td>
                            <td>";
            if(self::getStatusBorrow($id)=='1')
                $html =$html.'<span  style="color:green" class="glyphicon glyphicon-ok-sign" aria-hidden="true" title="เพียงพอ"></span>';
            elseif(self::getStatusBorrow($id)=='4')
                $html =$html.'<span  style="color:orange" class="glyphicon glyphicon-remove-sign" aria-hidden="true" title="ยกเลิก"></span>';
            else $html =$html.self::statusEquip($data->borrow_count,$data->em_count);
            $html = $html.'</td></tr>';
        }

        $html = $html."</table><br>";

        if($type=='borrow'){
            if(self::checkBtnConfirmBorrow($id)){
                $html = $html.'<a class="btn btn-primary" onclick="return confirm(`คุณต้องการอนุมัติการยืมอุปกรณ์นี้หรือไม่`)"  href="'.url('control/return-eq/confirm/'.$id).'" role="button">ยืนยัน</a>
                              <a class="btn btn-danger"onclick="return confirm(`คุณต้องการยกเลิกการยืมอุปกรณ์นี้หรือไม่`)"  href="'.url('control/return-eq/cancel/'.$id).'" role="button">ยกเลิก</a>';
            }
            $dataEquipment = func::GET_EQUIPMENT();
            $action = url('').'/control/return-eq/borrow';
            $html =$html.'<div id = "form_borrow" style="display:none"><hr>
                            <form action="'.$action.'" method="post"  class="form-horizontal" > 
                            <input type="hidden" name="id" value="'.$id.'">
                            <div class="form-group">
                            <label class="col-sm-2 control-label">อุปกรณ์</label>
                            <div class="col-sm-5">
                            <select class="sectionlist form-control" id="input-equip-name">';
            foreach($dataEquipment as $equipment){
                $html = $html.'<option value="'.$equipment->em_name.'">'.$equipment->em_name.' : (เหลือจำนวน '.$equipment->em_count.')</option>';
            }   
            $html = $html.'</select>
                            </div>
                            <label class="col-sm-1 control-label">จำนวน</label>
                            <div class="col-sm-2">
                                    <input type="number" class="form-control" min="1" id="input-equip-amount">
                            </div>
                            <div class="col-sm-1 control-label" >
                                <button style="padding-top: 0px" type="button" class="btn btn-default btn-circle" onclick="addEquioment()">
                                    <i style="margin-top:8px"class="fa fa-lg fa-plus" aria-hidden="true"></i>
                                </button>
                            </div>
                            </div>
                            <div class="form-group form-room" id="div-show-equip" style="display:none">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-7">
                                <ul style="-webkit-padding-start: 15px;" id="list-equip">
                                </ul>
                            </div>
                            </div>
                            <div class="form-group form-room">
                            <div class="col-sm-7" style="text-align:center">
                            <div id="hideEquip"></div>
                            <input type="hidden" name="_token" id="csrf-token" value="'.Session::token().'">
                            <input type="submit" class="btn btn-primary" value="ตกลง">
                            <input type="button" id="cancel_borrow" class="btn btn-default" value="ยกเลิก">
                            </div>
                            </div>
                            </form>
                        </div>';

            
        }
        elseif($type=='returnBooking'){
            if(self::checkBtnConfirmReturn($id)){
                $html = $html.'<a class="btn btn-primary" onclick="return confirm(`คุณแน่ใจว่าอุปกรณ์เหล่านี้ถูกคืนครบเเล้วหรือไม่`)"  href="'.url('control/return-eq/confirm-return/'.$id).'" role="button">คืนอุปกรณ์</a>';
            }
        }
        return $html;
    }

    public static function getStatusBorrow($id){
        $data = DB::table('borrow_booking')->select('borrow_status')->where('borrow_ID',$id)->first();
        return $data->borrow_status;
    }

    public static function statusEquip($value,$total){
        if($value <= $total){
            return '<span  style="color:green" class="glyphicon glyphicon-ok-sign " aria-hidden="true" title="เพียงพอ"></span>';
        }
        else return '<span style="color:red" class="glyphicon glyphicon-remove-sign" aria-hidden="true" title="ไม่เพียงพอ"></span>';
    }

    public static function checkBtnConfirmBorrow($id){
        $borrow = DB::table('borrow_booking')->select('borrow_status')->where('borrow_ID',$id)->where('borrow_date','>=', date('Y-m-d',strtotime(date('Y-m-d'). "+1 days")))->first();
        if(isset($borrow)  and $borrow->borrow_status == '3') 
            return true;
        return false;
    }

    public static function checkBtnConfirmReturn($id){
        $returnBooking = DB::table('booking')
                  ->join('borrow_booking','borrow_booking.booking_ID','=','booking.booking_ID')
                  ->join('return_booking','return_booking.booking_ID','=','booking.booking_ID')
                  ->where('borrow_booking.borrow_ID',$id)
                  ->first();
        if(!isset($returnBooking)) 
            return true;
        return false;
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
            "#3a87ad","#ff4d4d","#33cc33","#ff981a","#ff531a","#8600b3","#8080ff","#ff1a8c","#ccffcc","#4dff4d","#85adad","#660000","#ff99ff","#ffcc99","#c3c388","#3a87ad","#003300","#3d5c5c","#33331a","#ff8000",
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
        //dd($equip);

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
                    <td width="100"><b>เวลาที่จอง</b></td>
                    <td>'.$bookingTHTime.'</td>
                </tr>
                <tr>
                    <td valign="top" width="100"><b>การยืมอุปกรณ์</b></td>
                    <td>';
        foreach($equips as $eq){
            $html .= '<span>'.$eq->em_name.' x '.$eq->borrow_count.'</span><br>'  ;
        }
                    
        $html .=    '</td>';
        $html .= (($booking->link??null) != null) ? '<tr><td><b>แบบประเมิน</b></td><td><a onclick="window.open(\''.$booking->link.'\')">ดูผลการประเมิน</a></td>': '';
        $html .= '</tr>
            </table>';
        return $html;
    }

    public static function genQR_code($link){
        $qrCode ='';
            if(isset($link)){
                $qrCode = new QrCode;
                $qrCode->setText($link);
                $qrCode->setSize(150);
                return $qrCode->image("image alt", ['class' => 'qr-code-img']);
            }
    }

    public static function getDataBorrow($type=''){
        if($type == ''){
            return self::getallDataBorrow();
        }
        elseif($type = 'today'){
            return self::getDataBorrowToday();
        }

    }

    public static function getDataBorrowToday(){
        return DB::table('booking')
                ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
                ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
                ->join('borrow_booking','borrow_booking.booking_ID','=','booking.booking_ID')
                ->orderBy('detail_booking.detail_timestart','desc')
                ->select(
                    "booking.booking_ID",
                    "booking.status_ID",
                    "booking.section_ID",
                    "booking.booking_name",
                    "booking.booking_phone",
                    "booking.booking_date",
                    "booking.checkin",
                    "detail_booking.detail_topic",
                    "detail_booking.detail_timestart",
                    "detail_booking.detail_timeout",
                    "meeting_room.meeting_name",
                    "borrow_booking.borrow_ID",
                    "borrow_booking.borrow_date",
                    "borrow_booking.borrow_status"
                )
                ->where('borrow_booking.borrow_date' ,date('Y-m-d'))
                ->get();
    }

    public static function getallDataBorrow(){
        return DB::table('booking')
                ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
                ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
                ->join('borrow_booking','borrow_booking.booking_ID','=','booking.booking_ID')
                ->orderBy('detail_booking.detail_timestart','desc')
                ->select(
                    "booking.booking_ID",
                    "booking.status_ID",
                    "booking.section_ID",
                    "booking.booking_name",
                    "booking.booking_phone",
                    "booking.booking_date",
                    "booking.checkin",
                    "detail_booking.detail_topic",
                    "detail_booking.detail_timestart",
                    "detail_booking.detail_timeout",
                    "meeting_room.meeting_name",
                    "borrow_booking.borrow_ID",
                    "borrow_booking.borrow_date",
                    "borrow_booking.borrow_status"
                )
                ->get();
    }

    public static function getDataBorrow_success(){
        return DB::table('booking')
                ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
                ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
                ->join('borrow_booking','borrow_booking.booking_ID','=','booking.booking_ID')
                ->leftjoin('return_booking','return_booking.booking_ID','=','booking.booking_ID')
                ->where('borrow_booking.borrow_status',1)
                ->orderBy('detail_booking.detail_timestart','desc')
                ->select(
                    "booking.booking_ID",
                    "booking.status_ID",
                    "booking.section_ID",
                    "booking.booking_name",
                    "booking.booking_phone",
                    "booking.booking_date",
                    "booking.checkin",
                    "detail_booking.detail_topic",
                    "detail_booking.detail_timestart",
                    "detail_booking.detail_timeout",
                    "meeting_room.meeting_name",
                    "borrow_booking.borrow_ID",
                    "borrow_booking.borrow_date",
                    "borrow_booking.borrow_status"
                )
                ->get();
    }

    public static function getDataReturn_success(){
        return DB::table('booking')
                ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
                ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
                ->join('borrow_booking','borrow_booking.booking_ID','=','booking.booking_ID')
                ->join('return_booking','return_booking.booking_ID','=','booking.booking_ID')
                ->where('borrow_booking.borrow_status',1)
                ->orderBy('detail_booking.detail_timestart','desc')
                ->select(
                    "booking.booking_ID",
                    "booking.status_ID",
                    "booking.section_ID",
                    "booking.booking_name",
                    "booking.booking_phone",
                    "booking.booking_date",
                    "booking.checkin",
                    "detail_booking.detail_topic",
                    "detail_booking.detail_timestart",
                    "detail_booking.detail_timeout",
                    "meeting_room.meeting_name",
                    "borrow_booking.borrow_ID",
                    "borrow_booking.borrow_date",
                    "borrow_booking.borrow_status"
                )
                ->get();
    }

    public static function isReturnEquipment($booking_ID){
        $returnBooking = DB::table('return_booking')
                  ->where('booking_ID',$booking_ID)
                  ->first();
        if(isset($returnBooking)) 
            return true;
        return false;
    }

    public static function dateDBtoBE($date){
        $dt = explode(" ", $date);
        if(sizeof($dt)>1){
            $d = explode("-", $dt[0]);
            $dd = $d[2];
            $mm = $d[1];
            $yy = $d[0]+543;
            $time = substr($dt[1],0,5);
            return $dd.'-'.$mm.'-'.$yy.' '.$time;
        }
        else{
            $d = explode("-", $dt[0]);
            $dd = $d[2];
            $mm = $d[1];
            $yy = $d[0]+543;
            return $dd.'-'.$mm.'-'.$yy;
        }
    }



    public static function getBuilding(){
        return DB::table('building')->get();
    }

    public static function setDefaultRoomOpenTime(){
        return array(
            [ "day_id" => 1,"open_time"=>"08:00", "close_time"=>"16:00" ,"open_flag" => 0 ],
            [ "day_id" => 2,"open_time"=>"08:00", "close_time"=>"16:00" ,"open_flag" => 1 ],
            [ "day_id" => 3,"open_time"=>"08:00", "close_time"=>"16:00" ,"open_flag" => 1 ],
            [ "day_id" => 4,"open_time"=>"08:00", "close_time"=>"16:00" ,"open_flag" => 1 ],
            [ "day_id" => 5,"open_time"=>"08:00", "close_time"=>"16:00" ,"open_flag" => 1 ],
            [ "day_id" => 6,"open_time"=>"08:00", "close_time"=>"16:00" ,"open_flag" => 1 ],
            [ "day_id" => 7,"open_time"=>"08:00", "close_time"=>"16:00" ,"open_flag" => 0 ]
        );
    }

    public static function getShortDayThai(){
        return array(
            '1' => 'อา.',
            '2' => 'จ.',
            '3' => 'อ.',
            '4' => 'พ.',
            '5' => 'พฤ.',
            '6' => 'ศ.',
            '7' => 'ส.'
        );
    }

    public static function setRoomOpenAllDay($req,$meeting_ID){

            Md_RoomOpenTime::updateOrCreate([
                'day_id' => 1,
                'meeting_ID' => $meeting_ID
            ],[
                'open_time' => $req->room_open_1 ?? '08:00',
                'close_time' => $req->room_close_1 ?? '16:00',
                'open_flag' => (isset($req->open_flag_1)) ? 1:0
            ]);

            Md_RoomOpenTime::updateOrCreate([
                'day_id' => 2,
                'meeting_ID' => $meeting_ID
            ],[
                'open_time' => $req->room_open_2 ?? '08:00',
                'close_time' => $req->room_close_2 ?? '16:00',
                'open_flag' => (isset($req->open_flag_2)) ? 1:0
            ]);

            Md_RoomOpenTime::updateOrCreate([
                'day_id' => 3,
                'meeting_ID' => $meeting_ID
            ],[
                'open_time' => $req->room_open_3 ?? '08:00',
                'close_time' => $req->room_close_3 ?? '16:00',
                'open_flag' => (isset($req->open_flag_3)) ? 1:0
            ]);

            Md_RoomOpenTime::updateOrCreate([
                'day_id' => 4,
                'meeting_ID' => $meeting_ID
            ],[
                'open_time' => $req->room_open_4 ?? '08:00',
                'close_time' => $req->room_close_4 ?? '16:00',
                'open_flag' => (isset($req->open_flag_4)) ? 1:0
            ]);

            Md_RoomOpenTime::updateOrCreate([
                'day_id' => 5,
                'meeting_ID' => $meeting_ID
            ],[
                'open_time' => $req->room_open_5 ?? '08:00',
                'close_time' => $req->room_close_5 ?? '16:00',
                'open_flag' => (isset($req->open_flag_5)) ? 1:0
            ]);

            Md_RoomOpenTime::updateOrCreate([
                'day_id' => 6,
                'meeting_ID' => $meeting_ID
            ],[
                'open_time' => $req->room_open_6 ?? '08:00',
                'close_time' => $req->room_close_6 ?? '16:00',
                'open_flag' => (isset($req->open_flag_6)) ? 1:0
            ]);

            Md_RoomOpenTime::updateOrCreate([
                'day_id' => 7,
                'meeting_ID' => $meeting_ID
            ],[
                'open_time' => $req->room_open_7 ?? '08:00',
                'close_time' => $req->room_close_7 ?? '16:00',
                'open_flag' => (isset($req->open_flag_7)) ? 1:0
            ]);
    }

    public static function cancelBorrowEquipment($booking_ID){
        
        $data_borrow = DB::table('borrow_booking')
                            ->join('detail_borrow as dbr','borrow_booking.borrow_ID','=','dbr.borrow_ID')
                            ->where('borrow_booking.booking_ID',$booking_ID)
                            ->get();
        if(isset($data_borrow)){
            //set status borrow = 4
            DB::table('borrow_booking')
                ->where('booking_ID',$booking_ID)
                ->update([
                    'borrow_status' => 4
                ]);
            //for loop to plus equipment 
            foreach($data_borrow as $dbr){
                DB::table('equipment')->where('em_ID', $dbr->equiment_ID)->increment('em_count',$dbr->borrow_count);
            }
        }   
    }

    public static function checkreserv($room_id){
        $date = date('Y-m-d');

        //dd($date);
        $data_reserv = DB::table('booking')
                            ->join('detail_booking as dtb','booking.booking_ID','=','dtb.booking_ID')
                            ->where('booking.checkin','>=',$date)
                            ->where('dtb.meeting_ID',$room_id)
                            ->get();
        $data_room_open = Md_RoomOpenTime::where('meeting_ID',$room_id)->get()->toArray();
        foreach($data_reserv as $key => $drs){
            $day_id = (date("N", strtotime($drs->checkin." 00:00:00"))+1);
            $time_start = date("H:i:s", strtotime($drs->detail_timestart));
            $time_end = date("H:i:s", strtotime($drs->detail_timeout));

            echo "check in at :".$drs->checkin." day: ".(date("N", strtotime($drs->checkin." 00:00:00"))+1)." time st:".$time_start."<br>";
            if($data_room_open[$day_id-1]["open_flag"]==0 or $data_room_open[$day_id-1]["open_time"] > $time_start or $data_room_open[$day_id-1]["close_time"] < $time_end ){
                $old_status = DB::table('booking')->select('status_ID')->where('booking_ID',$drs->booking_ID)->first();
                if(isset($old_status) and $old_status->status_ID ==1 ){
                    officer::cancelBorrowEquipment($drs->booking_ID);
                }

                DB::table('booking')
                ->where('booking_ID',$drs->booking_ID)
                ->update([
                    'status_ID' => 4
                ]);
                echo "canceled reserve.<br>";
            }
        }
        //dd($data_reserv);
    }

    public static function isHasReserveRoom($meeting_id,$date_point){
        $result = DB::table('booking')
                    ->join('detail_booking as dbk','booking.booking_ID','=','dbk.booking_ID')
                    ->where('dbk.meeting_ID',$meeting_id)
                    ->where('booking.checkin',$date_point)
                    ->get(); 
        if(isset($result[0])){
            return $result;
        }
        return false;
    }

    public static function isHoliday($date_point){
        $result = DB::table('holiday')
                    ->where('holiday_start','<=',$date_point)
                    ->where('holiday_end','>=',$date_point)
                    ->get(); 
        if(isset($result[0])){
            return true;
        }
        return false;
    }

    public static function isRoomOpenExtra($meeting_id,$open_time,$close_time){
        $result = DB::table('meeting_open_extra')
                    ->WhereBetween('extra_start', [$open_time, $close_time])
                    ->orWhereBetween('extra_end', [$open_time, $close_time])
                    ->get(); 
        if(isset($result[0])){   
            return $result[0];
        }
        else{
            $result2 = DB::table('meeting_over_time')
                    ->WhereBetween('start_date', [$open_time, $close_time])
                    ->orWhereBetween('end_date', [$open_time, $close_time])
                    ->get(); 
            if(isset($result2[0])){   
                return $result2[0];
            }else{
                return false;
            }
        }
        return false;
    }
    
    public static function getRoomOpen($meeting_ID){
        return Md_RoomOpenTime::select(
                    'room_open_time.*',
                    DB::raw('
                    CASE
                        WHEN day_id = 1 THEN "อา."
                        WHEN day_id = 2 THEN "จ."
                        WHEN day_id = 3 THEN "อ."
                        WHEN day_id = 4 THEN "พ."
                        WHEN day_id = 5 THEN "พฤ."
                        WHEN day_id = 6 THEN "ศ."
                        WHEN day_id = 7 THEN "ส."
                    END as day
                    ')
                )
                ->where('meeting_ID',$meeting_ID)->where('open_flag',1)
                ->get();
    }

    public static function getEstimateLink($meeting_id){
        $data_meetingroom = DB::table('meeting_room')
        ->where('meeting_ID', $meeting_id)
        ->first();

        $estimate_link = $data_meetingroom->estimate_link.'#responses';
        return $estimate_link;
    }

    public static function getDocumentByBooking($booking_id){
        $res = DB::table('document')->where('booking_id',$booking_id)->get();
        return $res;
    }

    public static function setDataBtnReserve($time_btn,$time_start,$time_end){
        for($p = 0 ;$p<sizeof($time_btn);$p++){
            if($time_btn[$p]["index"] < $time_start or $time_btn[$p]["index"] > $time_end){
                $time_btn[$p]["can_book"] = 0;
            }
        }
        return $time_btn;
    }
}
