<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>500</title>

    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css?family=Maven+Pro:400,900" rel="stylesheet">
    <link href="{{ asset('css/coreui.min.css')}}" rel="stylesheet" />

</head>
<style>

    #notfound {
        position: relative;
        height: 100vh;
    }

    #notfound .notfound {
        position: absolute;
        left: 50%;
        top: 50%;
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
    }

    .notfound {
        max-width: 920px;
        width: 100%;
        line-height: 1.4;
        text-align: center;
        padding-left: 15px;
        padding-right: 15px;
    }

    .notfound .notfound-500 {
        position: absolute;
        height: 100px;
        top: 0;
        left: 50%;
        -webkit-transform: translateX(-50%);
        -ms-transform: translateX(-50%);
        transform: translateX(-50%);
        z-index: -1;
    }

    .notfound .notfound-500 h1 {
        font-family: 'Maven Pro', sans-serif;
        color: #ececec;
        font-weight: 900;
        font-size: 276px;
        margin: 0px;
        position: absolute;
        left: 50%;
        top: 50%;
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
    }

    .notfound h2 {
        font-family: 'Maven Pro', sans-serif;
        font-size: 46px;
        color: #000;
        font-weight: 900;
        text-transform: uppercase;
        margin: 0px;
    }

    .notfound p {
        font-family: 'Maven Pro', sans-serif;
        font-size: 16px;
        color: #000;
        font-weight: 400;
        text-transform: uppercase;
        margin-top: 15px;
    }

    .backHome {
        font-family: 'Maven Pro', sans-serif;
        font-size: 14px;
        text-decoration: none;
        text-transform: uppercase;
        background: #189cf0;
        display: inline-block;
        padding: 16px 38px;
        border: 2px solid transparent;
        border-radius: 40px;
        color: #fff;
        font-weight: 400;
        -webkit-transition: 0.2s all;
        transition: 0.2s all;
    }

    .backHome:hover {
        background-color: #fff;
        border-color: #189cf0;
        color: #189cf0;
    }

    @media only screen and (max-width: 480px) {
        .notfound .notfound-500 h1 {
            font-size: 162px;
        }

        .notfound h2 {
            font-size: 26px;
        }
    }

</style>

<body class="container">
    <div id="notfound">
        <div class="col-12 d-flex justify-content-center pt-4">
            <img src="{{ asset('images/dotappslogo.png') }}" alt="Dotapps" width="200" height="130" class="">
        </div>
        <div class="notfound">
            <div class="notfound-500">
                <h1>500</h1>
            </div>
            <h2>Something Went Wrong</h2>
            <p> 
                please contact technical support department via. 
                {{-- <a href="https://support.dotapps.net/">Dotapps Ticket System</a> --}}
            </p>
            <a class="backHome" href="{{ route('admin.home') }}">Back To Homepage</a>
        </div>
    </div>

</body>

</html>
