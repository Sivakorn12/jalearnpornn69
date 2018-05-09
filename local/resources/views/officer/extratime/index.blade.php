@extends('layouts.officer',['page'=>'extratime'])
@section('page_heading','เวลาการใช้งาน')
@section('content')
<div class="row">
   
    <div class="col-md-1"></div>
    <div class="col-md-10" id="tableroom"> 
        <div style="float:right;margin-bottom:10px"> 
            <a data-toggle="modal" data-target="#extratime-form" class="btn btn-success" data-toggle="tooltip" title="เพิ่ม"><i class="fa fa-plus" aria-hidden="true"></i></a>
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
                <td>{{substr($ex->extra_start,0,10)}}</td>
                <td>{{substr($ex->extra_start,-8,5)}} - {{substr($ex->extra_end,-8,5)}}</td>
                <td>
                    <a class="btn btn-warning btn-sm" data-toggle="tooltip" href="{{url('control/equipment/edit/'.$ex->extra_ID)}}" title="แก้ไข"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a class="btn btn-danger btn-sm" data-toggle="tooltip" href="{{url('control/equipment/delete/'.$ex->extra_ID)}}" 
                    onclick="return confirm('คุณต้องการลบห้อง {{$ex->extra_start}} หรือไม่?');" title="ลบ"><i class="fa fa-times" aria-hidden="true"></i></i></a>
                </td>
            </tr>
            @endforeach
           </tbody>
        </table>
    </div>
    <div class="col-md-1"></div>
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
            <form action="{{url('control/extratime/add')}}" method="GET" >
      
                <div class="form-group form-room">
                        <label class="col-sm-3 control-label">วันที่เริ่ม</label>
                        <div class="col-sm-7">
                            <input type="text" name="date_start" id="date_start" class="datepicker" data-provide="datepicker" data-date-language="th-th">
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
      if('{{session("successMessage")}}' != null) $.notify('{{session("successMessage")}}',"success");
      else if('{{session("errorMesaage")}}' != null) $.notify('{{session("errorMesaage")}}',"error");
});
</script>   
@endsection