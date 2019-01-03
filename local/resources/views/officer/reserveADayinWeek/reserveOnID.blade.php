<?php
  use App\func as func;
  use App\Officer as officer;
?>

@extends('layouts.officer',['page'=>'reservation'])
@section('page_heading','จองห้องประชุม (แบบทุก 1วัน/สัปดาห์)')
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
          <ul class="nav nav-tabs tabs_reserve_type">
            <li role="presentation" ><a href="{{url('control/reservation')}}/{{$rooms->meeting_ID}}">จองวันเดียว</a></li>
            <li role="presentation" class="active"><a href="{{url('control/reservation/adayinweek')}}/{{$rooms->meeting_ID}}">จองทุก 1วัน/สัปดาห์</a></li>
          </ul>
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
            <div  class="row" style="margin-top:15px" id="div_date_list">
              <div class="col-md-7" >
                  <div class="form-group form-room">
                      <label class="col-sm-3 control-label" id="list_reserve_title"></label>
                      <div class="col-sm-7" id="list_reserve">
                      </div>
                  </div>
              </div>
            </div>
            <div  class="row" style="margin-top:15px" id="div_btn_time" style="display:none">
              <div class="col-md-10" >
                  <div class="form-group form-room">
                      <label class="col-sm-2 control-label"></label>
                      <div class="col-sm-10" id="btn_time">
                        {{-- <a type='button' id='btnTime09' class='btn btn-danger' style='margin-right: 1rem; margin-top: 1rem;' disabled='disabled'>09</a>
                        <a type='button' id='btnTime09' class='btn btn-danger' style='margin-right: 1rem; margin-top: 1rem;' disabled='disabled'>09</a> --}}
                      </div>
                  </div>
              </div>
            </div>
            <div class="row" style="margin-top:20px">
              <div class="col-md-7">
                  <div class="form-group form-room">
                      <label class="col-sm-3 control-label"></label>
                      <div class="col-sm-7">
                        <form action="{{ url('control/reserve_adayinweek/form') }}" method="get" id="select_date_form">
                            <input type="hidden" name="meeting_ID" id="meeting_ID" value="{{$rooms->meeting_ID}}">
                            <input type="hidden" name="data_reserve" id="data_reserve" value="">
                            <input type="hidden" name="time_reserve" id="time_reserve" value="">
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
  var time_btn= []
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
        arrTime = []
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        if(start.day() != end.day()){
          alert('กรุณาเลือกวันให้ตรงกัน')
          
          $('#confirm_reserve_date').hide()
          $('#div_date_list').hide()
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
                //console.log(data)
                if(data.success == 0){
                 
                  alert(data.message)
                  $('input[name="daterange"]').data('daterangepicker').setStartDate(date_now);
                  $('input[name="daterange"]').data('daterangepicker').setEndDate(date_now);
                  $('#div_date_list').hide()
                  $('#div_btn_time').hide()
                  $('#confirm_reserve_date').hide()
                }
                else{
                  showDataReserve(data)
                  $('#data_reserve').val(JSON.stringify(data.date_reserve))
                  //$('#time_reserve').val(JSON.stringify(data.time_reserve))
                  $('#div_btn_time').show()
                  time_btn = data.time_btn
                  render_btn_time()
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
      html+= '<tr><td>'+data.date_reserve[index]+'</td><td>เปิด '+data.time_reserve[index][0].substring(0,5)+' - '+data.time_reserve[index][1].substring(0,5)+'</td></tr>'
    }
    html += '</table>'
    $('#div_date_list').show()
    $('#list_reserve_title').html('รายการวันที่จอง')
    $('#list_reserve').html(html)
  }

  function render_btn_time(){
    var viewHTML = ''
    for(index in time_btn){
      if (time_btn[index]["can_book"] == 0 ) {
        viewHTML += "<a type='button' id='btnTime"+parseInt(time_btn[index]["index"])+"' class='btn btn-danger' style='margin-right: 1rem; margin-top: 1rem;' disabled='disabled'>"+time_btn[index]["time"]+"</a>"
      } else {
        viewHTML += "<a type='button' id='btnTime"+parseInt(time_btn[index]["index"])+"' class='btn btn-success' style='margin-right: 1rem; margin-top: 1rem;' onclick='addTime(`"+time_btn[index]["index"]+"`)'>"+time_btn[index]["time"]+"</a>"
      }
    }
    $('#btn_time').html(viewHTML)
    setColorBtn()
  }

  function addTime (value) {
    
    if(arrTime.length == 2){
      arrTime = []
      arrTime[0] = value
    }
    else{
      var hrs_st = parseInt(value.substring(0,2))
      const checkIndex = arrTime.findIndex( element => {
        return element === value
      })
      const checkErrorBackTime = arrTime.findIndex( element => {
        return parseInt(element.substring(0,2)) >= hrs_st
      })
      if (checkIndex === -1) {
        arrTime.push(value)
      }
      if (checkErrorBackTime === 0) {
        arrTime = []
        arrTime[0] = value
      }
    }
    render_btn_time()
    $('#time_reserve').val(JSON.stringify(arrTime))
  }

  function setColorBtn(){
    var st,end = 0
    if(arrTime.length == 2){
      st = parseInt(arrTime[0].substring(0,2))
      end = parseInt(arrTime[1].substring(0,2))
    }
    else if(arrTime.length == 1){
      st = parseInt(arrTime[0].substring(0,2))
      end = parseInt(arrTime[0].substring(0,2))
    }
    for(var n = st;n<=end;n++){
      $('#btnTime'+n).removeClass('btn-success')
      $('#btnTime'+n).addClass('btn-warning')
    }
  }

  $('#confirm_reserve_date').click(function(e){
    
    if(arrTime.length == 0){
      e.preventDefault();
      alert('กรุณาเลือกเวลาที่จะจองก่อน')
      //$('#select_date_form').submit(false); 
    }
    else{
            $('#select_date_form').submit();
    }
  })

  </script>
@endsection