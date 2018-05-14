@extends('layouts.admin')
@section('page_heading','จัดการผู้ใช้งาน')
@section('content')
<div class="row">
  <table class="table table-hover showroom" id="table_users">
    <thead>
        <tr>
            <th>ชื่อผู้ใช้งาน</th>
            <th>email ผู้ใช้งาน</th>
            <th>สถานะผู้ใช้งาน</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
      @foreach($users as $key => $user)
      <tr>
        <td>{{$user->user_name}}</td>
        <td>{{$user->user_email}}</td>
        <td>
          @if($user->user_status == 'user') <span class="label label-success">ผู้ใช้งานทั่วไป</span>
          @elseif($user->user_status == 'superuser') <span class="label label-info">เจ้าหน้าที่</span>
          @else <span class="label label-danger">admin</span>
          @endif
        </td>
        <td>
          @if($user->user_status != 'admin')
          <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#exampleModal" data-user="{{$users[$key]->id}}">แก้ไขสถานะ</button>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  </div>

  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
        </div>
      </div>
    </div>
  </div>
<script>
  $(document).ready(function() {
      $('#table_users').DataTable();

      var Oncheck_message = '{{session('message')}}'
        
      if (Oncheck_message) {
          $.notify(Oncheck_message, 'success')
      }
  });

  $('#exampleModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget)
    var id_user = button.data('user')
    var path = `{{url('admin/manageUser/editstatus')}}`
    var modal = $(this)

    $.ajax({
        url: path,
        type: 'GET',
        dataType: 'JSON',
        data: {id: id_user},
        success: function(data){
          console.log(data.html)
          modal.find('.modal-body').html(data.html)
        }
    })
  })
</script>
@endsection
