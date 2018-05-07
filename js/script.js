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

