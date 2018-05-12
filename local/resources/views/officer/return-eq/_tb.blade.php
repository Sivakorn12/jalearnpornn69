<?php
use App\Officer as officer;
if($type == 'borrow'){
  $selected_status = '1';
}
elseif($type == 'return'){
  $selected_status = '2';
}

?>
<div class="table-responsive">
<table class="table table-bordered  showroom" id="tb-{{$type}}">
    <thead>
        <tr style="background-color:#D3D3D3">
            <th width="25">#</th>
            <th>ห้อง</th>
            <th>วันที่</th>
            <th>เวลา</th>
            <th>ผู้ติดต่อ</th>
            <th>สถานะ</th>
        </tr>
    </thead>
   <tbody>
    @foreach($datas as $key => $data)
    <?php
      $chk = (date('Y-m-d')>=$data->checkin and $data->status_ID==3 and $data->detail_timestart<date('Y-m-d H:i:s'));
    ?>
      <tr>
        <td>{{($key+1)}}</td>
        <td>{{$data->meeting_name}}</td>
        <td>{{$data->borrow_date}}</td>
        <td >{{substr($data->detail_timestart, -8,5)}} - {{substr($data->detail_timeout, -8,5)}}</td>
        <td>{{$data->booking_name}}</td>
        <td>
          {!!($chk )? '<span class="label label-status label-default">เกินระยะเวลา(ยกเลิก)</span>' :officer::getStatusBooking($data->borrow_status,1)!!}
        </td>
      </tr>
     @endforeach
   </tbody>
</table>
</div>
<script>

$(document).ready(function() {
  $('#tb-{{$type}}').DataTable();
});


</script>   
