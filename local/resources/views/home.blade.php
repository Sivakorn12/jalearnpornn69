@extends('layouts.app')
@section('page_heading','หน้าหลัก')
@section('content')
    <div class="row">
        <div class="col-md-2">
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    ประเภทห้อง
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" id="selecttype">
                @foreach($types as $type)
                    <li><a href="#">{{$type->meeting_type_name}}</a></li>
                @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-2">
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    ขนาดห้อง
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" id="selectsize">
                @foreach($sizes as $size)
                    <li><a href="#">{{$size->meeting_size}}</a></li>
                @endforeach
                    <!-- <li><a href="#">S</a></li>
                    <li><a href="#">M</a></li>
                    <li><a href="#">L</a></li> -->
                </ul>
            </div>
        </div>
        <div class="col-md-10" id="tableroom">
            <table class="table table-hover showroom">
            @foreach($rooms as $key => $room)
                <tr>
                    <td>
                        <img src='{{$room->meeting_pic}}' width="100">
                    </td>
                    <td>
                        {{$room->meeting_name}}
                    </td>
                    <td>
                        {{$room->meeting_size}}
                    </td>
                    <td>
                        {{$room->meeting_type_name}}
                    </td>
                    <td>
                        {{$room->meeting_buiding}}
                    </td>
                    <td>
                        @if ($room->meeting_status == 1) <i class="fa fa-check-circle fa-lg" style="color: green" aria-hidden="true"></i>
                        @else <i class="fa fa-ban fa-lg" aria-hidden="true"></i>
                        @endif
                    </td>
                </tr>
            @endforeach
            </table>
        </div>
    </div>
<style>
    table.showroom > tbody > tr > td {
        vertical-align: middle;
    };
</style>
<script
  src="http://code.jquery.com/jquery-1.12.4.js"
  integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="
  crossorigin="anonymous">
</script>

<script>
       $(document).ready(function() 
        {
            $('ul#selecttype li').click(function(e) 
            { 
               var data = $(this).find("a").text();
                $.ajax({
                    url: "{{url('searchType')}}",
                    type: 'GET',
                    dataType: 'JSON',
                    data: {  _token: "{{ csrf_token() }}", type: data },
                    success: function(data) {
                        $('#tableroom').html(data.res)
                    }
                });
            });

            $('ul#selectsize li').click(function(e) 
            { 
               var data = $(this).find("a").text();
                $.ajax({
                    url: "{{url('searchSize')}}",
                    type: 'GET',
                    dataType: 'JSON',
                    data: {  _token: "{{ csrf_token() }}", size: data },
                    success: function(data) {
                        $('#tableroom').html(data.res)
                    }
                });
            });
        });
</script>
@endsection
