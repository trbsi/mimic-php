<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="apple-touch-icon" sizes="57x57" href="/img/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/img/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/img/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/img/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/img/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/img/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/img/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/img/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/img/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/img/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="/img/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/img/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:image" content="<?=env('APP_URL')?>/img/facebook_share_img.jpg"/>
    <meta property="og:title" content="Invest in Mimic & MimiCoin!"/>
    <meta property="og:url" content="<?=route('ico-invest')?>"/>
    <meta property="og:site_name" content="Mimic"/>
    <meta property="og:description" content="Mimic - Challenge people to copy your moves! Want to challenge others to copy your moves or your selfies? Mimic encourages top half, bottom half photo and video posts and replies. Swipe through, upvote and add your own."/>

    <style>
        @import url(https://fonts.googleapis.com/css?family=Cookie|Raleway:300,700,400);
        :root {
            --main-color: #E9B106;
        }
        
        * {
            box-sizing: border-box;
            font-size: 1em;
            margin: 0;
            padding: 0;
        }
        
        body {
            background: url('/ico-files/img/investbg.jpg') center no-repeat;
            background-size: cover;
            background-attachment: fixed;
            color: #333;
            font-size: 18px;
            font-family: 'Raleway', sans-serif;
        }
        
        .container {
            border-radius: 0.5em;
            box-shadow: 0 0 1em 0 rgba(51, 51, 51, 0.25);
            display: block;
            max-width: 480px;
            overflow: hidden;
            -webkit-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            padding: 2em;
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 1;
            width: 98%;
        }
        
        .container:before {
            background: url('/ico-files/img/investbg.jpg') center no-repeat;
            background-size: cover;
            background-attachment: fixed;
            content: '';
            -webkit-filter: blur(10px);
            filter: blur(10px);
            height: 100vh;
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: -1;
            -webkit-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            width: 100vw;
        }
        
        .container:after {
            background: rgba(255, 255, 255, 0.6);
            content: '';
            display: block;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: -1;
            width: 100%;
        }
        
        form button.submit {
            background: var(--main-color);
            border: 1px solid #333;
            line-height: 1em;
            padding: 1em 1.5em;
            -webkit-transition: all 0.25s;
            transition: all 0.25s;
            border-radius: 5px;
            width: 100%;
        }
        
        form button:hover,
        form button:focus,
        form button:active,
        form button.loading {
            background: #333;
            color: #fff;
            outline: none;
        }
        
        form button.success {
            background: #27ae60;
            border-color: #27ae60;
            color: #fff;
        }
        
        @-webkit-keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        form button span.loading-spinner {
            -webkit-animation: spin 0.5s linear infinite;
            animation: spin 0.5s linear infinite;
            border: 2px solid #fff;
            border-top-color: transparent;
            border-radius: 50%;
            display: inline-block;
            height: 1em;
            width: 1em;
        }
        
        form label {
            border-bottom: 1px solid #333;
            display: block;
            font-size: 1.25em;
            margin-bottom: 0.5em;
            -webkit-transition: all 0.25s;
            transition: all 0.25s;
        }
        
        form label.col-one-half {
            float: left;
            width: 50%;
        }
        
        form label.col-one-half:nth-of-type(even) {
            border-left: 1px solid #333;
            padding-left: 0.25em;
        }
        
        form label input, form label select, form label textarea {
            background: none;
            border: none;
            line-height: 1em;
            font-weight: 300;
            padding: 0.125em 0.25em;
            width: 100%;
        }
        
        form label input:focus {
            outline: none;
        }
        
        form label input:-webkit-autofill {
            background-color: transparent !important;
        }
        
        form label span.label-text {
            display: block;
            font-size: 0.5em;
            font-weight: bold;
            padding-left: 0.5em;
            text-transform: uppercase;
            -webkit-transition: all 0.25s;
            transition: all 0.25s;
        }
        
        form label.checkbox {
            border-bottom: 0;
            text-align: center;
        }
        
        form label.checkbox input {
            display: none;
        }
        
        form label.checkbox span {
            font-size: 0.5em;
        }
        
        form label.checkbox span:before {
            content: '\e157';
            display: inline-block;
            font-family: 'Glyphicons Halflings';
            font-size: 1.125em;
            padding-right: 0.25em;
            position: relative;
            top: 1px;
        }
        
        form label.checkbox input:checked + span:before {
            content: '\e067';
        }
        
        form label.invalid {
            border-color: #c0392b !important;
        }
        
        form label.invalid span.label-text {
            color: #c0392b;
        }
        
        form label.password {
            position: relative;
        }
        
        form label.password button.toggle-visibility {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.75em;
            line-height: 1em;
            position: absolute;
            top: 50%;
            right: 0.5em;
            text-align: center;
            -webkit-transform: translateY(-50%);
            -ms-transform: translateY(-50%);
            transform: translateY(-50%);
            -webkit-transition: all 0.25s;
            transition: all 0.25s;
        }
        
        form label.password button.toggle-visibility:hover,
        form label.password button.toggle-visibility:focus,
        form label.password button.toggle-visibility:active {
            color: #000;
            outline: none;
        }
        
        form label.password button.toggle-visibility span {
            vertical-align: middle;
        }
        
        h1 {
            font-size: 3em;
            text-align: center;
            font-family: 'Cookie', cursive;
        }
        
        h1 img {
            height: auto;
            margin: 0 auto;
            max-width: 240px;
            width: 100%;
        }
        
        html {
            font-size: 18px;
            height: 100%;
        }
        
        .text-center {
            text-align: center;
        }
        
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        
        .alert-error {
            color: #b94a48;
            background-color: #f2dede;
            border-color: #eed3d7;
        }
        
        .alert-info {
            color: #3a87ad;
            background-color: #d9edf7;
            border-color: #bce8f1;
        }
        
        .alert {
            display: none;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        
        .fade {
            opacity: 0;
            -webkit-transition: opacity .15s linear;
            -o-transition: opacity .15s linear;
            transition: opacity .15s linear;
        }
        
        .close {
            float: right;
            font-size: 21px;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            filter: alpha(opacity=20);
            opacity: .2;
        }
        
        a {
            color: #337ab7;
            text-decoration: none;
        }
        
        a {
            background-color: transparent;
        }
        
        .fade.in {
            opacity: 1;
        }
        
    </style>
    @yield('css')
</head>

<body>
    <div class="container">
        @yield('content')
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://unpkg.com/sweetalert2@7.0.7/dist/sweetalert2.all.js"></script>
    <script type='text/javascript' src='//platform-api.sharethis.com/js/sharethis.js#property=5a1db9b363750b0012e6bb1d&product=inline-share-buttons' async='async'></script>
    <script type="text/javascript">
        (function(p,u,s,h){
            p._pcq=p._pcq||[];
            p._pcq.push(['_currentTime',Date.now()]);
            s=u.createElement('script');
            s.type='text/javascript';
            s.async=true;
            s.src='https://cdn.pushcrew.com/js/d35695207738284b714c96cc03272aee.js';
            h=u.getElementsByTagName('script')[0];
            h.parentNode.insertBefore(s,h);
        })(window,document);
    </script>
    @yield('javascript')

</body>
</html>