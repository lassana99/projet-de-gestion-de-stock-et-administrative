<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Approlog Admin</title>

    <!-- Custom fonts for this template-->
    <link href="{{asset ('admin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    {{-- font awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom styles for this template-->
        <link href="{{asset ('admin/css/sb-admin-2.css')}}" rel="stylesheet">
        <link href="{{asset ('admin/css/sb-admin-2.min.css')}}" rel="stylesheet">

</head>

<body class="" style="background: linear-gradient(135deg, darkslategray, #2a9877);">

    @yield('content')

    <!-- Bootstrap core JavaScript-->
    <script src="{{asset ('admin/vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset ('admin/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{asset ('admin/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{asset ('admin/js/sb-admin-2.min.js')}}"></script>

</body>

</html>
