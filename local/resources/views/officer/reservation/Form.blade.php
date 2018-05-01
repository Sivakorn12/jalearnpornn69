<?php
  use App\func as func;

  $user_id = Auth::user()->id;
  $sections = func::GetSection();
?>
@extends('layouts.officer',['page'=>'reservation'])
@section('page_heading','จองห้องประชุม')
@section('content')
<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">
  <div class="panel panel-default">
    <div class="panel-heading">กรอกข้อมูลการจองห้องประชุม</div>
      <div class="panel-body">
        <form class="form-horizontal" action="{{ url('control/reservation/confirm') }}" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label class="col-sm-2 control-label">ห้องประชุม</label>
            <div class="col-sm-10">
              <p class="form-control-static">{{$room->meeting_name}}</p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" >เวลาเริ่มใช้</label>
            <div class="col-sm-10">
              <p class="form-control-static">{{$time_reserve}}</p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">จำนวนเวลา</label>
            <div class="col-sm-5">
              <select class="sectionlist form-control" name="time_use">
                @for($index = 1; $index <= $time_remain; $index++)
                  <option value="{{$index}}">{{$index}}</option>
                @endfor
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">หัวข้อการประชุม</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="detail_topic" maxlength="100">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">จำนวนผู้เข้าประชุม</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="detail_count" maxlength="3">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">ชื่อผู้ติดต่อ</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="contract_name" maxlength="100">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">เบอร์โทรติดต่อ</label>
            <div class="col-sm-5">
              <input type="text" class="form-control" name="user_tel" maxlength="10" placeholder="0123456789">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">สาขา</label>
            <div class="col-sm-5">
              <select class="sectionlist form-control" name="section_id">
                @foreach($sections as $section)
                  <option value="{{$section->section_ID}}">{{$section->section_name}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">เอกสารการจอง</label>
            <div class="col-sm-5">
              <input type="file" class="form-control" name="contract_file[]" multiple>
            </div>
          </div>
    
          <input type="hidden" name="user_id" value="{{$user_id}}">
          <input type="hidden" name="meeting_id" value="{{$room->meeting_ID}}">
          <input type="hidden" name="time_reserve" value="{{$time_reserve}}">
          <input type="hidden" name="checkin" value="{{$checkin}}">
          <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
          <div class="form-group">
            <div class="col-sm-2"></div>
            <div class="col-sm-10">
              <button type="submit" class="btn btn-success">ยืนยันการจอง</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-1"></div>
</div>
@endsection