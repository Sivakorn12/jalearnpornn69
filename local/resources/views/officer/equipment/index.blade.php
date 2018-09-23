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
                    <a class="btn btn-warning" data-toggle="tooltip" href="{{url('control/equipment/edit/'.$equipment->em_ID)}}" title="แก้ไข"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a class="btn btn-danger" data-toggle="tooltip" onclick="confirmDeleteEquipment({{$equipment->em_ID}},'{{$equipment->em_name}}');" title="ลบ"><i class="fa fa-times" aria-hidden="true"></i></i></a>
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
      };
    });

    function confirmDeleteEquipment (id,name) {
        swal({
            title: "คุณต้องการลบ "+name+" หรือไม่?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
            buttons: ["ยกเลิก", "ยืนยัน"]
        })
        .then((willDelete) => {
            if (willDelete) {
                var path = "{{url('control/equipment/delete')}}/"+id
                window.location.href = path
            }
        })
    }
</script>   
@endsection