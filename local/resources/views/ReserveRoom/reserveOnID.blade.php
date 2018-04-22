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
  $times = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];

  if (isset($reserveTime)) {
    // $timeStart = substr(explode(" ", $reserveTime->detail_timestart)[1], 0, strpos(explode(" ", $reserveTime->detail_timestart)[1], ":"));
    // $timeEnd = explode(" ", $reserveTime->detail_timeout)[1];
    $timeStart = substr(explode(" ", $reserveTime->detail_timestart)[1], 0, -3);
    $timeEnd = substr(explode(" ", $reserveTime->detail_timeout)[1], 0, -3);
    $postimeStart = array_search($timeStart, $times);
    $postimeEnd = array_search($timeEnd, $times);
  }
  // dd($timeStart, $timeEnd);
  // dd($postimeStart, $postimeEnd);
  // dd(array_search($timeEnd, $times));
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
    @for($index = 0; $index < sizeof($times); $index++)
      @if($index == $postimeStart || $index > $postimeStart && $index < $postimeEnd) <button type="button" class="btn btn-danger" disabled="disabled">{{$times[$index]}}</button>
      @else <button type="button" class="btn btn-success">{{$times[$index]}}</button>
      @endif
    @endfor
  </div>
  <div class="col-md-1"></div>
</div>
@endif
@endsection