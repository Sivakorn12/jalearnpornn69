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
?>
    <div class="col-md-1"></div>
    <div class="col-md-10">
      <div class="panel panel-default">
        <div class="panel-heading"><h4>{{$rooms->meeting_name}}</h4></div>
        <div class="panel-body">
          <div class="col-md-4">
            <img class="img-responsive" src='{{url ("asset/rooms/".$rooms->meeting_pic)}}'>
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
      <div class="panel panel-default">
        <div class="panel-heading"><h4>เวลาการจอง</h4></div>
        <div class="panel-body">
          <div class="col-md-12">
            <input type="text" class="datepicker" data-provide="datepicker" data-date-language="th-th">
          </div>
          <div class="col-md-12" id="time-reserve" style="padding-top: 1rem; display: none;">
          </div>
        </div>
      </div>
    </div>
  <div class="col-md-1"></div>
</div>
@endif
<script>
$(document).ready(function() {
  $('.datepicker').datepicker({
    format: 'dd-mm-yyyy',
    thaiyear: true,
    language: 'th',
  }).on("change", function() {
    var dataOnchange = $(this).val();
    $('#time-reserve').hide()
      $.ajax({
          url: "{{url('checkdate')}}",
          type: 'GET',
          dataType: 'JSON',
          data: {  _token: "{{ csrf_token() }}", date: dataOnchange, roomid: "{{$rooms->meeting_ID}}" },
          success: function(data) {
            if (data.time_use) {
              render_button_time(data.time_use, dataOnchange)
              $('#time-reserve').show()
            } else {
              render_button_time(data.constant_time)
              $('#time-reserve').show()
              swal('ไม่สำเร็จ', data.error, 'error')
            }
          }
      });
  });
});

  function render_button_time (time, date_select) {
    var viewHTML = ""
    let button_times = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00']
    for (index = 0; index < time.length; index++) {
      let path = "{{url('reserve')}}"+"/{{$rooms->meeting_ID}}/"+button_times[index].substring(-8, 2)+"/"+date_select
      if (time[index] == 1) {
        viewHTML += "<a type='button' class='btn btn-danger' style='margin-right: 1rem;' disabled='disabled'>"+button_times[index]+"</a>"
      } else {
        viewHTML += "<a type='button' class='btn btn-success' style='margin-right: 1rem;' href="+path+">"+button_times[index]+"</a>"
      }
    }
    $('#time-reserve').html(viewHTML)
  }
</script>
@endsection