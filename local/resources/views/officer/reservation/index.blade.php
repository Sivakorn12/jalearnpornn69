
@extends('layouts.officer',['page'=>'reservation'])
@section('page_heading','ตรวจสอบการจองห้อง')
@section('content')
 <div class="row">
      <div class="col-xs-12">
          <ul class="nav nav-tabs" style="margin-bottom:10px">
              <li class="active"><a data-toggle="tab" href="#all">การจองทั้งหมด</a></li>
              <li><a data-toggle="tab" href="#wait">รออนุมัติ</a></li>
              <li><a data-toggle="tab" href="#confirmed">อนุมัติแล้ว</a></li>
          </ul>
      </div>
      <div class="col-xs-12" id="tableroom"> 
          <div class="tab-content">
              <div id="all" class="tab-pane fade in active">
                  @component('officer.reservation._tb',[
                    "bookings"=>$bookings,
                    "type" => 'all'
                  ])
                  @endcomponent
              </div>
              <div id="wait" class="tab-pane fade">
                  @component('officer.reservation._tb',[
                    "bookings"=>$bookings,
                    "type" => 'wait'
                  ])
                  @endcomponent
              </div>
              <div id="confirmed" class="tab-pane fade">
                  @component('officer.reservation._tb',[
                    "bookings"=>$bookings,
                    "type" => 'confirmed'
                  ])
                  @endcomponent
              </div>
            </div>
      </div>
  </div>

 </div>

 <!-- Modal -->
<div id="booking-detail" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">รายละเอียดการจอง</h4>
        </div>
        <div class="modal-body">
          <p id="showView"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
  
    </div>
  </div>


<script>
  
  setTimeout(function(){ fecthdataBooking() }, 3000);
  //setTimeout(function(){ window.location.reload() }, 5000);
</script>
<script src="{{ url('js/script.js') }}"></script>
@endsection