$('#booking-detail').on('show.bs.modal', function(event) {
  var button = $(event.relatedTarget)
  var id = button.data('id'); 
  var modal = $(this)
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
  $.ajax({
      url: window.location.pathname + "/fetchTbBooking/",
      type: 'GET',
      dataType: 'JSON',
      data: '',
      success: function(data) {
          modal.find('#showView').html(data.html);
      }
  });
  setTimeout(function(){ fecthdataBooking() }, 3000);
}