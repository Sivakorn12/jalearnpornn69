@extends('layouts.officer',['page'=>'equipment'])
@section('page_heading','การยืม - การคืนอุปกรณ์')
@section('content')
<div class="row">
    <div class="col-xs-12" style="padding-bottom:10px">
      <div style="float:right"> 
          <a class="btn btn-success" data-toggle="tooltip" href="{{url('control/reservation')}}" title="เพิ่ม"><i class="fa fa-plus" aria-hidden="true"></i> เพิ่มการจอง</a>
      </div>
    </div>
    <div class="col-xs-12">
        <ul class="nav nav-tabs" style="margin-bottom:10px">
            <li class="active"><a data-toggle="tab" href="#borrow">รายการการยืม</a></li>
            <li><a data-toggle="tab" href="#return">รายการที่คืนแล้ว</a></li>
        </ul>
    </div>
    <div class="col-xs-12" id="tableroom"> 
        <div class="tab-content">
            <div id="borrow" class="tab-pane fade in active">
                @component('officer.return-eq._tb',[
                    "datas"=>$datas,
                    "type" => 'borrow'
                  ])
                  @endcomponent
            </div>
            <div id="return" class="tab-pane fade">
                
            </div>
          </div>
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