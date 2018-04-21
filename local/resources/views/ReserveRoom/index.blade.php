@extends('layouts.app')
@section('page_heading','จองห้องประชุม')
@section('content')
<div class="row">
  <div class="col-md-1"></div>
          <div class="col-md-10" id="tableroom">
              <table class="table table-hover showroom">
              <tbody>
                  <tr>
                      <td>
                          รูปภาพ
                      </td>
                      <td>
                          ชื่อห้อง
                      </td>
                      <td>
                          ขนาดห้อง
                      </td>
                      <td>
                          ประเภทห้อง
                      </td>
                      <td>
                          อาคาร
                      </td>
                      <td>
                          สถานะห้องประชุม
                      </td>
                  </tr>
              </tbody>
              @foreach($rooms as $key => $room)
                  <tr onclick="@if($room->meeting_status == 1)location.href='{{ url('reserve/'.$room->meeting_ID)}}' 
                                @else swal('ไม่สำเร็จ', 'ห้องประชุมไม่พร้อมใช้งาน', 'error')
                                @endif">
                      <td>
                          <img src='{{url ("asset/".$room->meeting_pic)}}' width="100">
                      </td>
                      <td>
                          {{$room->meeting_name}}
                      </td>
                      <td>
                          {{$room->meeting_size}}
                      </td>
                      <td>
                          {{$room->meeting_type_name}}
                      </td>
                      <td>
                          {{$room->meeting_buiding}}
                      </td>
                      <td>
                          @if ($room->meeting_status == 1) <i class="fa fa-check-circle fa-lg" style="color: green" aria-hidden="true"></i>
                          @else <i class="fa fa-ban fa-lg" style="color: #e60000" aria-hidden="true"></i>
                          @endif
                      </td>
                  </tr>
              @endforeach
              </table>
          </div>
      <div class="col-md-1"></div>
  </div>
@endsection
<style>
  table.showroom > tbody > tr > td {
      vertical-align: middle;
      text-align: center;
  };
</style>