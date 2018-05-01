<?php
use App\Officer as officer;
$roomTypes = officer::getTypeRoom();
?>
@extends('layouts.officer',['page'=>'room'])
@section('page_heading','จัดการห้องประชุม')
@section('content')
<div class="row">
    <div class="col-xs-12" style="padding-bottom:10px">
        <div class="panel-group">
            <div class="panel panel-{{$form}}">
                <div class="panel-heading">
                  ข้อมูลห้องประชุม
                </div>
                <div class="panel-body" >
                <form action="{{url('control/room/'.$action)}}" method="POST" enctype="multipart/form-data">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label">ชื่อห้องประชุม</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="room_name" value="{{(isset($room->meeting_name))?$room->meeting_name:old('room_name')}}" >
                                <p  style="color:red">@if($errors->has('room_name')) {{$errors->first('room_name')}}@endif</p>
                            </div>
                        </div>
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label">ประเภทห้องประชุม</label>
                            <div class="col-sm-7">
                                <select class="form-control" name="type" id="sel1">
                                    @foreach($roomTypes as $type)
                                      <option value="{{$type->meeting_type_ID}}" @if(isset($room->meeting_type_ID) and ($room->meeting_type_ID ==$type->meeting_type_ID)) selected @endif >{{$type->meeting_type_name}}</option>
                                    @endforeach
                                </select>
                                <p style="color:red">@if($errors->has('type')) {{$errors->first('type')}}@endif</p>
                            </div>
                        </div>
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label">ขนาดห้อง</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="room_size" value="{{(isset($room->meeting_size))?$room->meeting_size:old('room_size')}}" >
                                <p  style="color:red">@if($errors->has('room_size')) {{$errors->first('room_size')}}@endif</p>
                            </div>
                        </div>
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label">อาคาร</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="room_building" value="{{(isset($room->meeting_buiding))?$room->meeting_buiding:old('room_building')}}" >
                                <p  style="color:red">@if($errors->has('room_building')) {{$errors->first('room_building')}}@endif</p>
                            </div>
                        </div>
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label" >ข้อกำหนด</label>
                            <div class="col-sm-7">
                                <textarea name="provision" class="form-control"  rows="3">{{(isset($room->provision))?$room->provision:old('provision')}}</textarea>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label">รูปภาพ</label>
                            <div class="col-sm-7">
                                <input name="room_image[]" type="file" class="form-control" multiple onchange='handleFiles(this.files)' name="room_image"  accept="image/x-png,image/gif,image/jpeg">
                                <p id="error-pic" style="display:none;color:red">กรุณาเลือกรูปภาพเท่านั้น</p>
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
                    @if(isset($room->meeting_ID))
                        <input type="hidden" name="id"  value="{{ $room->meeting_ID }}" />
                    @endif
                    @if(isset($room->meeting_pic))
                        <input type="hidden" name="oldpic"  value="{{ $room->meeting_pic }}" />
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

    function handleFiles(files){
    $('#error-pic').hide()
    var isImg = true
    for (var m = 0; m < files.length; m++){
      if(!isImage(files[m])) {
        isImg = false
        break
      }
    }
    if(!isImg){
      $('#error-pic').show()

    }
    else {
    }
  }
  function isImage(file){
    return file['type'].split('/')[0]=='image';//returns true or false
 }
</script>   
@endsection