<?php
use App\Officer as officer;
?>
@extends('layouts.officer',['page'=>'reservation'])
@section('page_heading','จองห้องประชุม')
@section('content')

<div class="row">
    <div class="col-xs-12" id="tableroom"> 
        <table class="table table-hover showroom" id="table_room">
            <thead>
                <tr >
                    <th>
                        รูปภาพ
                    </th>
                    <th>
                        ชื่อห้อง
                    </th>
                    <th>
                        ขนาดห้อง
                    </th>
                    <th>
                        ประเภทห้อง
                    </th>
                    <th id="tb-build">
                        อาคาร
                    </th>
                    <th>
                        สถานะห้องประชุม
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $key => $room)
                    <tr onclick="@if($room->meeting_status == 1)location.href='{{ url('control/reservation/'.$room->meeting_ID)}}' 
                                    @else swal('ไม่สำเร็จ', 'ห้องประชุมไม่พร้อมใช้งาน', 'error')
                                    @endif">
                        <td>
                            <img src='{{url ("asset/rooms/".officer::getAImage($room->meeting_pic))}}' width="100">
                        </td>
                        <td>
                            {{$room->meeting_name}}
                        </td>
                        <td>
                            {{$room->meeting_size}} ที่นั่ง
                        </td>
                        <td>
                            {{$room->meeting_type_name}}
                        </td>
                        <td>
                            {{$room->building_name}}
                        </td>
                        <td>
                            @if ($room->meeting_status == 1) <i class="fa fa-check-circle fa-lg" style="color: green" aria-hidden="true"></i>
                            @else <i class="fa fa-ban fa-lg" style="color: #e60000" aria-hidden="true"></i>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
          </table>
    </div>
</div>
<script>       
    $(document).ready(function() {
      $('#table_room').DataTable();
      $('#tb-build').click();
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

    if('{{session("errorMessage")}}' != null){
        var Oncheck_error = '{{session('errorMessage')}}'
        if (Oncheck_error) {
          swal(Oncheck_error, {
                icon: "error",
                button: true
              })
        }
      };
    });
</script>   
@endsection