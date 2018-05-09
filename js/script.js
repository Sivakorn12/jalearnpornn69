$('#booking-detail').on('show.bs.modal', function(event) {
  var button = $(event.relatedTarget)
  var id = button.data('id'); 
    var modal = $(this)
  console.log(window.location.pathname + "/view/" + id)  
  $.ajax({
      url: window.location.pathname + "/view/" + id,
      type: 'GET',
      dataType: 'JSON',
      data: { id: button.data('id') },
      success: function(data) {
          modal.find('#showView').html(data.html);
      }
  });
});

function fecthdataBooking(){
  console.log('fetch Booking At:'+ new Date())
  $.ajax({
      url: window.location.pathname + "/fetchTbBooking",
      type: 'GET',
      dataType: 'JSON',
      data: '',
      success: function(data) {
          $('#all').html(data.tball);          
          $('#wait').html(data.tbwait);
          $('#confirmed').html(data.tbconfirmed);
          $('#tb-wait').DataTable();
          $('#tb-all').DataTable();
          $('#tb-confirmed').DataTable();
      }
  });
}

function showModal(){
    $('#my-modal').modal({
        show: 'true'
    });
}

function detailHoliday(event){
    var html = ''
    var today = new Date(event.end);
    var yesterday = new Date(today);
    yesterday.setDate(today.getDate()-1);
    
    yesterday = moment(yesterday).format('YYYY-MM-DD')
    html +="<table>"+
             "<tr><td width='80px'>หัวข้อ</td><td>"+event.title+"</td></tr>"+
             "<tr><td>รายละเอียด</td><td>"+event.description+"</td></tr>"+
             "<tr><td>วันที่หยุด</td><td>"+dateThai(event.start.format())+"</td></tr>"+
             "<tr><td>ถึง</td><td>"+dateThai(yesterday) +"</td></tr>"+
            "</table>"+
            "<br><br><a class='btn btn-danger' href='{{url('control/holiday/delete')}}/"+event.id+"'>เอาวันหยุดออก</a>";
    $('#msgConfirm').html(html)
    $('#detailModal').modal('show')
}

function FormaddHoliday(day){
    $("#date_start").val(dateThai(day))
    $('#formModal').modal('show')
}
function dateThai(day){
    var dmy = day.split("-");
    return dmy[2]+"-"+dmy[1]+"-"+(parseInt(dmy[0])+543).toString()
}
