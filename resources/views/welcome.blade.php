<!DOCTYPE html>
<html lang="en">
<head>
    @include('templates.core.parts.headers.meta')
    <title><?= config("app.name") ?> - <?= config("app.slogan") ?></title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/owl.theme.css">
    <link rel="stylesheet" href="css/nivo-lightbox/nivo-lightbox.css">
    <link rel="stylesheet" href="css/nivo-lightbox/nivo-lightbox-theme.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/style.css">
    <meta property="og:image" content="<?= env('APP_URL') ?>/img/facebook_share_img.jpg"/>
    <meta property="og:title" content="<?= config("app.name") ?> - <?=config('app.slogan')?>"/>
    <meta property="og:url" content="<?= env('APP_URL') ?>"/>
    <meta property="og:site_name" content="<?= config("app.name") ?>"/>
    <meta property="og:description" content="<?=config('app.app_description')?>"/>
    @include('templates.core.parts.headers.favicon')

    <script src="js/modernizr.custom.js"></script>
    <style type="text/css">
        #ico {
            background: linear-gradient(180deg, #f9c21f, #FFDF7C);
            color: white;
            padding: 20px 0px;
            /*position: relative;*/
        }
    </style>

</head>

<body>
<!-- HHHHHHHHHHHHHHHHHH        Preloader          HHHHHHHHHHHHHHHH -->
<!-- <div id="preloader"></div> -->
<!-- HHHHHHHHHHHHHHHHHH        Header          HHHHHHHHHHHHHHHH -->
<section id="header" class="header">
    <div class="top-bar">
        <div class="container">
            <div class="navigation" id="navigation-scroll">
                <div class="row">
                    <div class="col-md-11 col-xs-10">
                        <a href="/">
                            <img src="img/logo.png" class="img-responsive"
                                 style="max-width: 35px; float: left;margin-right: 20px; padding-top: 10px;">
                            <span id="logo"><?= strtolower(config("app.name")) ?></span>
                        </a>
                    </div>
                    <div class="col-md-1 col-xs-2">
                        <p class="nav-button">
                            <button id="trigger-overlay" type="button">
                                <i class="fa fa-bars"></i>
                            </button>
                        </p>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.navigation -->
        </div>
        <!--/.container-->
    </div>
    <!--/.top-bar-->

    <div class="container">
        <div class="starting">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <img src="img/phone.png" alt="Mimic" class="img-responsive wow flipInY animated animated">
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="banner-text">
                        <h2 class="animation-box wow bounceIn animated"><strong
                                    class="strong"><?= config("app.name") ?></strong><br>
                            <span style="font-size: 0.5em;"><?= config("app.slogan") ?></span>
                        </h2>

                        <p>
                              Want to challenge others to copy your moves? Have a funny response to a post? Mimic encourages top half, bottom half photo and video posts and replies. Swipe through, upvote and add your own.
                        </p>
                        <?php /*<a href="<?= env("GOOGLE_PLAY_LINK") ?>" target="_blank"
                           class="btn btn-download wow animated fadeInLeft">
                            <i class="fa fa-android pull-left"></i>
                            <strong>Get it on</strong>
                            <br/>Google Play </a>*/ ?>
                        <a href="<?= env("IOS_STORE_LINK") ?>" target="_blank"
                           class="btn btn-download wow animated fadeInRight">
                            <i class="fa fa-apple pull-left"></i>
                            <strong>Get it on</strong>
                            <br/>App store </a>
                    </div>
                    <!-- /.banner-text -->
                </div>
            </div>
        </div>
        <!-- /.starting -->
    </div>
    <!-- /.container -->
</section>

<!-- /#header -->

<!-- HHHHHHHHHHHHHHHHHH        Video          HHHHHHHHHHHHHHHH -->
<?php /*
<div id="video" class="wrapper">
    <div class="container">
        <h2 class=""><?= config("app.name") ?> Video</h2>

        <div class="virticle-line"></div>
        <div class="circle"></div>
        <div class='embed-container'>
            <iframe src='https://www.youtube.com/embed/7n_UVpR3hJE' frameborder='0' allowfullscreen></iframe>
        </div>
    </div>
    <!-- /.container -->
</div>
<!-- /#video -->
*/?>
<!-- HHHHHHHHHHHHHHHHHH      Screenshots    HHHHHHHHHHHHHHHH -->

<section id="gallery" class="wrapper">
    <div class="container">
        <h2 class="animation-box wow bounceIn animated">SCREENSHOTS</h2>

        <div class="virticle-line"></div>
        <div class="circle"></div>
        <div class="row">
            <div class="col-xs-12">
                <div id="screenshots" class="owl-carousel owl-theme">
                    @for($i=1;$i<=10;$i++)
                    <a href="img/screenshots/{{$i}}.png" class="item wow flipInY animated animated" data-lightbox-gallery="screenshots">
                        <img src="img/screenshots/{{$i}}.png" class="img_res wow animated zoomIn" alt="">
                    </a>
                    @endfor
                </div>
                <div class="customNavigation row">
                    <a class="btn prev gallery-nav wow animated bounceInLeft"><i class="fa fa-chevron-left"></i></a>
                    <a class="btn next gallery-nav wow animated bounceInRight"><i class="fa fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HHHHHHHHHHHHHHHHHH        Footer          HHHHHHHHHHHHHHHH -->

<section id="footer" class="wrapper">
    <div class="container text-center">
        <div class="footer-logo">
            <h1 class="text-center animation-box wow bounceIn animated"><?= config("app.name") ?></h1>
        </div>
        <ul class="social-icons text-center">
            @foreach($socialAccounts as $socialName => $socialUrl)
            <li class="wow animated fadeInLeft {{$socialName}}"><a href="{{$socialUrl}}" target="_blank"><i class="fa fa-{{$socialName}}"></i></a>
            </li>
            @endforeach
            <li class="wow animated fadeInRight twitter"><a href="mailto:<?=config('app.official_email')?>"><i class="fa fa-envelope"></i></a>
            </li>
        </ul>
    </div>
    <!-- container -->
</section>

<!-- HHHHHHHHHHHHHHHHHH        Open/Close          HHHHHHHHHHHHHHHH -->
<div class="overlay overlay-hugeinc">
    <button type="button" class="overlay-close">Close</button>
    <nav>
        <ul>
            <li class="hideit"><a href="#header">Home</a></li>
            <li class="hideit"><a href="#video">Video</a></li>
            <li class="hideit"><a href="#gallery">Screenshots</a></li>
            <li class="hideit"><a href="/blog">Blog</a></li>
            <li class="hideit"><a href="<?= url('legal') ?>">Legal</a></li>
        </ul>
    </nav>
</div>
<script src="js/jquery-1.11.2.min.js"></script>
<script src="js/wow.min.js"></script>
<script src="js/owl-carousel.js"></script>
<script src="js/nivo-lightbox.min.js"></script>
<script src="js/smoothscroll.js"></script>
<!--<script src="js/jquery.ajaxchimp.min.js"></script>-->
<script src="js/bootstrap.min.js"></script>
<script src="js/classie.js"></script>
<script src="js/script.js"></script>
<script>
    // new WOW().init();
</script>
<script>
    $(document).ready(function () {
        $(".hideit").click(function () {
            $(".overlay").hide();
        });
        $("#trigger-overlay").click(function () {
            $(".overlay").show();
        });
    });
</script>
<script>
    $(document).ready(function () {

        var kawa = $('.top-bar');
        var back = $('#back-to-top');

        function scroll() {
            if ($(window).scrollTop() > 700) {
                kawa.addClass('fixed');
                back.addClass('show-top');

            } else {
                kawa.removeClass('fixed');
                back.removeClass('show-top');
            }
        }

        document.onscroll = scroll;
    });
</script>
<!--HHHHHHHHHHHH        Smooth Scrooling     HHHHHHHHHHHHHHHH-->
<script>
    $(function () {
        $('a[href*=#]:not([href=#])').click(function () {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top
                    }, 1000);
                    return false;
                }
            }
        });
    });
</script>
</body>
</html>