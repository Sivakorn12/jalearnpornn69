<?php
use App\Officer as officer;
$roomTypes = officer::getTypeRoom();
$equips = array();
if(isset($room))
    $equips = officer::getEquips($room->meeting_ID);
//dd($equips);


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
                <form action="{{url('control/room/'.$action)}}" class="form-horizontal" method="POST" enctype="multipart/form-data">
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
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label">ลิ้งค์แบบประเมิน</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="est_link" name="est_link" value="{{(isset($room->estimate_link))?$room->estimate_link:old('est_link')}}">
                                <p  style="color:red">@if($errors->has('est_link')) {{$errors->first('est_link')}}@endif</p>
                            </div>
                        </div>
                        @if(isset($room->estimate_link))
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-7" style="height:180px">
                                {!!officer::genQR_code($room->estimate_link)!!}
                            </div>
                        </div>
                        @endif
                        <div class="form-group form-room" >
                            <label class="col-sm-3 control-label" >รูปภาพ</label>
                            <div class="col-sm-7">
                                <input name="room_image[]" type="file" class="form-control" multiple onchange='handleFiles(this.files)' name="room_image"  accept="image/x-png,image/gif,image/jpeg" >
                                <p id="error-pic" style="display:none;color:red">กรุณาเลือกรูปภาพเท่านั้น</p>
                            </div>
                        </div>
                        <div class="form-group form-room" >
                                <label class="col-sm-3 control-label" >อุปกรณ์ภายใน</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="input-equip-name" >
                                </div>
                                <label class="col-sm-1 control-label" >จำนวน</label>
                                <div class="col-sm-2">
                                        <input type="number" class="form-control" min="1" id="input-equip-amount" >
                                </div>
                                <div class="col-sm-1 control-label" >
                                    <button style="margin-top: -3px" type="button" class="btn btn-default btn-circle" onclick="addEquioment()">
                                        <i style="margin-top:3px"class="fa fa-lg fa-plus" aria-hidden="true"></i>
                                    </button>
                                </div>
                        </div>
                        <div class="form-group form-room" id="div-show-equip" style="display:none">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-7">
                                <ul style="-webkit-padding-start: 15px;" id="list-equip">
                                </ul>
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
                    <div id="hideEquip"></div>
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                    <input type="hidden" name="changeEq" id="changeEq" value="no" />
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
    var equip =[]
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
 function addEquioment(){
     console.log(equip.length)
     var name = $('#input-equip-name').val()
     var amount = ($('#input-equip-amount').val()=='')? 0:$('#input-equip-amount').val()
     if (checkDuplicate(name, equip)) {
        equip[equip.length] = [name,amount];
    }
    fetchListEquip(equip);
    $('#input-equip-name').val('')
    $('#input-equip-amount').val('')
    $('#changeEq').val('yes');
 }
 function checkDuplicate(newVal, arrVal) {
    for (var m = 0; m < arrVal.length; m++)
        if (newVal == arrVal[m][0] ) return false;
    return true;
 }
 function fetchListEquip(equipment){  
     if(equipment.length == 0){
        $('#div-show-equip').hide()
     }else{
        console.log("process fetch")
        var html = ''
        for(var i = 0 ; i < equipment.length ; i++){
            html +='<li>'+
                        '<b>'+equipment[i][0]+'</b> จำนวน : '+equipment[i][1]+
                        ' <i class="fa fa-times" aria-hidden="true" title="ลบ" onclick="deleteEquip('+i+')"></i>'+
                    '</li>'
        }
        pushHiddenEquip(equipment)
        $('#list-equip').html(html)
        $('#div-show-equip').show()
    }
    console.log(equipment)
 }

 function pushHiddenEquip(equipment){
    var html =''
    for(var i = 0 ; i < equipment.length ; i++){
        html +='<input type="hidden" name="hdnEq[]" value="'+equipment[i]+'">'
    }
    $('#hideEquip').html(html)
 }
 function deleteEquip(index){
    equip.splice(index, 1);
    fetchListEquip(equip)
    $('#changeEq').val('yes');
 }
</script>   
<script>
     $(document).ready(function() {
      if('{{$action}}'=='update'){
        <?php
          $cnt = 0;
            foreach($equips as $key=>$eq){
          ?>
            equip[{{$cnt}}] = ['{{$eq->em_in_name}}','{{$eq->em_in_count}}'];
          <?php
          $cnt++;
          }
        ?>
        fetchListEquip(equip)
      }    
    });
</script>
@endsection