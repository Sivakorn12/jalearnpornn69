<?php
  use App\func as func;
  use App\Officer as officer;
?>

@extends('layouts.officer',['page'=>'reservation'])
@section('page_heading','จองห้องประชุม (แบบ 1วัน/สัปดาห์)')
@section('content')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
            <div class="row">
                <div class="col-md-7">
                  <div class="form-group form-room">
                      <label class="col-sm-3 control-label">เลือกวันที่จอง</label>
                      <div class="col-sm-7">
                          <input type="text" name="daterange" id="daterange" class="form-control" />
                        {{-- <input type="text" class="datepicker" data-provide="datepicker" data-date-language="th-th" placeholder="กดเพื่อเลือก"> --}}
                      </div>
                  </div>
                </div>
                <div class="col-md-5" style="background-color:aquamarine;display:none" >
                  <p style="font-size: 24px;">Tips!</p>
                </div>
            </div>
            <div  class="row" style="margin-top:15px">
              <div class="col-md-7" >
                  <div class="form-group form-room">
                      <label class="col-sm-3 control-label" id="list_reserve_title"></label>
                      <div class="col-sm-7" id="list_reserve">
                      </div>
                  </div>
              </div>
            </div>
            <div class="row" style="margin-top:10px">
              <div class="col-md-7">
                  <div class="form-group form-room">
                      <label class="col-sm-3 control-label"></label>
                      <div class="col-sm-7">
                        <form action="{{ url('control/reservation/adayinweek/form') }}" method="post">
                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                            <input type="hidden" name="meeting_ID" id="meeting_ID" value="{{$rooms->meeting_ID}}">
                            <input type="hidden" name="data_reserve" id="data_reserve" value="">
                            <button class="btn btn-primary" id="confirm_reserve_date" style="display:none">ถัดไป</button>
                        </form>  
                      </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <div class="col-md-1"></div>
  </div>
  @endif
  <script>
  var arrTime = []
  var tmpString
  var times = [] 
  var time_reserve = []
  var date_select = ''
  const meetingId = <?php echo $rooms->meeting_ID ?>;
  var date_now = moment().format('DD/MM/YYYY');
  $(function() {
      
      $('input[name="daterange"]').daterangepicker({
      autoApply: true,
      opens: 'left',
      locale: {
          format: 'DD/MM/YYYY'
      }
      }, function(start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        if(start.day() != end.day()){
          alert('กรุณาเลือกวันให้ตรงกัน')
          
          $('#confirm_reserve_date').hide()
          //console.log(date_now)
          $('input[name="daterange"]').data('daterangepicker').setStartDate(date_now);
          $('input[name="daterange"]').data('daterangepicker').setEndDate(date_now);
        }
        else{
          $.ajax({
              url: '{{url("")}}/control/checkdayreserve',
              type: 'post',
              dataType: 'JSON',
              data: {
                _token: "{{ csrf_token() }}",
                date_st: start.format('YYYY-MM-DD'),
                date_end: end.format('YYYY-MM-DD'),
                meeting_id:meetingId
              },
              success: function(data) {
                if(data.success == 0){
                 
                  alert(data.message)
                  $('input[name="daterange"]').data('daterangepicker').setStartDate(date_now);
                  $('input[name="daterange"]').data('daterangepicker').setEndDate(date_now);
                  $('#confirm_reserve_date').hide()
                }
                else{
                  console.log(data)
                  showDataReserve(data)
                  $('#data_reserve').val(JSON.stringify(data.date_reserve))
                  $('#confirm_reserve_date').show()
                }
              }
          });
        }
      });
  });

  function showDataReserve(data){
    var html = ''
    html += '<table class="table table-bordered">'
    for(index in data.date_reserve){
      html+= '<tr><td>'+data.date_reserve[index]+'</td><td>'+data.time_start.substring(0, 5)+' - '+data.time_end.substring(0, 5)+'</td></tr>'
    }
    html += '</table>'
    $('#list_reserve_title').html('รายการวันที่จอง')
    $('#list_reserve').html(html)
  }

  </script>
@endsection