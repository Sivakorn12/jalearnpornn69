<?php
use App\Officer as officer;
if($type == 'all'){
  $selected_status = '';
}
elseif($type == 'wait'){
  $selected_status = '3';
}
elseif($type == 'confirmed'){
  $selected_status = '1';
}
?>

<table class="table table-hover showroom" id="tb-{{$type}}">
    <thead>
        <tr>
            <th>#</th>
            <th>ห้อง</th>
            <th>วันที่</th>
            <th>เวลา</th>
            <th>อุปกรณ์</th>
            <th>สถานะ</th>
            <th></th>
        </tr>
    </thead>
   <tbody>
    @foreach($bookings as $key => $booking)
      <?php
        $selectRow = true;
        $chk = (date('Y-m-d')>=$booking->checkin and $booking->status_ID==3 and $booking->detail_timestart<date('Y-m-d H:i:s'));
        if($selected_status == ''){ $selectRow = true;}
        if($selected_status == '1'){ $selectRow = ($booking->status_ID == 1) ;}
        if($selected_status == '3'){ $selectRow = (date('Y-m-d')<$booking->checkin and $booking->status_ID==3 and $booking->detail_timestart>date('Y-m-d H:i:s')) ;}
        $eq_list = explode(",", $booking->eqiupment_list);
      ?>
      @if($selectRow) 
          <tr>
              <td data-toggle="modal" data-target="#booking-detail" data-id="{{$booking->booking_ID}}"><img src='{{url ("asset/rooms/".officer::getAImage($booking->meeting_pic))}}' width="80"></td>
              <td data-toggle="modal" data-target="#booking-detail" data-id="{{$booking->booking_ID}}">{{$booking->meeting_name}}</td>
              <td data-toggle="modal" data-target="#booking-detail" data-id="{{$booking->booking_ID}}">{{officer::dateDBtoBE($booking->checkin)}}</td>
              <td data-toggle="modal" style="text-align:left" data-target="#booking-detail" data-id="{{$booking->booking_ID}}">
                <ul>
                @foreach($eq_list as $eq)
                  @if($eq != '') <li>{{$eq}}</li> @endif
                @endforeach
                </ul>
              </td>
              <td data-toggle="modal" data-target="#booking-detail" data-id="{{$booking->booking_ID}}">{{substr($booking->detail_timestart, -8,5)}} - {{substr($booking->detail_timeout, -8,5)}}</td>
              <td data-toggle="modal" data-target="#booking-detail" data-id="{{$booking->booking_ID}}">
                {!!($chk )? '<span class="label label-status label-default">เกินระยะเวลา(ยกเลิก)</span>' :officer::getStatusBooking($booking->status_ID,1)!!}
              </td>
              <td>
              @if($chk)
                <i style="color:#777" class=" fa fa-clock-o fa-lg" aria-hidden="true"></i>
              @else 
                @if($booking->status_ID==3)
                  <button type="button" class="btn btn-success" onclick="confirmBooking('{{$booking->booking_ID}}')" ><i class="fa fa-check" aria-hidden="true"></i> อนุมัติ</button>
                  <button type="button" class="btn btn-danger" onclick="cancelBooking('{{$booking->booking_ID}}')"><i class="fa fa-times" aria-hidden="true"></i> ไม่อนุมัติ</button>
                @elseif($booking->status_ID==2)
                  <i class="fa fa-ban fa-lg" aria-hidden="true" style="color: red"></i>
                @elseif($booking->status_ID==1)
                  <i class="fa fa-check-circle fa-lg" aria-hidden="true" style="color: green"></i>
                @endif
              @endif
              </td>
          </tr>

      @endif
     @endforeach
   </tbody>
</table>
<script>

$(document).ready(function() {
  $('#tb-{{$type}}').DataTable();
});


</script>   
