@extends('layouts.officer',['page'=>'equipment'])
@section('page_heading','จัดการอุปกรณ์')
@section('content')
<div class="row">
    <div class="col-xs-12" style="padding-bottom:10px">
        <div style="float:right"> 
            <a class="btn btn-success" data-toggle="tooltip" href="{{url('control/equipment/form')}}" title="เพิ่ม"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="col-xs-12" id="tableroom"> 
        <table class="table table-hover showroom" id="tb-room">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ชื่ออุปกรณ์</th>
                    <th>จำนวน</th>
                    <th width="60"></th>
                </tr>
            </thead>
           <tbody>
            @foreach($equipments as $key => $equipment )
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$equipment->em_name	}}</td>
                <td>{{$equipment->em_count}}</td>
                <td>
                    <a class="btn btn-warning" data-toggle="tooltip" href="{{url('control/room/edit/'.$equipment->em_ID)}}" title="แก้ไข"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a class="btn btn-danger" data-toggle="tooltip" href="{{url('control/room/delete/'.$equipment->em_ID)}}" 
                    onclick="return confirm('คุณต้องการลบห้อง {{$equipment->em_name}} หรือไม่?');" title="ลบ"><i class="fa fa-times" aria-hidden="true"></i></i></a>
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