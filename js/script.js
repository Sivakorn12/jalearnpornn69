$('#booking-detail').on('show.bs.modal', function(event) {
  var button = $(event.relatedTarget)
  var id = button.data('id'); 
  var modal = $(this)
  //console.log(window.location.pathname + "/view/" + id)  
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
            "<br><br><a class='btn btn-danger' href='"+window.location.pathname+"/delete/"+event.id+"'>เอาวันหยุดออก</a>";
    $('#msgConfirm').html(html)
    $('#detailModal').modal('show')
}

function FormaddHoliday(day){
    $("#date_start").val(dateThai(day))
    $("#date_end").val(dateThai(day))
    $('#formModal').modal('show')
}
function dateThai(day){
    var dmy = day.split("-");
    return dmy[2]+"-"+dmy[1]+"-"+(parseInt(dmy[0])+543).toString()
}
function dateThaiYearBC(day){
    var dmy = day.split("-");
    return dmy[2]+"-"+dmy[1]+"-"+dmy[0]
}

function getNoti(){
    $.ajax({
        url: '/project/getNoti',
        type: 'GET',
        dataType: 'JSON',
        data: '',
        success: function(res) {
            //console.log(res.data)
            $('.button_badge').text(Object.keys(res.data).length)
            var list = setListNoti(res.data)
            $('.noti').html(list)
        }
    });
}

function setListNoti(data){
    var html =''
    if(Object.keys(data).length > 0){
        for(d in data){
            var dt = moment(data[d]["booking_date"]+ "+07:00", "YYYY-MM-DD HH:mm:ssZ")
            html += '<li>'+
                        '<p class="text-noti"><b>'+data[d]["booking_name"] + '</b> ได้จองห้อง ' + data[d]["meeting_name"]+'</p>'+
                        '<p style="padding-top:8px"><i style="font-size:12px;color:#b3b3b3;padding-left:15px;">'+dt.fromNow()+'</i></p></li>'
        }
    }
    else{
        html +='<li><a>ไม่มีเเจ้งเตือน</a></li>'
    }
    return html
}

function setDateEndCalendar(dt){
    return moment(dt, "YYYY-MM-DD").add(1, 'days');
}
$(document).ready(function(){
    $("#faculty_id").change(function(){
        var fac_id = $("#faculty_id").val()
        if(fac_id == '') $("#department_id").prop('disabled', true);
        else $("#department_id").prop('disabled', false);
        var select_dep = dep.filter(function (a) {
            return a.faculty_ID == fac_id;
        });
        var option = '<option value="">-- เลือกภาควิชา --</option>'
        for(index in select_dep){
            option += '<option value="'+select_dep[index].department_ID+'">'+select_dep[index].department_name+'</option>'
        }
        $("#department_id").html(option)
    })

    $("#department_id").change(function(){
        var dep_id = $("#department_id").val()
        if(dep_id == '') $("#section_id").prop('disabled', true);
        else $("#section_id").prop('disabled', false);
        //console.log(dep)
        var select_sec = sec.filter(function (a) {
            return a.department_ID == dep_id;
        });
        var option = '<option value="">-- เลือกภาคสาขา --</option>'
        for(index in select_sec){
            option += '<option value="'+select_sec[index].section_ID+'">'+select_sec[index].section_name+'</option>'
        }
        $("#section_id").html(option)
    })
})





