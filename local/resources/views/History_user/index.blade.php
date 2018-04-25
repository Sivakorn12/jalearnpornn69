@extends('layouts.app')
@section('page_heading','ประวัติการจองห้องประชุม')
@section('content')
<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#home">ประวัติจองห้องประชุม</a></li>
      <li><a data-toggle="tab" href="#menu1">ประวัติยืมอุปกรณ์</a></li>
    </ul>
  </div>

  <div class="col-md-12">
    <div class="tab-content">
      <div id="home" class="tab-pane fade in active">
        @component('History_user.component._tb_reserve',[
          "reserves"=>$historys,
        ])
        @endcomponent
      </div>
      <div id="menu1" class="tab-pane fade">
        <h3>Menu 1</h3>
        <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
      </div>
    </div>
  </div>
</div>
@endsection