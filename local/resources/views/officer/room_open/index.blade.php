<?php
use App\Officer as officer;
?>
@extends('layouts.officer',['page'=>'extratime'])
@section('page_heading','เวลาการใช้งานพิเศษเฉพาะห้อง')
@section('content')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div class="row">
    <div class="col-md-12" id="tableroom"> 
        <div style="float:right;margin-bottom:10px">
            <a data-toggle="modal" onclick="addEx()" class="btn btn-success" data-toggle="tooltip" title="เพิ่ม"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </div>
        <table class="table table-hover showroom" id="tb-room">
            <thead>
                <tr>
                    <th width="20">#</th>
                    <th>วันที่เริ่มเปิดใช้</th>
                    <th>วันที่สิ้นสุดการเปิดใช้</th>
                    <th>เวลา</th>
                    <th>ห้อง</th>
                    <th width="60"></th>
                </tr>
            </thead>
           <tbody>
            @foreach($mot as $key => $ex )
            <tr>
                <td>{{$key+1}}</td>
                
                <td>{{officer::dateDBtoBE(substr($ex->start_date,0,10))}}</td>
                <td>{{officer::dateDBtoBE(substr($ex->end_date,0,10))}}</td>
                <td>{{substr($ex->start_date,-8,5)}} - {{substr($ex->end_date,-8,5)}}</td>
                <td>{{$ex->meeting_name}}</td>
                <td>
                <a class="btn btn-warning btn-sm" data-toggle="tooltip" data-toggle="modal" onclick="changeEx('{{$ex->id}}','{{$ex->meeting_ID}}','{{$ex->start_date}}','{{$ex->end_date}}')" title="แก้ไข"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a class="btn btn-danger btn-sm" data-toggle="tooltip" onclick="confirmDeleteEx({{$ex->id}},'{{officer::dateDBtoBE(substr($ex->start_date,0,10))}}');" title="ลบ"><i class="fa fa-times" aria-hidden="true"></i></i></a>
                </td>
            </tr>
            @endforeach
           </tbody>
        </table>
    </div>
</div>
<!-- extra time Modal-->
<div class="modal fade" id="extratime-form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">เวลาการใช้งานพิเศษเฉพาะห้อง</h4>
        </div>
        <div class="modal-body">
            <form action="{{url('control/room_open/save')}}" class="form-horizontal" method="POST" >
                <div class="form-group form-room">
                    <label class="col-sm-3 control-label">ห้อง</label>
                    <div class="col-sm-7">
                        <select name="room_id" id="room_id" class="form-control">
                            @foreach($room as $r)
                                <option value="{{$r->meeting_ID}}">{{$r->meeting_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group form-room">
                        <label class="col-sm-3 control-label">ช่วงเวลา</label>
                        <div class="col-sm-7">
                                <input type="text" name="daterange" class="form-control" />
                        </div>
                </div>
                <div class="form-group form-room">
                        <label class="col-sm-3 control-label">เวลา</label>
                        <div class="col-sm-3">
                            <select name="ex_start" onchange="setExEnd()" class="form-control"id="ex_start">
                                @for($n = 8 ; $n<=21 ;$n++)
                                    <option value="{{$n}}">{{$n}}.00 น.</option>
                                @endfor
                            </select>
                        </div>
                        <label class="col-sm-1 control-label">ถึง</label>
                        <div class="col-sm-3">
                            <select name="ex_end" class="form-control"id="ex_end">
                                @for($n = 9 ; $n<=22 ;$n++)
                                    <option value="{{$n}}">{{$n}}.00 น.</option>
                                @endfor
                            </select>
                        </div>
                </div>
                <div class="form-group form-room">
                    <label class="col-sm-3 control-label"></label>
                    <div class="col-sm-7">
                        <input type="submit"  class="btn btn-primary" value="บันทึก"></button>
                    </div>
                </div>
                <div id="idHide"></div>
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>
<script> 
$(document).ready(function() {
  $('.datepicker').datepicker({
    format: 'dd-mm-yyyy',
    thaiyear: true,
    language: 'th',
  })
});      
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
      };;
});
function setExEnd(){
    var st = $('#ex_start').val()
    var html=''
    st = parseInt(st)+1
    for(var i = st;i<=22;i++){
        html +="<option value='"+i+"'>"+i+".00 น.</option>"
    }
    $('#ex_end').html(html)
}
function addEx(){
    $('#ex_start').val('8')
    $('#ex_end').val('9')
    $('#idHide').html("")
    $('#extratime-form').modal('show')
}
function changeEx(id,room_id,st,end){
    var st_date = dateThaiYearBC(st.substring(0,10))
    var end_date = dateThaiYearBC(end.substring(0,10))
    $('#room_id').val(parseInt(room_id))
    $('input[name="daterange"]').data('daterangepicker').setStartDate(st_date);
    $('input[name="daterange"]').data('daterangepicker').setEndDate(end_date);
    $('#ex_start').val(parseInt(st.substring(11,13)))
    setExEnd()
    $('#ex_end').val(parseInt(end.substring(11,13)))
    $('#idHide').html("<input type='hidden' name='id' value='"+id+"'>")
    $('#extratime-form').modal('show')
}
function confirmDeleteEx (id,name) {
        swal({
            title: "คุณต้องการลบเวลาการใช้งานวันที่ "+name+" หรือไม่?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
            buttons: ["ยกเลิก", "ยืนยัน"]
        })
        .then((willDelete) => {
            if (willDelete) {
                var path = "{{url('control/room_open/delete')}}/"+id
                window.location.href = path
            }
        })
    }

$('#extratime-form').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
})
</script>  
<script>
$(function() {
    $('input[name="daterange"]').daterangepicker({
    opens: 'left',
    locale: {
        format: 'DD/MM/YYYY'
    }
    }, function(start, end, label) {
    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });
});
</script> 
@endsection