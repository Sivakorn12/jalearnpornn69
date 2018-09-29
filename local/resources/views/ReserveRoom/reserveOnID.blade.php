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

          <div id="myCarousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
              <div class="item active">
                <img class="img-responsive" src='{{url ("asset/rooms/".$imgs[0])}}'>
              </div>

              @foreach($imgs as $key => $img)
              <div class="item">
                <img class="img-responsive" src='{{url ("asset/rooms/".$imgs[$key])}}'>
              </div>
              @endforeach
            </div>

            <a class="left carousel-control" href="#myCarousel" data-slide="prev">
              <span class="glyphicon glyphicon-chevron-left"></span>
              <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#myCarousel" data-slide="next">
              <span class="glyphicon glyphicon-chevron-right"></span>
              <span class="sr-only">Next</span>
            </a>
          </div>

          </div>
          <div class="col-md-8">
            <div class="table-responsive">
              <table width="100%">
                <tr>
                  <td valign="top" width="130px"><b>ลักษณะการใช้งาน : </b></td>
                  <td>{{$rooms->provision}}</td>
                </tr>
                <tr>
                  <td valign="top"><b>ขนาดห้อง : </b></td>
                  <td>{{$rooms->meeting_size}} ที่นั่ง</td>
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
            <div class="form-group form-room">
                <label class="col-sm-3 control-label">เลือกวันที่จอง</label>
                <div class="col-sm-7">
                  <input type="text" class="datepicker" data-provide="datepicker" data-date-language="th-th" placeholder="กดเพื่อเลือก">
                </div>
            </div>
          </div>
          <div class="form-group form-room">
            <div class="col-md-12" id="time-reserve" style="padding-top: 1rem; display: none;"></div>
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
            if (data.time_empty) {
              render_button_time(data.time_empty, data.time_reserve, dataOnchange)
              $('#time-reserve').show()
            } else {
              swal('ไม่สำเร็จ', data.error, 'error')
            }
          }
      });
  });
});

  function render_button_time (time, time_reserve, date_select) {
    var viewHTML = ""
    for (index = 0; index < time.length; index++) {
      let path = "{{url('reserve')}}"+"/{{$rooms->meeting_ID}}/"+time_reserve[index].substring(-8, 2)+"/"+date_select
      if (time[index] == 1) {
        viewHTML += "<a type='button' class='btn btn-danger' style='margin-right: 1rem;' disabled='disabled'>"+time_reserve[index]+"</a>"
      } else {
        viewHTML += "<a type='button' class='btn btn-success' style='margin-right: 1rem;' href="+path+">"+time_reserve[index]+"</a>"
      }
    }
    $('#time-reserve').html(viewHTML)
  }
</script>
@endsection
