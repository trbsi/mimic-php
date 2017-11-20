<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invest in Mimic</title>
    <link rel="apple-touch-icon" sizes="57x57" href="img/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="img/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="img/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="img/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="img/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="img/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="img/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
    <link rel="manifest" href="img/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="img/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
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
            background: url('ico-files/img/investbg.jpg') center no-repeat;
            background-size: cover;
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
            background: url('ico-files/img/investbg.jpg') center no-repeat;
            background-size: cover;
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
        
        form label input {
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
        
        .spinner {
            margin: 0 auto;
            width: 50px;
            height: 40px;
            text-align: center;
            font-size: 10px;
        }
        
        .spinner > div {
            background-color: #333;
            height: 100%;
            width: 6px;
            display: inline-block;
            -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
            animation: sk-stretchdelay 1.2s infinite ease-in-out;
        }
        
        .spinner .rect2 {
            -webkit-animation-delay: -1.1s;
            animation-delay: -1.1s;
        }
        
        .spinner .rect3 {
            -webkit-animation-delay: -1.0s;
            animation-delay: -1.0s;
        }
        
        .spinner .rect4 {
            -webkit-animation-delay: -0.9s;
            animation-delay: -0.9s;
        }
        
        .spinner .rect5 {
            -webkit-animation-delay: -0.8s;
            animation-delay: -0.8s;
        }
        
        @-webkit-keyframes sk-stretchdelay {
            0%,
            40%,
            100% {
                -webkit-transform: scaleY(0.4)
            }
            20% {
                -webkit-transform: scaleY(1.0)
            }
        }
        
        @keyframes sk-stretchdelay {
            0%,
            40%,
            100% {
                transform: scaleY(0.4);
                -webkit-transform: scaleY(0.4);
            }
            20% {
                transform: scaleY(1.0);
                -webkit-transform: scaleY(1.0);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>
               <a href="<?=route('ico')?>" style="">
               <img src="ico-files/img/mimic-eth.png" style="max-height: 30px; width: auto;"> 
               <span style="font-size: 0.8em; color: var(--main-color); text-decoration: underline">ICO Info</span>
               <img src="ico-files/img/mimic-eth.png" style="max-height: 30px; width: auto;">
               </a>
            </h1>
        </header>
        
        <h1 class="text-center">Invest now</h1>
        <form id="invest-form">
            <label class="col-one-half">
                <span class="label-text">First Name</span>
                <input type="text" id="first_name" required="">
            </label>
            <label class="col-one-half">
                <span class="label-text">Last Name</span>
                <input type="text" id="last_name" required="">
            </label>
            <label>
                <span class="label-text">Email</span>
                <input type="email" id="email" required="">
            </label>
            <label>
                <span class="label-text">MimiCoins</span>
                <input type="number" id="mimicoins_bought" required="" min="15" onkeyup="calculateInvestment()">
            </label>
            <label>
                <span class="label-text">Your account number</span>
                <input type="text" id="investor_account_number" required="" onkeyup="calculateInvestment()">
            </label>
            <label>
                <span class="label-text">Affiliate code</span>
                <input 
                type="text" 
                id="affiliate_code" 
                onkeyup="calculateInvestment()" 
                value="@if ($affiliate) {{$affiliate}} @endif" 
                @if($affiliate) readonly @endif>
            </label>
            <div id="calculate_investment" style="display: none; text-align: center">
                Calculating investment...
                <div class="spinner">
                    <div class="rect1"></div>
                    <div class="rect2"></div>
                    <div class="rect3"></div>
                    <div class="rect4"></div>
                    <div class="rect5"></div>
                </div>
            </div>

            <div id="processing_investment" style="display: none; text-align: center">
                We're processing your investment...
                <div class="spinner">
                    <div class="rect1"></div>
                    <div class="rect2"></div>
                    <div class="rect3"></div>
                    <div class="rect4"></div>
                    <div class="rect5"></div>
                </div>
            </div>

            <div class="alert alert-success fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" onclick="fadeOutAlert(this)">×</a>
                <div class="content"></div>
            </div>

            <div class="alert alert-error fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" onclick="fadeOutAlert(this)">×</a>
                <div class="content"></div>
            </div>

            <div class="alert alert-info fade in" style="margin-top: 20px;">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" onclick="fadeOutAlert(this)">×</a>
                <div class="content"></div>
            </div>
            <div class="text-center">
                <button class="submit" id="invest-btn">Invest</button>
            </div>
        </form>
        
    </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
    var calculateInvestmentTimer;
    var minMimiCoins = <?=env('ICO_MIN_MIMCOINS')?>;
    var affiliateUrl = '<?=route('ico-invest')?>?affiliate='

    function calculateInvestment() {
        clearTimeout(calculateInvestmentTimer);
        var data = {
            mimicoins_bought: $("#mimicoins_bought").val(),
            affiliate_code: $("#affiliate_code").val(),
            investor_account_number: $("#investor_account_number").val(),
        };

        if (data['mimicoins_bought'] != '' && data['mimicoins_bought'] >= minMimiCoins) {
            $("#calculate_investment").fadeIn();
            calculateInvestmentTimer = setTimeout(function() {
                $.ajax({
                    url: '<?=app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('calculate-investment')?>',
                    type: "POST",
                    data: data,
                    success: function(data, textStatus, jqXHR) {
                        $(".alert").hide();
                        $("#calculate_investment").hide();

                        if(data.amount_to_send_to_other_account == 0) {
                            var msg = "You'll get <b>"+data.amount_to_send_to_investor+"</b> MimiCoins<br>You need to pay <b>"+data.number_of_eth_to_pay+"</b> ETH"; 
                            showInfo(msg, true);
                        } else {
                            var msg = "You'll get <b>"+data.amount_to_send_to_investor+"</b> MimiCoins<br>Person who referred you will get <b>"+data.amount_to_send_to_other_account+"</b> MimiCoins<br>You need to pay <b>"+data.number_of_eth_to_pay+"</b> ETH"; 
                            showInfo(msg, true);
                        }
                        
                    },
                    error: function(error) {
                        $(".alert").hide();
                        $("#calculate_investment").hide();
                        showError(error.responseJSON.error.message);

                    }
                });
            }, 1500);

        }

    }

    function showError(msg, forever = false) {
        var error_div = $(".alert-error");
        var content_div = $(".alert-error .content");
        content_div.html(msg)
        error_div.fadeIn();

        if(forever === false) {
            setTimeout(function() {
                error_div.fadeOut();
            }, 5000);
        }
    }

    function showInfo(msg, forever = false) {
        var info_div = $(".alert-info");
        var content_div = $(".alert-info .content");
        content_div.html(msg)
        info_div.fadeIn();

        if(forever === false) {
            setTimeout(function() {
                info_div.fadeOut();
            }, 5000);
        }
    }

    function showSuccess(msg, forever = false) {
        var success_div = $(".alert-success");
        var content_div = $(".alert-success .content");
        content_div.html(msg);
        success_div.fadeIn();

        if(forever === false) {
            setTimeout(function() {
                success_div.fadeOut();
            }, 5000);
        }
        
    }

    function fadeOutAlert(el)
    {
        $(el).parent().fadeOut();
    }

    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    $("#invest-form").submit(function(e) {

        e.preventDefault(); //STOP default action

        var data = {
            first_name: $("#first_name").val(),
            last_name: $("#last_name").val(),
            email: $("#email").val(),
            mimicoins_bought: $("#mimicoins_bought").val(),
            investor_account_number: $("#investor_account_number").val(),
            affiliate_code: $("#affiliate_code").val(),
        };

        //validate
        if (data['first_name'] == '' || data['last_name'] == '' || data['email'] == '' || data['mimicoins_bought'] == '' || data['investor_account_number'] == '') {
            showError("Please fill all fields!");
            return;
        }

        if (data['mimicoins_bought'] < minMimiCoins) {
            showError("Miminum amount of MimiCoins you're able to buy is "+minMimiCoins+"!");
            return;
        }

        if (!validateEmail(data['email'])) {
            showError("Email is not valid!");
            return;
        }

        $("#processing_investment").fadeIn();
        $("#invest-btn").prop('disabled', true);

        $.ajax({
            url: '<?=app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('invest-in-mimic')?>',
            type: "POST",
            data: data,
            success: function(data, textStatus, jqXHR) {
                $(".alert").hide();
                $("#processing_investment").hide();
                $("#invest-btn").prop('disabled', false);

                var msg = "Dear "+data.investment.first_name+" "+data.investment.last_name+", thank you for your investment. We won't let you down!<br><br>"+
                    "This is your affiliate number: <b>"+data.affiliate.affiliate_code+"</b><br>"+
                    "This is your affiliate url: <a href='"+affiliateUrl+data.affiliate.affiliate_code+"'>"+affiliateUrl+data.affiliate.affiliate_code+"</a><br>"+
                    "Refer other investors and get extra MimiCoins.";
                showSuccess(msg, true);
            },
            error: function(error) {
                $(".alert").hide();
                $("#processing_investment").hide();
                $("#invest-btn").prop('disabled', false);
                showError(error.responseJSON.error.message);
            }
        });
    });
</script>

</html>