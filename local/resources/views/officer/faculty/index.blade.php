<?php
use App\Officer as officer;
?>
@extends('layouts.officer',['page'=>'master_data'])
@section('page_heading','จัดการข้อมูลคณะ')
@section('content')
<div class="row">
    <div class="col-xs-12" style="padding-bottom:10px">
        <div style="float:right"> 
            <a class="btn btn-success" data-toggle="modal" title="เพิ่ม"  data-target="#buildingModal"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="col-xs-12" id="tableroom"> 
        <table class="table table-hover showroom" id="tb-room">
            <thead>
                <tr>
                    <th style="text-align: left">คณะ</th>
                    <th  width="80"></th>
                </tr>
            </thead>
           <tbody>
            @foreach($faculty as $key => $fac )
            <tr>
                <td style="text-align: left">{{$fac->faculty_name}}</td>
                <td>
                    <a class="btn btn-warning" data-toggle="tooltip" data-toggle="modal" onclick="changeFaculty('{{$fac->faculty_ID}}','{{$fac->faculty_name}}')" title="แก้ไข"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a class="btn btn-danger" data-toggle="tooltip" onclick="confirmDeleteFaculty({{$fac->faculty_ID}},'{{$fac->faculty_name}}');" title="ลบ"><i class="fa fa-times" aria-hidden="true"></i></i></a>
                </td>
            </tr>
            @endforeach
           </tbody>
        </table>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="buildingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog" role="document">
    <div class="modal-content">
    <form action="{{url('control/faculty/save')}}" class="form-horizontal" method="POST">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">จัดการข้อมูลคณะ</h4>
    </div>
    <div class="modal-body">
        <input type="hidden" name="faculty_id" id="faculty_id" value="">
        <input type="text" name="faculty_name" class="form-control"  id="faculty_name" placeholder="ชื่อคณะ" required>
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
        <input type="submit" class="btn btn-primary" value="บันทึก">
    </div>
    </form>
    </div>
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

    function confirmDeleteFaculty (id,name) {
        swal({
            title: "คุณต้องการลบคณะ "+name+" หรือไม่?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
            buttons: ["ยกเลิก", "ยืนยัน"]
        })
        .then((willDelete) => {
            if (willDelete) {
                var path = "{{url('control/faculty/delete')}}/"+id
                window.location.href = path
            }
        })
    }

    function changeFaculty(id,name){
        $('#faculty_id').val(id)
        $('#faculty_name').val(name)
        $('#buildingModal').modal('show')
    }

    $('#buildingModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    })
</script>   
@endsection