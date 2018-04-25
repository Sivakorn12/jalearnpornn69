@extends('layouts.officer',['page'=>'room'])
@section('page_heading','จัดการห้องประชุม')
@section('content')
<div class="row">
    <div class="col-xs-12" style="padding-bottom:10px">
        <div style="float:right"> 
            <a class="btn btn-success" data-toggle="tooltip" href="{{url('control/room/form')}}" title="เพิ่ม"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="col-xs-12" id="tableroom"> 
        <table class="table table-hover showroom" id="tb-room">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ห้อง</th>
                    <th>อาคาร</th>
                    <th>ขนาด</th>
                    <th></th>
                </tr>
            </thead>
           <tbody>
            @foreach($rooms as $key => $room )
            <tr>
                <td><img src='{{url ("asset/rooms/".$room->meeting_pic)}}' width="80"></td>
                <td>{{$room->meeting_name}}</td>
                <td>{{$room->meeting_buiding}}</td>
                <td>{{$room->meeting_size}}</td>
                <td>
                    <a class="btn btn-warning" data-toggle="tooltip" href="{{url('control/room/edit/'.$room->meeting_ID)}}" title="แก้ไข"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a class="btn btn-danger" data-toggle="tooltip" href="{{url('control/room/delete/'.$room->meeting_ID)}}" 
                    onclick="return confirm('คุณต้องการลบห้อง {{$room->meeting_name}} หรือไม่?');" title="ลบ"><i class="fa fa-times" aria-hidden="true"></i></i></a>
                </td>
            </tr>
            @endforeach
           </tbody>
        </table>
    </div>
</div>
<script>       
    $(document).ready(function() {
      $('#tb-room').DataTable();
      $('[data-toggle="tooltip"]').tooltip(); 
      var msg = ''
      if('{{session("successMessage")}}' != null) $.notify('{{session("successMessage")}}',"success");
      else if('{{session("errorMesaage")}}' != null) $.notify('{{session("errorMesaage")}}',"error");
    });
</script>   
@endsection