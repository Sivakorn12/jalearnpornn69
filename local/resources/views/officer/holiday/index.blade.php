<?php
use App\Officer as officer;
?>
@extends('layouts.officer',['page'=>'holiday'])
@section('page_heading','จัดการวันหยุด')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script>  
<script type="text/javascript" src="{{ url('js/fullcalendar.th.js')}}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css" />
<div class="row">
        <div class="col-xs-12" style="padding-bottom:10px">
                <div class="panel panel-primary" style="width:100%!important">  
                    <div class="panel-heading"> ปฏิทินวันหยุด </div>  
                    <div class="panel-body"> 
                            <div id='calendar'></div>
                    </div>  
                </div>  
        </div>  
</div>
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="myModalLabel">เพิ่มวันหยุด</h4>
            </div>
            <div class="modal-body">
                <form action="{{url('control/holiday/add')}}" class="form-horizontal" method="GET" >
                    <div class="form-group form-room">
                        <label class="col-sm-3 control-label">หัวข้อวันหยุด</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="holiday_name" name="holiday_name" value="" >
                        </div>
                    </div>
                    <div class="form-group form-room">
                        <label class="col-sm-3 control-label">รายละเอียด</label>
                        <div class="col-sm-7">
                                <textarea name="holiday_detail" class="form-control" id="holiday_detail" cols="30" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="form-group form-room">
                            <label class="col-sm-3 control-label">วันที่เริ่ม</label>
                            <div class="col-sm-7">
                                    <input type="text" name="date_start" id="date_start" class="datepicker" data-provide="datepicker" data-date-language="th-th">
                            </div>
                    </div>
                    <div class="form-group form-room">
                            <label class="col-sm-3 control-label">วันที่สิ้นสุด</label>
                            <div class="col-sm-7">
                                    <input type="text" name="date_end" id="date_end" class="datepicker" data-provide="datepicker" data-date-language="th-th">
                            </div>
                    </div>
                    <div class="form-group form-room">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-7">
                                <input type="submit"  class="btn btn-primary" value="บันทึก"></button>
                            </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
</div>

<div id="detailModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">ดูรายละเอียดวันหยุด</h4>
        </div>
        <div class="modal-body">
          <p id="msgConfirm"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
  
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
<script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            thaiyear: true,
            language: 'th',
        })
        // page is now ready, initialize the calendar...
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today myCustomButton',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            events : [
                @foreach($holidays as $holiday)
                {
                    id: '{{ $holiday->holiday_ID }}',
                    title : '{{ $holiday->holiday_name }}',
                    start: new Date('{{ $holiday->holiday_start }}'),
                    end: new Date('{{$holiday->holiday_end}}T23:59:59.0+0100'),
                    description :'{{ $holiday->holiday_detail }}',
                    allDay: true,    
                    className: 'moreBorder'
                    },
                    @endforeach
            ],
            dayClick: function(date, jsEvent, view) {FormaddHoliday(date.format())},
            eventClick: function(calEvent, jsEvent, view) {detailHoliday(calEvent);},
            eventRender: function(event, element, view) {
                    $(element).height(100);
            }
    });
});
    
    function addHoliday(){
        $.ajax({
          url: "{{url('control/checkdate')}}",
          type: 'GET',
          dataType: 'JSON',
          data: {  _token: "{{ csrf_token() }}"},
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
        console.log($('#holiday_name').val())
    }
</script>

@endsection