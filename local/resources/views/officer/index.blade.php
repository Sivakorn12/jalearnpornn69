@extends('layouts.officer',['page'=>'index'])
@section('page_heading','หน้าหลัก')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script>  
<script type="text/javascript" src="{{ url('js/fullcalendar.th.js')}}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css" />
    <div class="row">
      <div class="col-xs-12" style="padding-bottom:10px">
              <div class="panel panel-primary" style="width:100%!important">  
                  <div class="panel-heading"> ปฏิทินการใช้ห้องประชุม </div>  
                  <div class="panel-body"> {!! $calendar->calendar() !!} {!! $calendar->script() !!} </div>  
              </div>  
      </div>
    </div>
<script>
</script>
@endsection