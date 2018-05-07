<table class="table table-hover showroom" id="tb-reserve">
    <thead>
        <tr>
            <th>ห้อง</th>
            <th>วันที่จองห้อง</th>
            <th>วันที่เข้าใช้ห้อง</th>
            <th>เวลาที่เข้าใช้ห้อง</th>
            <th>สถานะ</th>
            <th></th>
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
      </tr>
     @endforeach
   </tbody>
</table>
<script>

$(document).ready(function() {
  $('#tb-reserve').DataTable();
});


</script>   
