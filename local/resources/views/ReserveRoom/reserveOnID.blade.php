<?php
  use App\func as func;
?>

@extends('layouts.app')
@section('page_heading','จองห้องประชุม')
@section('content')
<div class="row">
  @if (!isset($rooms)) <h1>ห้องไม่พร้อมใช้งาน</h1>
  @else
<?php
  $equipments = func::Getequips($rooms->meeting_ID);
  $reserveTime = func::queryReserveTime($rooms->meeting_ID);
  $timeStart = $reserveTime->detail_timestart;
  $timeEnd = $reserveTime->detail_timeout;
  $times = ['9:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
?>
    <div class="col-md-1"></div>
    <div class="col-md-10">
      <div class="panel panel-default">
        <div class="panel-heading"><h4>{{$rooms->meeting_name}}</h4></div>
        <div class="panel-body">
          <div class="col-md-4">
            <img class="img-responsive" src='{{url ("asset/".$rooms->meeting_pic)}}'>
          </div>
          <div class="col-md-8">
            <div class="table-responsive">
              <table width="100%">
                <tr>
                  <td valign="top" width="130px"><b>ข้อกำหนดการ : </b></td>
                  <td>{{$rooms->provision}}</td>
                </tr>
                <tr>
                  <td valign="top"><b>ขนาดห้อง : </b></td>
                  <td>{{$rooms->meeting_size}}</td>
                </tr>
                <tr>
                  <td valign="top"><b>อาคารห้องประชุม : </b></td>
                  <td>{{$rooms->meeting_buiding}}</td>
                </tr>
                <tr>
                  <td valign="top"><b>อุปกรณ์ห้องประชุม : </b></td>
                  <td>
                    <ul style="list-style-type: none; padding: 0;">
                      @if(isset($equipments))
                        @foreach($equipments as $equipment)
                          <li>{{$equipment->em_in_name}}</li>
                        @endforeach
                      @endif
                    </ul>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-1"></div>
</div>
<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">
    @foreach($times as $time)
    <button type="button" class="btn btn-success">{{$time}}</button>
    @endforeach
  </div>
  <div class="col-md-1"></div>
</div>
@endif
@endsection