<?php
use App\Officer as officer;
?>
<table class="table table-hover showroom" id="tb-reserve">
    <thead>
        <tr>
            <th>ห้อง</th>
            <th>วันที่จองห้อง</th>
            <th>วันที่เข้าใช้ห้อง</th>
            <th>เวลาที่เข้าใช้ห้อง</th>
            <th>สถานะ</th>
            <th></th>
            <th>ประเมินห้องประชุม</th>
        </tr>
    </thead>
   <tbody>
    @foreach($reserves as $key => $reserve)
      <tr>
      <td>{{$reserve->meeting_name}}</td>
      <td>{{$years_th[$key]}}</td>
      <td>{{$reserve->checkin}}</td>
      <td>{{$time_start[$key]}} - {{$time_out[$key]}}</td>
      <td>
      @if($check_date[$key] == 1) <span class="label label-warning">รออนุมัติ</span>
      @elseif($check_date[$key] == 2) <span class="label label-success">อนุมัติ</span>
      @elseif($check_date[$key] == 3) <span class="label label-info">เกินวันเข้าใช้งาน</span>
      @else <span class="label label-danger">ไม่อนุมัติ</span>
      @endif
      </td>
      <td>
      @if($check_date[$key] == 1)<a href="{{url('history/'.$reserve->booking_ID)}}" class="btn btn-danger btn-xs">ยกเลิกการจอง</a>
      @endif
      </td>
      <td>
      @if ($check_date[$key] == 2) <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#exampleModal" data-room="{{$reserves[$key]->estimate_link}}">ทำแบบประเมิน</button>
      @endif
      </td>
      </tr>
     @endforeach
   </tbody>
</table>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">สแกน QR Code เพื่อทำแบบประเมิน</h4>
      </div>
      <div class="modal-body text-center">
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
  $('#tb-reserve').DataTable();
});

$('#exampleModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var recipient = button.data('room')
  var path = `{{url('history/getQr')}}`
  var modal = $(this)

  $.ajax({
      url: `{{url('getQr')}}`,
      type: 'GET',
      dataType: 'JSON',
      data: {id: recipient},
      success: function(data){
        modal.find('.modal-body').html(data.html)
      }
  })
})
</script>
