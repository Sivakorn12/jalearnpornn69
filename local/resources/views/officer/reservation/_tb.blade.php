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
            <th>สถานะ</th>
            <th></th>
        </tr>
    </thead>
   <tbody>
    @foreach($bookings as $key => $booking)
      @if($selected_status == $booking->status_ID or $selected_status=='')
      <tr>
          <td data-toggle="modal" data-target="#booking-detail" data-id="{{$booking->booking_ID}}"><img src='{{url ("asset/".$booking->meeting_pic)}}' width="80"></td>
          <td data-toggle="modal" data-target="#booking-detail" data-id="{{$booking->booking_ID}}">{{$booking->meeting_name}}</td>
          <td data-toggle="modal" data-target="#booking-detail" data-id="{{$booking->booking_ID}}">{{$booking->checkin}}</td>
          <td data-toggle="modal" data-target="#booking-detail" data-id="{{$booking->booking_ID}}">{{substr($booking->detail_timestart, -8,5)}} - {{substr($booking->detail_timeout, -8,5)}}</td>
          <td data-toggle="modal" data-target="#booking-detail" data-id="{{$booking->booking_ID}}">55</td>
          <td>
            @if($booking->status_ID==3)
              <button type="button" class="btn btn-success" data-id="{{$booking->booking_ID}}" id="confirmBooking-{{$type}}" ><i class="fa fa-check" aria-hidden="true"></i> อนุมัติ</button>
              <button type="button" class="btn btn-danger" data-id="{{$booking->booking_ID}}" id="cancelBooking-{{$type}}"><i class="fa fa-times" aria-hidden="true"></i> ไม่อนุมัติ</button>
            @elseif($booking->status_ID==2)
              <i class="fa fa-ban fa-lg" aria-hidden="true" style="color: red"></i>
            @elseif($booking->status_ID==1)
              <i class="fa fa-check-circle fa-lg" aria-hidden="true" style="color: green"></i>
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



$('#confirmBooking-{{$type}}').click(function(e){
  var id = $(this).data("id")
  $.ajax({
      url: window.location.pathname + "/" + id+"/confirm",
      type: 'POST',
      dataType: 'JSON',
      data: { _token: "{{ csrf_token() }}",id:id },
      success: function(data) {
        $.notify("อนุมัติการจองหมายเลข :"+data.id,"success");
        setTimeout(function(){ window.location.reload() }, 500);
      }
  });
});

$('#cancelBooking-{{$type}}').click(function(e){
var id = $(this).data("id")
$.ajax({
    url: window.location.pathname + "/" + id+"/cancel",
    type: 'POST',
    dataType: 'JSON',
    data: { _token: "{{ csrf_token() }}",id:id },
    success: function(data) {
      $.notify("ยกเลิกการจองหมายเลข :"+data.id,"error");
      setTimeout(function(){ window.location.reload() }, 500);
    }
});
});
</script>   
