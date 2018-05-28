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
                <a href="" style="margin-top:-10px" class="navbar-brand"><img src="{{url('asset/icons/kmutnb-logo.png')}}" width="45" alt=""></a>
                <a class="navbar-brand" href="{{ url ('') }}">ระบบการจองและการใช้ห้องประชุมออนไลน์</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">            
                <li>
                <p >วันที่: {{date('Y-m-d')}}</p>
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
                        <li {{ (Request::is('/') ? 'class="active"' : 'class="active"') }}>
                            <a href="{{ url ('') }}"><i class="fa fa-home fa-fw"></i> หน้าหลัก</a>
                        </li>
                        <li {{ (Request::is('reserve/') ? 'class="active"' : '') }}>
                            <a href="{{ url ('reserve/') }}"><i class="fa fa-book fa-fw"></i> จองห้อง</a>
                            <!-- /.nav-second-level -->
                        </li>
                        <li >
                            <a href="{{ url ('history/') }}">
                                <i class="fa fa-bookmark fa-fw"></i> ประวัติการใช้งาน
                            </a>
                        </li>
                        <li >
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

    <!-- Scripts -->
</body>
</html>
