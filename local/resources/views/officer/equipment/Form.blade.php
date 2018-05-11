<?php
use App\Officer as officer;

?>
@extends('layouts.officer',['page'=>'equipment'])
@section('page_heading','จัดการอุปกรณ์')
@section('content')
<div class="row">
    <div class="col-xs-12" style="padding-bottom:10px">
        <div class="panel-group">
            <div class="panel panel-{{$form}}">
                <div class="panel-heading">
                  ข้อมูลอุปกรณ์
                </div>
                <div class="panel-body" >
                <form action="{{url('control/equipment/'.$action)}}" method="POST" enctype="multipart/form-data">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label">ชื่ออุปกรณ์</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="em_name" value="{{(isset($equipment->em_name))?$equipment->em_name:old('em_name')}}" >
                                <p  style="color:red">@if($errors->has('em_name')) {{$errors->first('em_name')}}@endif</p>
                            </div>
                        </div>
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label">จำนวนอุปกรณ์</label>
                            <div class="col-sm-7">
                                <input type="number" class="form-control" min="1" name="em_count" value="{{(isset($equipment->em_count))?$equipment->em_count:old('em_count')}}" >
                                <p  style="color:red">@if($errors->has('em_count')) {{$errors->first('em_count')}}@endif</p>
                            </div>
                        </div>
                        <div class="form-group form-room" >
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-7">
                                <input class="btn btn-success" type="submit" value="บันทึก">
                                <input class="btn btn-danger" type="reset" value="ยกเลิก">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                    @if(isset($equipment->em_ID))
                        <input type="hidden" name="id"  value="{{ $equipment->em_ID }}" />
                    @endif
                  </form>
                </div>
            </div>
          </div>
    </div>
</div>
<script>       
    $(document).ready(function() {
      $('#tb-room').DataTable();
      $('[data-toggle="tooltip"]').tooltip(); 
       
    });
</script>   
@endsection