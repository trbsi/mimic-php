@extends('ico.templates.form.ico-form')

@section('title', 'Invest in Mimic & MimiCoin')

@section('css')
    <style>
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
@stop

@section('content')
    <div>
        @if($discount)
        <span style="color:#F12A2A; font-size: 0.8em;"><h1>{{$discount['name']}} discount! {{$discount['discount_amount']}}% OFF</h1></span>
        @endif
        <header>
            <h1>
               <a href="<?=route('ico')?>" style="">
               <img src="/ico-files/img/mimic-eth.png" style="max-height: 30px; width: auto;"> 
               <span style="font-size: 0.8em; color: var(--main-color); text-decoration: underline">ICO Info</span>
               <img src="/ico-files/img/mimic-eth.png" style="max-height: 30px; width: auto;">
               </a>
            </h1>
        </header>
        
        <h1 class="text-center">Invest now!</h1>
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
                <input type="number" id="mimicoins_bought" required="" step="0.00001" onkeyup="calculateInvestment()">
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
                value="@if ($affiliate_code) {{$affiliate_code}} @endif" 
                @if($affiliate_code) readonly @endif>
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
                <div class="content" style="padding-bottom: 15px;"></div>
                <div class="sharethis-inline-share-buttons"></div>
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
@stop

@section('javascript')
    <script>
        var calculateInvestmentTimer;
        var minMimiCoins = <?=$minInvestment?>;
        var affiliateUrl = '<?=route('ico-invest')?>';
        var domain_url = '<?=env('APP_URL')?>';

        function calculateInvestment() {
            clearTimeout(calculateInvestmentTimer);

            var mimicoins_bought = $("#mimicoins_bought");
            var data = {
                mimicoins_bought: parseFloat($("#mimicoins_bought").val()).toFixed(5),
                affiliate_code: $("#affiliate_code").val(),
                investor_account_number: $("#investor_account_number").val(),
            };

            

            if (data['mimicoins_bought'] != '') {
                $("#calculate_investment").fadeIn();
                calculateInvestmentTimer = setTimeout(function() {   
                    //round mimicpoins to 5 decimals
                    if(mimicoins_bought.val()) {
                        mimicoins_bought.val(parseFloat(mimicoins_bought.val()).toFixed(5));
                    }

                    //min number of mimcoins msg
                    if(mimicoins_bought.val() < minMimiCoins) {
                        showError("Miminum amount of MimiCoins you're able to buy is "+minMimiCoins+"!");
                        $("#calculate_investment").hide();
                        return;
                    }

                    $.ajax({
                        url: '<?=app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('calculate-investment')?>',
                        type: "POST",
                        data: data,
                        success: function(data, textStatus, jqXHR) {
                            $(".alert").hide();
                            $("#calculate_investment").hide();
                            var msg = "<b>Phase "+data.phase+"</b><br>";

                            if(data.amount_to_send_to_other_account == 0 || data.amount_to_send_to_other_account == null) {
                                msg+= "You'll get <b>"+data.amount_to_send_to_investor+"</b> MimiCoins<br>You need to pay <b>"+data.number_of_eth_to_pay+"</b> ETH"; 
                                showInfo(msg, true);
                            } else {
                                msg+= "You'll get <b>"+data.amount_to_send_to_investor+"</b> MimiCoins<br>Person who referred you will get <b>"+data.amount_to_send_to_other_account+"</b> MimiCoins<br>You need to pay <b>"+data.number_of_eth_to_pay+"</b> ETH"; 
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
                }, 7000);
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
                }, 7000);
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

        function emptyForm()
        {
            $('input[type=number], input[type=text], input[type=email]').each(function(){
                $(this).val("");
            });
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
            var shareThis = $(".sharethis-inline-share-buttons");

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
                    emptyForm();
                    $(".alert").hide();
                    $("#processing_investment").hide();
                    $("#invest-btn").prop('disabled', false);

                    var msg = "Dear <b>"+data.investment.first_name+" "+data.investment.last_name+"</b>, thank you for your investment. We won't let you down!<br>Check your email for detailed report of your investment.<br><br>"+
                        "Transaction ID: <b>"+data.investment.transaction_id+"</b><br>"+
                        "This is your affiliate number: <b>"+data.affiliate.affiliate_code+"</b><br>"+
                        "This is your affiliate url: <a href='"+data.affiliate.affiliate_url+"'>"+data.affiliate.affiliate_url+"</a><br><br>"+
                        "Refer other investors and get extra MimiCoins.";

                    shareThis.attr('data-url', data.affiliate.affiliate_url);
                    shareThis.attr('data-title', 'Invest in Mimic and get extra MimiCoins :D');
                    shareThis.attr('data-image', domain_url+'/img/facebook_share_img.jpg');
                    shareThis.attr('data-description', 'This is my affiliate code for Mimic ICO. Go ahead, invest and get some extra MimiCoins :D');
                    $(".sharethis-inline-share-buttons .st-btn").css("display", "inline-block");

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
@stop