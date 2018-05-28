@extends('layouts.admin')
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
    <div id="detailModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">ดูรายละเอียดการจอง</h4>
                </div>
                <div class="modal-body">
                  <p id="msg"></p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
          
            </div>
    </div>
<script>
  $(document).ready(function() {
    var Oncheck_message = '{{session('message')}}'
        
    if (Oncheck_message) {
        swal(Oncheck_message, {
          icon: "success",
          buttons: false
        })
        setTimeout(function(){ window.location.reload() }, 1000);
    }
  });
    function  detailreserve(event) {
       var path = '{{url("/getdataCalendar")}}/'+event.id
        $.ajax({
            url: path,
            type: 'POST',
            dataType: 'JSON',
            data: { _token: "{{ csrf_token() }}",id:event.id },
            success: function(data) {
              $('#msg').html(data.html)
            }
        });
    $('#detailModal').modal('show')
   }
</script>
@endsection