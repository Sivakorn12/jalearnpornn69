@extends('layouts.officer',['page'=>'equipment'])
@section('page_heading','การยืม - การคืนอุปกรณ์')
@section('content')
<div class="row">
    <div class="col-xs-12">
        <ul class="nav nav-tabs" style="margin-bottom:10px">
            <li class="active"><a data-toggle="tab" href="#borrow">รายการการยืม</a></li>
            <li><a data-toggle="tab" href="#return">รายการการคืน</a></li>
        </ul>
    </div>
    <div class="col-xs-12" id="tableroom"> 
        <div class="tab-content">
            <div id="borrow" class="tab-pane fade in active">
                <div class="tbEQ-section">
                    <h4><b><i class="fa fa-calendar-check-o" aria-hidden="true"></i> รายการการยืมในวันนี้</b></h4>
                    @component('officer.return-eq._tb-borrow',[
                        "type" => 'borrowtoday'
                        ])
                    @endcomponent
                </div>
                <div class="tbEQ-section">
                    <h4><b><i class="fa fa-calendar" aria-hidden="true"></i> รายการการยืมอุปกรณ์ทั้งหมด</b></h4>
                    @component('officer.return-eq._tb-borrow',[
                        "type" => 'borrow'
                        ])
                    @endcomponent
                </div>
            </div>
            <div id="return" class="tab-pane fade">
                <div class="tbEQ-section">
                    <h4><b><i class="fa fa-calendar-times-o" aria-hidden="true"></i> รายการที่ยังไม่ได้คืนอุปกรณ์</b></h4>
                    @component('officer.return-eq._tb-return',[
                        "type" => 'notreturn'
                        ])
                    @endcomponent
                </div>
                <div class="tbEQ-section">
                    <h4><b><i class="fa fa-calendar-check-o" aria-hidden="true"></i> รายการที่คืนอุปกรณ์แล้ว</b></h4>
                    @component('officer.return-eq._tb-return',[
                        "type" => 'return'
                        ])
                    @endcomponent
                </div>
            </div>
          </div>
    </div>
</div>
<div id="detailModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">ดูรายละเอียดการยืม</h4>
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
      $('#tb-room').DataTable();
      $('[data-toggle="tooltip"]').tooltip(); 
      var msg = ''
      if('{{session("successMessage")}}' != null){
        var Oncheck_message = '{{session("successMessage")}}'
        if (Oncheck_message) {
          swal(Oncheck_message, {
                icon: "success",
                buttons: false
              })
              setTimeout(function(){ window.location.reload() }, 1000);
        }
      }
      else if('{{session("errorMesaage")}}' != null) {
        var Oncheck_message = '{{session("errorMessage")}}'
        if (Oncheck_message) {
          swal(Oncheck_message, {
                icon: "error",
                buttons: false
              })
              setTimeout(function(){ window.location.reload() }, 1000);
        }
      };
    });

    function viewBorrow(id){
        var path = window.location.pathname+"/viewdetailBorrow"
        $.ajax({
            url: path,
            type: 'GET',
            dataType: 'JSON',
            data: { id: id ,type:'borrow' },
            success: function(data) {
                $('#msg').html(data.html);
            }
        });
        $('#detailModal').modal('show')  
    }
    function viewReturn(id){
        var path = window.location.pathname+"/viewdetailBorrow"
        $.ajax({
            url: path,
            type: 'GET',
            dataType: 'JSON',
            data: { id: id ,type:'returnBooking' },
            success: function(data) {
                $('#msg').html(data.html);
            }
        });
        $('#detailModal').modal('show')  
    }
</script>   
@endsection