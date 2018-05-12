@extends('layouts.app')
@section('page_heading','ประวัติการจองห้องประชุม')
@section('content')
<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#reserve">ประวัติจองห้องประชุม</a></li>
      <li><a data-toggle="tab" href="#borrow">ประวัติยืมอุปกรณ์</a></li>
    </ul>
  </div>

  <div class="col-md-12">
    <div class="tab-content">
      <div id="reserve" class="tab-pane fade in active">
        @component('History_user.component._tb_reserve',[
          "reserves"=>$historys,
          "years_th"=>$years_th,
          "time_start"=>$time_start,
          "time_out"=>$time_out,
          "checkin_date"=>$checkin_date,
          "check_date"=>$check_date
        ])
        @endcomponent
      </div>
      <div id="borrow" class="tab-pane fade">
        @component('History_user.component._tb_borrow',[
          "borrows"=>$history_borrow,
          "checkin_date"=>$checkin_date,
          "check_date"=>$check_date,
          "checkin_borrow"=>$checkin_borrow
        ])
        @endcomponent
      </div>
    </div>
  </div>
</div>
<script>
    $(document).ready(function() {
        var Oncheck_message = '{{session('message')}}'
        
        if (Oncheck_message) {
            $.notify(Oncheck_message, 'success')
        }
    });
</script>
@endsection