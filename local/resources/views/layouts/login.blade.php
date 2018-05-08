<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

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
  <div class="bg-login">
    <div class="form-login">
      @yield('content')
    </div>
  </div>
</body>
<style>
  .bg-login {
    background: linear-gradient(to bottom right, #e6f2ff, #8080ff);
    height: 100vh;
  }

  .form-login {
    padding-top: 30vh;
  }
</style>
</html>
