<?php
use App\Officer as officer;
$datas = array();
$color ="#D3D3D3";

if($type == 'notreturn'){
  $selected_status = '1';
  $datas = officer::getDataBorrow_success();
  $color ='#5cb85c';
}
elseif($type == 'return'){
  $selected_status = '1';
  $datas = officer::getDataReturn_success();
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
          @if($type=="notreturn")
            <th></th>
          @endif
        </tr>
    </thead>
   <tbody>
    @foreach($datas as $key => $data)
      <?php
        $chk = (date('Y-m-d')>=$data->checkin and $data->status_ID==3 and $data->detail_timestart<date('Y-m-d H:i:s')); 
      ?>
      @if(!officer::isReturnEquipment($data->booking_ID) OR $type != 'notreturn')
          <tr>
            <td>{{($key+1)}}</td>
            <td>{{$data->meeting_name}}</td>
            <td>{{$data->borrow_date}}</td>
            <td >{{substr($data->detail_timestart, -8,5)}} - {{substr($data->detail_timeout, -8,5)}}</td>
            <td>{{$data->booking_name}}</td>
            <td>
              {!!(officer::isReturnEquipment($data->booking_ID))? 
                '<span class="label label-status label-success"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> คืนเเล้ว</span>' 
                :'<span class="label label-status label-warning"><i class="glyphicon glyphicon-time" aria-hidden="true"></i> ยังไม่ได้คืน</span>'!!}
            </td>
            @if($type=="notreturn")
                <td><a title='รายละเอียดการยืม' data-toggle="modal" onclick="viewReturn({{$data->borrow_ID}})"  data-toggle="tooltip" class="glyphicon glyphicon-search" aria-hidden="true"></a></td>
            @endif
          </tr>
        @endif
     @endforeach
   </tbody>
</table>
</div>
<script>

$(document).ready(function() {
  $('#tb-{{$type}}').DataTable();
});


</script>   
