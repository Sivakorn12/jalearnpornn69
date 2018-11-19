<?php
use App\Officer as officer;
?>
@extends('layouts.officer',['page'=>'extratime'])
@section('page_heading','เวลาการใช้งาน')
@section('content')
<div class="row">
    <div class="col-md-12" id="tableroom"> 
        <div style="float:right;margin-bottom:10px">
            <a data-toggle="modal" onclick="addEx()" class="btn btn-success" data-toggle="tooltip" title="เพิ่ม"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </div>
        <table class="table table-hover showroom" id="tb-room">
            <thead>
                <tr>
                    <th>#</th>
                    <th>วันที่</th>
                    <th>เวลา</th>
                    <th width="60"></th>
                </tr>
            </thead>
           <tbody>
            @foreach($exs as $key => $ex )
            <tr>
                <td>{{$key+1}}</td>
                <td>{{officer::dateDBtoBE(substr($ex->extra_start,0,10))}}</td>
                <td>{{substr($ex->extra_start,-8,5)}} - {{substr($ex->extra_end,-8,5)}}</td>
                <td>
                    <a class="btn btn-warning btn-sm" data-toggle="tooltip" data-toggle="modal" onclick="changeEx('{{$ex->extra_ID}}','{{$ex->extra_start}}','{{$ex->extra_end}}')" title="แก้ไข"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a class="btn btn-danger btn-sm" data-toggle="tooltip" onclick="confirmDeleteEx({{$ex->extra_ID}},'{{officer::dateDBtoBE(substr($ex->extra_start,0,10))}}');" title="ลบ"><i class="fa fa-times" aria-hidden="true"></i></i></a>
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
          <h4 class="modal-title" id="myModalLabel">เวลาการใช้งาน</h4>
        </div>
        <div class="modal-body">
            <form action="{{url('control/extratime/add')}}" class="form-horizontal" method="POST" >
                <div class="form-group form-room">
                        <label class="col-sm-3 control-label">วันที่</label>
                        <div class="col-sm-7">
                            <input type="text" name="date_start" id="date_start" class="datepicker" data-provide="datepicker" data-date-language="th-th">
                        </div>
                </div>
                <div class="form-group form-room">
                        <label class="col-sm-3 control-label">เวลา</label>
                        <div class="col-sm-3">
                            <select name="ex_start" onchange="setExEnd()" class="form-control"id="ex_start">
                                @for($n = 8 ; $n<=19 ;$n++)
                                    <option value="{{$n}}">{{$n}}.00 น.</option>
                                @endfor
                            </select>
                        </div>
                        <label class="col-sm-1 control-label">ถึง</label>
                        <div class="col-sm-3">
                            <select name="ex_end" class="form-control"id="ex_end">
                                @for($n = 9 ; $n<=20 ;$n++)
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
    for(var i = st;i<=20;i++){
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
function changeEx(id,st,end){
    var dThai = dateThaiYearBC(st.substring(0,10))
    $('#ex_start').val(parseInt(st.substring(11,13)))
    setExEnd()
    $('#ex_end').val(parseInt(end.substring(11,13)))
    if(dThai != ''){
      $('.datepicker').datepicker("setDate", dThai);
    }
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
                var path = "{{url('control/extratime/delete')}}/"+id
                window.location.href = path
            }
        })
    }
</script>   
@endsection