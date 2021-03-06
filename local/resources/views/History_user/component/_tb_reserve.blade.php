<?php
use App\Officer as officer;
?>
<div class="row">
  <div class="col-md-12">
      <table class="table table-hover showroom" id="tb-reserve">
        <thead>
            <tr>
              <th>ห้อง</th>
              <th id="sort-th-header">วันที่จองห้อง</th>
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
            <td class="td-head-reserve">{{$years_th[0][$key]}}</td>
            <td>{{$checkin_date[0][$key]}}</td>
            <td>{{$time_start[$key]}} - {{$time_out[$key]}}</td>
            <td>
            @if($check_date[0][$key] == 1) <span class="label label-success">อนุมัติ</span>
            @elseif($check_date[0][$key] == 2) <span class="label label-danger">ไม่อนุมัติ</span>
            @elseif($check_date[0][$key] == 3) <span class="label label-warning">รออนุมัติ</span>
            @elseif($check_date[0][$key] == 4) <span class="label label-danger">ยกเลิกการจอง</span>
            @elseif($check_date[0][$key] == 5) <span class="label label-info">เกินวันเข้าใช้งาน</span>
            @endif
            </td>
            <td>
            @if($check_date[0][$key] == 3) <a type="button" href="{{ url('history/editdata/'.$reserve->booking_ID.'/'.$time_start[$key]) }}" class="btn btn-warning btn-xs">แก้ไขการจอง</a> <button type="button" onclick="cancelBooking({{$reserve->booking_ID}})" class="btn btn-danger btn-xs">ยกเลิกการจอง</button>
            @endif
            </td>
            <td>
            @if ($checking_est[$key] == 1 && ($check_date[0][$key] == 1 || $check_date[0][$key] == 4)) <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#exampleModal" data-room="{{$reserves[$key]->estimate_link}}">QR Code</button> <a type="button" class="btn btn-info btn-xs" href="{{$reserves[$key]->estimate_link}}" target="_blank">ไปลิ้งค์ประเมิน</a>
            @elseif ($check_date[0][$key] == 3) <button type="button" disabled="disabled" class="btn btn-info btn-xs" data-toggle="modal" data-target="#exampleModal" data-room="{{$reserves[$key]->estimate_link}}">QR Code</button> <a type="button" disabled="disabled" class="btn btn-info btn-xs" href="{{$reserves[$key]->estimate_link}}" target="_blank">ไปลิ้งค์ประเมิน</a>
            @endif
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
  </div>
</div>
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
  var path = `{{url('getQr')}}`
  var recipient = button.data('room')
  var modal = $(this)

  $.ajax({
      url: path,
      type: 'GET',
      dataType: 'JSON',
      data: {id: recipient},
      success: function(data){
        modal.find('.modal-body').html(data.html)
      }
  })
})

function cancelBooking(id) {
  swal({
    title: "คุณต้องการไม่อนุมัติการจองใช่หรือไม่ ?",
    text: 'กรุณาแจ้งหมายเหตุ',
    icon: "warning",
    content: "input",
    buttons: true,
    dangerMode: true,
    buttons: ["ยกเลิก", "ตกลง"]
  })
  .then(name => {
    if (!name) throw null;

    $.ajax({
          url: "{{url('history/submitComment')}}",
          type: 'POST',
          dataType: 'JSON',
          data: { _token: "{{ csrf_token() }}", data_booking: id, comment: name},
          success: function(data){
            swal(data.message, {
              icon: "success",
              buttons: false
            })
            setTimeout(function(){ window.location.reload() }, 1000);
          }
      })
  })
}
</script>
