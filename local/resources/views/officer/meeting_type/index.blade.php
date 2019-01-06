<?php
use App\Officer as officer;
?>
@extends('layouts.officer',['page'=>'room'])
@section('page_heading','จัดการประเภทห้องประชุม')
@section('content')
<div class="row">
    <div class="col-xs-12" style="padding-bottom:10px">
        <div style="float:right"> 
            <a class="btn btn-success" data-toggle="modal" title="เพิ่ม"  data-target="#meeting_typeModal"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="col-xs-12" id="tableroom"> 
        <table class="table table-hover showroom" id="tb-room">
            <thead>
                <tr>
                    <th style="text-align: left">ประเภทห้องประชุม</th>
                    <th  width="80"></th>
                </tr>
            </thead>
           <tbody>
            @foreach($meeting_types as $key => $mt )
            <tr>
                <td style="text-align: left">{{$mt->meeting_type_name}}</td>
                <td>
                    <a class="btn btn-warning" data-toggle="tooltip" data-toggle="modal" onclick="changeRoomType('{{$mt->meeting_type_ID}}','{{$mt->meeting_type_name}}')" title="แก้ไข"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a class="btn btn-danger" data-toggle="tooltip" onclick="confirmDeleteRoomType({{$mt->meeting_type_ID}},'{{$mt->meeting_type_name}}');" title="ลบ"><i class="fa fa-times" aria-hidden="true"></i></i></a>
                </td>
            </tr>
            @endforeach
           </tbody>
        </table>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="meeting_typeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog" role="document">
    <div class="modal-content">
    <form action="{{url('control/roomtype/save')}}" class="form-horizontal" method="POST">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">จัดการประเภทห้องประชุม</h4>
    </div>
    <div class="modal-body">
        <input type="hidden" name="roomtype_id" id="roomtype_id" value="">
        <input type="text" name="roomtype" class="form-control"  id="roomtype" placeholder="ประเภทห้องประชุม" required>
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
      if('{{session("errorMessage")}}' != null){
        var Oncheck_message = '{{session("errorMessage")}}'
        if (Oncheck_message) {
          swal(Oncheck_message, {
                icon: "error",
                buttons: false
              })
              //setTimeout(function(){ window.location.reload() }, 1000);
        }
      };
    });

    function confirmDeleteRoomType (id,name) {
        swal({
            title: "คุณต้องการลบประเภทห้อง "+name+" หรือไม่?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
            buttons: ["ยกเลิก", "ยืนยัน"]
        })
        .then((willDelete) => {
            if (willDelete) {
                var path = "{{url('control/roomtype/delete')}}/"+id
                window.location.href = path
            }
        })
    }

    function changeRoomType(id,name){
        $('#roomtype_id').val(id)
        $('#roomtype').val(name)
        $('#meeting_typeModal').modal('show')
    }

    $('#meeting_typeModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    })
</script>   
@endsection