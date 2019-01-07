<?php
use App\Officer as officer;
$roomTypes = officer::getTypeRoom();
$buildings = officer::getBuilding();
$equips = array();
if(isset($room)){
    $equips = officer::getEquips($room->meeting_ID);
    $pics = explode(",", $room->meeting_pic);
}


?>
@extends('layouts.officer',['page'=>'room'])
@section('page_heading','จัดการห้องประชุม')
@section('content')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
<div class="row">
    <div class="col-xs-12" style="padding-bottom:10px">
        <div class="panel-group">
            <div class="panel panel-{{$form}}">
                <div class="panel-heading">
                  ข้อมูลห้องประชุม
                </div>
                <div class="panel-body" >
                <form action="{{url('control/room/'.$action)}}" class="form-horizontal" id="form_room"  method="POST" onsubmit="return confirm('WARNING : การเปลี่ยนแปลงเวลาเปิด-ปิดห้องจะทำให้การจองห้อง '+$('#room_name').val()+' หลังจากวันนี้ที่ไม่อยู่ในช่วงเวลาที่เปลี่ยนแปลงถูกยกเลิก\n\nคุณต้องการบันทึกการเปลี่ยนแปลงหรือไม่?')" enctype="multipart/form-data">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label"><span style="color: red;">* </span>ชื่อห้องประชุม</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="room_name" name="room_name" value="{{(isset($room->meeting_name))?$room->meeting_name:old('room_name')}}" >
                                <p  style="color:red">@if($errors->has('room_name')) {{$errors->first('room_name')}}@endif</p>
                            </div>
                        </div>
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label"><span style="color: red;">* </span>ประเภทห้องประชุม</label>
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
                            <label class="col-sm-3 control-label"><span style="color: red;">* </span>ขนาดห้อง(ที่นั่ง)</label>
                            <div class="col-sm-7">
                                <input type="number" style="text-align:right" class="form-control" name="room_size" value="{{(isset($room->meeting_size))?$room->meeting_size:old('room_size')}}" >
                                <p  style="color:red">@if($errors->has('room_size')) {{$errors->first('room_size')}}@endif</p>
                            </div>
                        </div>
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label"><span style="color: red;">* </span>อาคาร</label>
                            <div class="col-sm-7">
                                <select class="form-control" name="room_building"  id="">
                                @foreach($buildings as $bd)
                                    <option value="{{$bd->building_id}}" @if(isset($room->meeting_buiding) and ($bd->building_id == $room->meeting_buiding) ) selected @endif>{{$bd->building_name}}</option>
                                @endforeach
                                </select>
                                <p  style="color:red">@if($errors->has('room_building')) {{$errors->first('room_building')}}@endif</p>
                            </div>
                        </div>
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label" >ลักษณะการใช้งาน</label>
                            <div class="col-sm-7">
                                <textarea name="provision" class="form-control"  rows="3">{{(isset($room->provision))?$room->provision:old('provision')}}</textarea>
                            </div>
                        </div>
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label"><span style="color: red;">* </span>ลิ้งค์แบบประเมิน</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="est_link" name="est_link" value="{{(isset($room->estimate_link))?$room->estimate_link:old('est_link')}}">
                                <p  style="color:red">@if($errors->has('est_link')) {{$errors->first('est_link')}}@endif</p>
                            </div>
                        </div>
                        @if(isset($room->estimate_link))
                        {{-- <div class="form-group form-room">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-7" style="height:180px">
                                {!!officer::genQR_code($room->estimate_link)!!}
                            </div>
                        </div> --}}
                        @endif
                        <div class="form-group form-room" >
                            <label class="col-sm-3 control-label" ><span style="color: red;">* </span>รูปภาพ</label>
                            <div class="col-sm-7">
                                <input name="room_image[]" type="file" class="form-control" multiple onchange='handleFiles(this.files)' name="room_image"  accept="image/x-png,image/gif,image/jpeg" >
                                <p style="color:red; font-size: 10px;">ขนาดไฟล์ไม่เกิน 2 mb</p>
                                <p id="error-pic" style="display:none;color:red">กรุณาเลือกรูปภาพเท่านั้น</p>
                            </div>
                        </div>
                        @if(isset($room->meeting_pic))
                        <div class="form-group form-room">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-4" >
                                <div id="myCarousel" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                      <div class="item active">
                                        <img class="img-responsive" src='{{url ("asset/rooms/".$pics[0])}}'>
                                      </div>
                        
                                      @foreach($pics as $key => $img)
                                      <div class="item">
                                        <img class="img-responsive" src='{{url ("asset/rooms/".$img)}}'>
                                      </div>
                                      @endforeach
                                    </div>
                        
                                    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                      <span class="glyphicon glyphicon-chevron-left"></span>
                                      <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                      <span class="glyphicon glyphicon-chevron-right"></span>
                                      <span class="sr-only">Next</span>
                                    </a>
                                  </div>
                            </div>
                        </div>
                        @endif
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
                                    <button style="margin-top: -3px" type="button" class="btn btn-default btn-circle" onclick="addEquipment()">
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
                            <label class="col-sm-3 control-label">ตารางเปิด-ปิดห้อง</label>
                            <div class="col-sm-7">
                                <table class="table table-bordered" width="100%">
                                    <thead>
                                        <tr style="background-colr:#4c9adc26">
                                            <th>วัน</th>
                                            <th>เวลาเปิด</th>
                                            <th>เวลาปิด</th>
                                            <th>ใช้งาน</th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        @foreach($room_open_time as $index => $r)
                                        <tr>
                                            <td>{{$day[$r["day_id"]]}}</td>
                                            <td><input type="text" class="form-control input-sm" name="room_open_{{$index+1}}" id="room_open_{{$index+1}}"></td>
                                            <td><input type="text" class="form-control input-sm" name="room_close_{{$index+1}}" id="room_close_{{$index+1}}"></td>
                                            <td><input type="checkbox" name="open_flag_{{$index+1}}" id="open_flag_{{$index+1}}" @if($r["open_flag"] ==1 ) checked @endif data-toggle="toggle" data-on="เปิด" data-off="ปิด" data-size="small"></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group form-room" >
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-7">
                                <input class="btn btn-success" id="submit-btn" type="submit" value="บันทึก">
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
    var open_time_list = <?php echo json_encode($room_open_time); ?>;
    $(document).ready(function() {
      $('#tb-room').DataTable();
      $('[data-toggle="tooltip"]').tooltip();  
      setTimepickerAllDay(open_time_list)
      //console.log(open_time_list)
    //   $('#submit-btn').click(function(event){
    //     //$('#form_room').submit(false)
    //     event.preventDefault();
    //     if(confirm('การเปลี่ยนแปลงเวลาเปิด-ปิดห้องจะทำให้การจองห้อง '+$('#room_name').val()+' หลังจากวันนี้ที่ไม่อยู่ในช่วงเวลาที่เปลี่ยนแปลงถูกยกเลิก\n\nคุณต้องการบันทึกการเปลี่ยนแปลงหรือไม่?')){
    //         console.log('flfl')
    //         $('#form_room').submit(true)
    //     }
    //   })

    });

    function setTimepickerAllDay(time_list){
        for(index  in open_time_list){
            $('#room_open_'+time_list[index].day_id).timepicker({
                    template: false,
                    showInputs: false,
                    minuteStep: 10,
                    showMeridian:false,
                    defaultTime:time_list[index].open_time 
            });
            $('#room_close_'+time_list[index].day_id).timepicker({
                    template: false,
                    showInputs: false,
                    minuteStep: 10,
                    showMeridian:false,
                    defaultTime:time_list[index].close_time 
            });
        }
        
    }
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
 function addEquipment(){
     var name = $('#input-equip-name').val()
     var amount = ($('#input-equip-amount').val()=='')? 0:$('#input-equip-amount').val()
     if (checkDuplicate(name, equip) && name!='') {
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