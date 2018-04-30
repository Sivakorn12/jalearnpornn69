@extends('layouts.officer',['page'=>'holiday'])
@section('page_heading','จัดการวันหยุด')
@section('content')
<div class="row">
    <div class="col-xs-12" style="padding-bottom:10px">
        <div style="float:right"> 
            <a class="btn btn-success" data-toggle="tooltip" href="{{url('control/holiday/form')}}" title="เพิ่ม"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="col-xs-12" id="tableroom"> 
        <table class="table table-hover showroom" id="tb-room">
            <thead>
                <tr>
                    <th>#</th>
                    <th>วันที่</th>
                    <th>รายละเอียด</th>
                    <th width="60"></th>
                </tr>
            </thead>
           <tbody>
            @foreach($holidays as $key => $holiday )
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$holiday->holiday_close	}}</td>
                <td>{{$holiday->holiday_detail}}</td>
                <td>
                    <a class="btn btn-warning" data-toggle="tooltip" href="{{url('control/holiday/edit/'.$holiday->holiday_ID)}}" title="แก้ไข"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a class="btn btn-danger" data-toggle="tooltip" href="{{url('control/holiday/delete/'.$holiday->holiday_ID)}}" 
                    onclick="return confirm('คุณต้องการลบวันหยุดวันที่ {{$holiday->holiday_close}} หรือไม่?');" title="ลบ"><i class="fa fa-times" aria-hidden="true"></i></i></a>
                </td>
            </tr>
            @endforeach
           </tbody>
        </table>
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
@endsection