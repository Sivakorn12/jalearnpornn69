@extends('layouts.officer',['page'=>'room'])
@section('page_heading','จัดการห้องประชุม')
@section('content')
<div class="row">
    <div class="col-xs-12" id="tableroom"> 
        <table class="table table-hover showroom" id="tb-room">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ห้อง</th>
                    <th>วันที่</th>
                    <th>เวลา</th>
                    <th>สถานะ</th>
                    <th></th>
                </tr>
            </thead>
           <tbody>
            @foreach($rooms as $key => $room )
            
            @endforeach
           </tbody>
        </table>
      
    </div>
</div>
<script>       
    $(document).ready(function() {
      $('#tb-room').DataTable();
    });
</script>   
@endsection