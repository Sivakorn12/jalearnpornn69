<?php
use App\Officer as officer;
?>
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
                    <th  width="60"></th>
                </tr>
            </thead>
           <tbody>
            @foreach($rooms as $key => $room )
            <tr>
                <td><img src='{{url ("asset/rooms/".officer::getAImage($room->meeting_pic))}}' width="80"></td>
                <td style="text-align:left">{{$room->meeting_name}}</td>
                <td style="text-align:left">{{$room->building_name}}</td>
                <td>{{$room->meeting_size}} ที่นั่ง</td>
                <td>
                    <a class="btn btn-warning" data-toggle="tooltip" href="{{url('control/room/edit/'.$room->meeting_ID)}}" title="แก้ไข"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a class="btn btn-danger" data-toggle="tooltip" onclick="confirmDeleteRoom({{$room->meeting_ID}},'{{$room->meeting_name}}');" title="ลบ"><i class="fa fa-times" aria-hidden="true"></i></i></a>
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
      if('{{session("successMessage")}}' != null){
        var Oncheck_message = '{{session("successMessage")}}'
        if (Oncheck_message) {
          swal(Oncheck_message, {
                icon: "success",
                buttons: false
              })
              setTimeout(function(){ window.location.reload() }, 1000);
        }
      }
      else if('{{session("errorMesaage")}}' != null){
        var Oncheck_message = '{{session("errorMessage")}}'
        if (Oncheck_message) {
          swal(Oncheck_message, {
                icon: "error",
                buttons: false
              })
              setTimeout(function(){ window.location.reload() }, 1000);
        }
      };
    });

    function confirmDeleteRoom (id,name) {
        swal({
            title: "คุณต้องการลบห้อง "+name+" หรือไม่?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
            buttons: ["ยกเลิก", "ยืนยัน"]
        })
        .then((willDelete) => {
            if (willDelete) {
                var path = "{{url('control/room/delete')}}/"+id
                window.location.href = path
            }
        })
    }
</script>   
@endsection