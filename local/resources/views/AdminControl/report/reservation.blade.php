<?php
use App\Officer as officer;
?>
@extends('layouts.admin')
@section('page_heading','รายงานสรุปการจองห้องประชุม')
@section('content')
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>

<div class="row" style="padding-bottom: 15px;">
  <div class="col-xs-12" style="text-align: -webkit-right;">
    <div style="    display: inline-flex;">
      <span style="width: 198px;
      padding-right: 10px;
      margin-top: 9px;font-weight:600"> เลือกช่วงเวลา </span><input type="text" class="form-control" name="daterange" style="max-width:250px"/>
    </div>
    
  </div>
</div>

<div class="row">
  <div class="col-xs-12">
  <table class="table table-hover showroom" id="table_report">
    <thead>
        <tr>
            <th>วันที่</th>
            <th>ห้องประชุม</th>
            <th>ผู้จอง</th>
            <th>ช่วงเวลา</th>
        </tr>
    </thead>
    <tbody id="data-list">
        @if($showtb)
          @foreach($datas as $key => $data)
          <tr>
          <td>{{date('d/m/Y',strtotime($data->checkin))}}</td>
          <td>{{$data->meeting_name}}</td>
          <td>{{$data->booking_name}}</td>
          <td>{{$data->start_time}} - {{$data->end_time}}</td>
          </tr>
          @endforeach
        @else
          <tr>
            <td colspan="3" style="text-align: center">กรุณาเลือกช่วงเวลาก่อนจะดูรายงาน</td>
          </tr>
        @endif
    </tbody>
  </table>
  </div>
  </div>




<script>
  $(document).ready(function() {
    var groupColumn = 0;
    var st = '{{date("d/m/Y",strtotime($_GET["start_dt"]))??date("d/m/Y")}}'
    var end = '{{date("d/m/Y",strtotime($_GET["end_dt"]))??date("d/m/Y")}}'
    $('input[name="daterange"]').daterangepicker({
      startDate: st, 
      endDate: end ,
      opens: 'left',
      locale: {
          format: 'DD/MM/YYYY'
      }
    }, function(start, end, label) {
      window.location.href = window.location.pathname+"?start_dt="+start.format('YYYY-MM-DD')+"&end_dt="+end.format('YYYY-MM-DD')   
    })
    
    $('#table_report').DataTable({
      dom: 'Bfrtip',
      buttons: [
          'excelHtml5'
      ],
      columnDefs: [
            { "visible": false, "targets": groupColumn }
      ],
      "order": [[ groupColumn, 'asc' ]],
      "displayLength": 25,
      "drawCallback": function ( settings ) {
          var api = this.api();
          var rows = api.rows( {page:'current'} ).nodes();
          var last=null;

          api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
              if ( last !== group ) {
                  $(rows).eq( i ).before(
                      '<tr class="group"><td colspan="3"  style="background-color:#ddd">'+group+'</td></tr>'
                  );

                  last = group;
              }
          } );
      }
    });
  });
</script>
@endsection
