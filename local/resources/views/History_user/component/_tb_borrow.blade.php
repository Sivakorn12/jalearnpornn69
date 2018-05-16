<?php
use App\Officer as officer;
?>
<table class="table table-hover showroom" id="tb-borrow">
    <thead>
        <tr>
            <th>ห้อง</th>
            <th>วันที่เข้าใช้ห้อง</th>
            <th>วันที่ยืมอุปกรณ์</th>
            <th>อุปกรณ์ที่ยืม</th>
            <th>จำนวน</th>
            <th>สถานะ</th>
            <th></th>
        </tr>
    </thead>
   <tbody>
    @foreach($borrows as $key => $borrow)
    <tr>
      <td>{{$borrow->meeting_name}}</td>
      <td>{{$checkin_date[1][$key]}}</td>
      <td>{{$checkin_borrow[$key]}}</td>
      <td>{{$borrow->em_name}}</td>
      <td>{{$borrow->borrow_count}}</td>
      <td>
      @if($check_date[1][$key] == 1) <span class="label label-warning">รออนุมัติ</span>
      @elseif($check_date[1][$key] == 2) <span class="label label-success">อนุมัติ</span>
      @elseif($check_date[1][$key] == 3) <span class="label label-info">เกินวันยืมอุปกรณ์</span>
      @else <span class="label label-danger">ไม่อนุมัติ</span>
      @endif
      </td>
      <td>
      @if($check_date[1][$key] == 1)<button onclick="checkDecided_Delete({{$borrow->booking_ID}}, {{$borrow->borrow_ID}})" class="btn btn-danger btn-xs">ยกเลิกการยืมอุปกรณ์</button>
      @endif
      </td>
    </tr>
    @endforeach
   </tbody>
</table>
<script>
$(document).ready(function() {
  $('#tb-borrow').DataTable();
});

function checkDecided_Delete (booking_id, borrow_id) {
  swal({
    title: "คุณต้องการลบการยืมใช่ไหม ?",
    icon: "warning",
    buttons: true,
    dangerMode: true,
    buttons: ["ยกเลิก", "ยืนยัน"]
  })
  .then((willDelete) => {
    if (willDelete) {
      $.ajax({
          url: "{{url('deleteborrow')}}",
          type: 'GET',
          dataType: 'JSON',
          data: { _token: "{{ csrf_token() }}", data_booking: booking_id, data_borrow: borrow_id},
          success: function(data){
            swal(data.message, {
              icon: "success",
              buttons: false
            })
            setTimeout(function(){ window.location.reload() }, 1000);
          }
      })
    }
  })
}
</script>