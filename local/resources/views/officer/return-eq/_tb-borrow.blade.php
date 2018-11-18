<?php
use App\Officer as officer;
$datas = array();
$color ="#D3D3D3";
if($type == 'borrowtoday'){
  $selected_status = '';
  $datas = officer::getDataBorrow('today');
  $color ='#5cb85c';
}
elseif($type == 'borrow'){
  $selected_status = '1';
  $datas = officer::getDataBorrow();
}
?>
<div class="table-responsive">
<table class="table table-bordered  showroom" id="tb-{{$type}}">
    <thead>
      <tr style='background-color:{{$color}}'>
            <th width="25">#</th>
            <th>ห้อง</th>
            <th>วันที่</th>
            <th>เวลา</th>
            <th>ผู้ติดต่อ</th>
            <th>สถานะ</th>
          {{-- @if($type=="borrowtoday") --}}
            <th></th>
          {{-- @endif --}}
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
        {{-- @if($type=="borrowtoday") --}}
            <td><a title='รายละเอียดการยืม' data-toggle="modal" onclick="viewBorrow({{$data->borrow_ID}})"  data-toggle="tooltip" class="glyphicon glyphicon-search" aria-hidden="true"></a></td>
        {{-- @endif --}}
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
