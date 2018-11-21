<?php
use App\Officer as officer;
use App\func as func;
$datas = array();
$color ="#D3D3D3";
if($type == 'borrowtoday'){
  $selected_status = '';
  $datas = officer::getDataBorrow('today');
  $color ='#5cb85c';
}
elseif($type == 'borrow'){
  $selected_status = '1';
  $datas = officer::getDataBorrow();
}
$dataEquipment = func::GET_EQUIPMENT();

?>
<div class="table-responsive">
<table class="table table-bordered  showroom" id="tb-{{$type}}">
    <thead>
      <tr style='background-color:{{$color}}'>
            <th width="25">#</th>
            <th>ห้อง</th>
            <th>วันที่</th>
            <th>เวลา</th>
            <th>ผู้ติดต่อ</th>
            <th>สถานะ</th>
          {{-- @if($type=="borrowtoday") --}}
            <th></th>
          {{-- @endif --}}
        </tr>
    </thead>
   <tbody>
    @foreach($datas as $key => $data)
    <?php
      $chk = (date('Y-m-d')>=$data->checkin and $data->status_ID==3 and $data->detail_timestart<date('Y-m-d H:i:s'));
    ?>
      <tr>
        <td>{{($key+1)}}</td>
        <td>{{$data->meeting_name}}</td>
        <td>{{$data->borrow_date}}</td>
        <td >{{substr($data->detail_timestart, -8,5)}} - {{substr($data->detail_timeout, -8,5)}}</td>
        <td>{{$data->booking_name}}</td>
        <td>
          {!!($chk )? '<span class="label label-status label-default">เกินระยะเวลา(ยกเลิก)</span>' :officer::getStatusBooking($data->borrow_status,1)!!}
        </td>
        {{-- @if($type=="borrowtoday") --}}
            <td><a title='รายละเอียดการยืม' data-toggle="modal" onclick="viewBorrow({{$data->borrow_ID}})"  data-toggle="tooltip" class="glyphicon glyphicon-search" aria-hidden="true"></a></td>
        {{-- @endif --}}
      </tr>
     @endforeach
   </tbody>
</table>
</div>
<script>
var equip = []
var data_equip = <?php echo $dataEquipment ?>;
var remainEquip = <?php echo json_encode($dataEquipment) ?>;
$(document).ready(function() {
  $('#tb-{{$type}}').DataTable();
});

$(document).ready(function() {
    for (let index = 0; index < remainEquip.length; index++) {
      remainEquip[index].em_status = false
    }
  })

  
  function addEquioment() {
    var name = $('#input-equip-name').val()
    var amount = ($('#input-equip-amount').val()=='')? 0:$('#input-equip-amount').val()
     if (amount && amount > 0) {
       for (let index = 0; index < data_equip.length; index++) {
        if (data_equip[index].em_name == name && data_equip[index].em_count < amount) {
          swal('ไม่สำเร็จ', 'อุปกรณ์ '+data_equip[index].em_name+' ไม่เพียงพอ กรุณาเลือกจำนวนใหม่อีกครั้ง' , 'error')
          break
        } else if (data_equip[index].em_name == name && data_equip[index].em_count >= amount) {
          if (checkDuplicate(name,amount, equip)) {
            remainEquip[index].em_status = true
            equip[equip.length] = [name,amount];
          }
        }
      }
    }
    var html = ''
    for (let index = 0; index < data_equip.length; index++) {
      if (remainEquip[index].em_status != true) {
        html += '<option value="'+remainEquip[index].em_name+'">'+remainEquip[index].em_name+' : (เหลือจำนวน ' +remainEquip[index].em_count +')</option>'
      }
    }
    $('#input-equip-name').html(html)
    fetchListEquip(equip);
    $('#input-equip-amount').val('')
 }

 function checkDuplicate(newVal, amount, arrVal) {
    for (var m = 0; m < arrVal.length; m++)
        if (newVal == arrVal[m][0]) return false;
    return true;
 }

 function fetchListEquip(equipment){  
     if(equipment.length == 0){
        $('#div-show-equip').hide()
     }else{
        var html = ''
        for(var i = 0 ; i < equipment.length ; i++){
            html +='<li>'+
                        '<b>'+equipment[i][0]+'</b> จำนวน : '+equipment[i][1]+' ชิ้น'+
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
    var html = ''
    for (let i = 0; i < data_equip.length; i++) {
      if (equip[index][0] == remainEquip[i].em_name && remainEquip[i].em_status == true) {
        remainEquip[i].em_status = false
      }
    }

    for (let index = 0; index < data_equip.length; index++) {
      if (remainEquip[index].em_status != true) {
        html += '<option value="'+remainEquip[index].em_name+'">'+remainEquip[index].em_name+' : (เหลือจำนวน ' +remainEquip[index].em_count +')</option>'
      }
    }
    equip.splice(index, 1);
    $('#input-equip-name').html(html)
    fetchListEquip(equip)
 }

$(document).ready(function(){
  $( "body" ).delegate( "#add_borrow", "click", function() {
    $('#form_borrow').show()
  });
  $( "body" ).delegate( "#cancel_borrow", "click", function() {
    $('#form_borrow').hide()
  });

  

})
 
</script>   
