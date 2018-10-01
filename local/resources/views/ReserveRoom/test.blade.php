@extends('layouts.app')
@section('page_heading','จองห้องประชุม')
@section('content')
<div class="row">
          <div class="col-md-12">
          {{$room_id}}
          {{$room_name}}
          {{$time_reserve}}
          {{$time_remain}}
          {{$time_select}}
          {{$dataReserve}}
          {{$dataBorrow}}
          </div>
  </div>
<script>
</script>
@endsection
