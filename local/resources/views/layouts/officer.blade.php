<?php
use App\Officer as officer;
?>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ระบบการจองและการใช้ห้องประชุมออนไลน์</title>

    <!-- Styles -->
    <link href="{{ url('css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ url('css/styles.css') }}" rel="stylesheet">
    <link href="{{ url('css/custom-style.css') }}" rel="stylesheet">
    <link href="{{ url('css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ url('css/datepicker.css')}}">
    <!-- Scripts -->
    
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="{{ url('js/bootstrap.min.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{ url('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ url('js/notify.js') }}"></script>
   
    <script type="text/javascript" src="{{ url('js/bootstrap-datepicker-custom.js')}}"></script>
    <script type="text/javascript" src="{{ url('js/bootstrap-datepicker.th.js')}}"></script>
    <script type="text/javascript" src="{{ url('js/script.js')}}"></script>
    <script src="{{ url('js/moment.js') }}"></script>
    <script src="{{ url('js/moment-timezone.js') }}"></script>
    <script src="{{ url('js/moment-timezone-with-data-2012-2022.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script>      
    <script type="text/javascript" src="{{ url('js/fullcalendar.th.js')}}"></script>
</head>
<body>
    <div id="wrapper" class="main">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top main-navbar" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="{{url("control")}}" style="margin-top:-10px" class="navbar-brand"><img src="{{url('asset/icons/kmutnb-logo.png')}}" width="45" alt=""></a>
                <a  class="navbar-brand" href="{{ url ('') }}">ระบบการจองและการใช้ห้องประชุมออนไลน์</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <li>
                <p >วันที่: {{officer::dateDBtoBE(date('Y-m-d'))}}</p>
                </li>

                <li class="dropdown">
                    <a class="button dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-bell fa-lg"></i>
                            <span class="button_badge"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-user noti">
                        
                    </ul>
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span><i class="fa fa-user fa-fw"></i> {{Auth::user()->user_name }} :: {{Auth::user()->user_status}}  <i class="fa fa-caret-down"></i></span>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li {{ (Request::is('*charts') ? 'class="active"' : '') }}>
                            <a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                <i class="fa fa-power-off" aria-hidden="true"></i>
                                ออกจากระบบ
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar main" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class={{ $page === "index" ? "active" : "" }}>
                            <a href="{{ url ('/control') }}"><i class="fa fa-home fa-fw"></i> หน้าหลัก</a>
                        </li>
                        <li class={{ $page === "reservation" ? "active" : "" }}>
                            <a href="{{ url ('control/reservation/') }}"><i class="fa fa-table" aria-hidden="true"></i> การจองห้องประชุม</a>
                        </li>
                        <li class={{ $page === "checkbooking" ? "active" : "" }}>
                            <a href="{{ url ('control/checkbooking/') }}"><i class="fa fa-book fa-fw"></i> ตรวจสอบการจองห้อง</a>
                        </li>
                        <li class={{ $page === "room" ? "active" : "" }}>
                            <a data-toggle="collapse" href="#mng_room"><i class="fa fa-home" aria-hidden="true"></i> จัดการข้อมูลห้อง<span class="fa arrow fa-lg"  data-toggle="collapse" href="#mng_room"></span></a>
                            <ul id="mng_room" class="collapse nav nav-second-level">
                                <li ><a href="{{ url ('control/room/') }}"><i class="fa fa-circle-o" aria-hidden="true"></i> ห้องประชุม</a></li>
                                <li ><a href="{{ url ('control/roomtype/') }}"><i class="fa fa-circle-o" aria-hidden="true"></i> ประเภทห้อง</a></li>
                                
                            </ul>
                        </li>
                        <li class={{ $page === "equipment" ? "active" : "" }}>
                            <a data-toggle="collapse" href="#collapse1"><i class="fa fa-sitemap fa-fw"></i> อุปกรณ์<span class="fa arrow fa-lg"  data-toggle="collapse" href="#collapse1"></span></a>
                            <ul id="collapse1" class="collapse nav nav-second-level">
                                <li ><a href="{{ url ('control/return-eq/') }}"><span class="glyphicon glyphicon-retweet" aria-hidden="true"></span> การยืม-คืน</a></li>
                                <li ><a href="{{ url ('control/equipment/') }}"><span class="glyphicon glyphicon-hdd" aria-hidden="true"></span> อุปกรณ์</a></li>
                            </ul>
                        </li>
                        <li class={{ $page === "holiday" ? "active" : "" }}>
                            <a href="{{ url ('control/holiday/') }}"><i class="fa fa-flag" aria-hidden="true"></i> วันหยุด</a>
                        </li>
                        
                        <li class={{ $page === "extratime" ? "active" : "" }}>
                            <a href="{{ url ('control/extratime/') }}"<i class="fa fa-clock-o" aria-hidden="true"></i> เวลาการใช้งาน</a>
                        </li>
                        <li class={{ $page === "master_data" ? "active" : "" }}>
                            <a data-toggle="collapse" href="#master_data"><i class="fa fa-star" aria-hidden="true"></i> ข้อมูลพื้นฐาน<span class="fa arrow fa-lg"  data-toggle="collapse" href="#collapse1"></span></a>
                            <ul id="master_data" class="collapse nav nav-second-level">
                                <li ><a href="{{ url ('control/section/') }}"><i class="fa fa-circle-o" aria-hidden="true"></i> สาขาวิชา</a></li>
                                <li ><a href="{{ url ('control/department/') }}"><i class="fa fa-circle-o" aria-hidden="true"></i> ภาควิชา</a></li>
                                <li ><a href="{{ url ('control/faculty/') }}"><i class="fa fa-circle-o" aria-hidden="true"></i> คณะ</a></li>
                                <li ><a href="{{ url ('control/building/') }}"><i class="fa fa-circle-o" aria-hidden="true"></i> อาคาร</a></li>
                            </ul>
                        </li>
                        
                        <li {{ (Request::is('*charts') ? 'class="active"' : '') }}>
                            <a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                <i class="fa fa-power-off" aria-hidden="true"></i>
                                ออกจากระบบ
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                        
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
			 <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">@yield('page_heading')</h1>
                </div>
                <!-- /.col-lg-12 -->
           </div>
			
            @yield('content')

            <!-- /#page-wrapper -->
        </div>

       
    </div>
</body>
<script>
$(document).ready(function(){
    getNoti()
    setInterval(function(){ getNoti()}, 60000);
})
 
  //setTimeout(function(){ fecthdataBooking() }, 3000);
  //setTimeout(function(){ window.location.reload() }, 5000);
</script>
</html>
