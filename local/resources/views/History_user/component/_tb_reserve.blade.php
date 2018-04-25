<table class="table table-hover showroom" id="tb-reserve">
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
    @foreach($reserves as $key => $reserve)
      <tr>
      <td>{{$reserve->status_name}}</td>
      </tr>
     @endforeach
   </tbody>
</table>
<script>

$(document).ready(function() {
  $('#tb-reserve').DataTable();
});


</script>   
