<?php
use App\Officer as officer;
?>
@extends('layouts.officer',['page'=>'master_data'])
@section('page_heading','จัดการข้อมูลสาขาวิชา')
@section('content')
<div class="row">
    <div class="col-xs-12" style="padding-bottom:10px">
        <div style="float:right"> 
            <a class="btn btn-success" data-toggle="modal" title="เพิ่ม"  data-target="#Modal"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="col-xs-12" id="tableroom"> 
        <table class="table table-hover showroom" id="tb-room">
            <thead>
                <tr>
                    <th style="text-align: left">สาขาวิชา</th>
                    <th style="text-align: left">ภาควิชา</th>
                    <th style="text-align: left">คณะ</th>
                    <th  width="80"></th>
                </tr>
            </thead>
           <tbody>
            @foreach($section as $key => $sec )
            <tr>
                <td style="text-align: left">{{$sec->section_name}}</td>
                <td style="text-align: left">{{$sec->department_name}}</td>
                <td style="text-align: left">{{$sec->faculty_name}}</td>
                <td>
                <a class="btn btn-warning" data-toggle="tooltip" data-toggle="modal" onclick="changeSection('{{$sec->section_ID}}','{{$sec->section_name}}','{{$sec->department_ID}}')" title="แก้ไข"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a class="btn btn-danger" data-toggle="tooltip" onclick="confirmDeleteSection({{$sec->section_ID}},'{{$sec->section_name}}');" title="ลบ"><i class="fa fa-times" aria-hidden="true"></i></i></a>
                </td>
            </tr>
            @endforeach
           </tbody>
        </table>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog" role="document">
    <div class="modal-content">
    <form action="{{url('control/section/save')}}" method="POST">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">จัดการข้อมูลสาขาวิชา</h4>
    </div>
    <div class="modal-body " style="padding:15px!important">
        <input type="hidden" name="section_id" id="section_id" value="">
        <div class="form-group">
            <label for="section_name">ชื่อสาขาวิชา</label>
            <input type="text" name="section_name" class="form-control"  id="section_name" placeholder="ชื่อสาขาวิชา" required>
        </div>
        <div class="form-group">
        <label for="department">ภาควิชา</label>
        <select id="department" name="department_id" class="form-control">
            @foreach($department as $dep)
                <option value="{{$dep->department_ID}}">{{$dep->department_name}}</option>
            @endforeach
        </select>
        </div>

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

    function confirmDeleteSection (id,name) {
        swal({
            title: "คุณต้องการลบสาขาวิชา "+name+" หรือไม่?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
            buttons: ["ยกเลิก", "ยืนยัน"]
        })
        .then((willDelete) => {
            if (willDelete) {
                var path = "{{url('control/section/delete')}}/"+id
                window.location.href = path
            }
        })
    }

    function changeSection(id,name,dep_id){
        $('#section_id').val(id)
        $('#section_name').val(name)
        $('#department').val(dep_id).change()
        $('#Modal').modal('show')
    }

    $('#Modal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    })
</script>   
@endsection