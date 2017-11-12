<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Mimic ICO</title>
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
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="css/owl.carousel.css">
        <link rel="stylesheet" href="css/owl.theme.css">
        <link rel="stylesheet" href="css/nivo-lightbox/nivo-lightbox.css">
        <link rel="stylesheet" href="css/nivo-lightbox/nivo-lightbox-theme.css">
        <link rel="stylesheet" href="css/animate.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="ico-files/css/flipclock.css">
        <script src="js/modernizr.custom.js"></script>
        <style type="text/css">
            .vertical-align {
            display: flex;
            flex-direction: column;
            justify-content: center;
            }
            @media (max-width: 768px) {
            #countdowner {
            zoom: 0.5;
            -moz-transform: scale(0.5)
            }
            }
            @media (min-width: 992px) {
            #countdowner {
            zoom: 0.7;
            -moz-transform: scale(0.7)
            }
            }
            @media (min-width: 1200px) {
            #countdowner {
            zoom: 1;
            -moz-transform: scale(1)
            }
            }
            #countdowner-container {
            text-align: center;
            }
            #countdowner {
            display: inline-block;
            width: auto;
            }
            .overlay ul li {
            height: auto;
            }
            .overlay nav  {
            font-size: 34px;
            }
        </style>
    </head>
    <body>
        <a href="#header" id="back-to-top" class="top"><i class="fa fa-chevron-up"></i></a>
        <!-- HHHHHHHHHHHHHHHHHH        Preloader          HHHHHHHHHHHHHHHH -->
        <!-- <div id="preloader"></div> -->
        <!-- HHHHHHHHHHHHHHHHHH        Header          HHHHHHHHHHHHHHHH -->
        <section id="header" class="header">
            <div class="top-bar">
                <div class="container">
                    <div class="navigation" id="navigation-scroll">
                        <div class="row">
                            <div class="col-md-11 col-xs-10">
                                <a href="<?=url('ico')?>"><span id="logo"><strong class="strong">mimicoin</a>
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
                <div class="starting vertical-align">
                    <div class="row ">
                        <div class="col-md-6">
                            <div class="banner-text">
                                <img src="ico-files/img/mimic-eth.png" alt="Mimic ICO" class="wow flipInY animated animated img-responsive" style="width: 100%; max-width: 20em; height: auto; margin: 0 auto;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="banner-text">
                                <h2 class="animation-box wow bounceIn animated">Mimic ICO</h2>
                                <p style="font-size: 2em;">
                                    MimiCoin<br>
                                    First social cryptocurrency
                                </p>
                                <a href="ico-files/mimic-white-paper.pdf" class="btn btn-download wow animated fadeInLeft">
                                <i class="fa fa-file-pdf-o pull-left"></i>
                                <strong>Mimic</strong>
                                <br/>White paper</a>
                                <a href="<?= env('IOS_STORE_LINK') ?>" target="_blank" class="btn btn-download wow animated fadeInRight">
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
        <div id="redeem_code" class="wrapper">
            <div class="container text-center">
                <h2 class="animation-box wow bounceIn animated text-center" style="color:black;">Redeem a code</h2>
                <div class="virticle-line"></div>
                <div class="circle"></div>
                <p>
                    Everytime someone invests in Mimic using your code, you'll get a piece of action, more precisely you'll get MimiCoins.
                <form class="form-inline" id="redeem_code_form" role="form">
                    <label for="account_number" style="display: block;"><i>Your account number (address) e.g. 0x8826f335cEf6222xxxxxxxxxx673ba9B144d164b</i></label>
                    <div class="form-group">
                        <input type="text" class="form-control input-lg" id="account_number" name="account_number" placeholder="Account number" required>
                    </div>
                    <button type="submit" id="redeem_code_btn" class="btn btn-warning btn-lg">Redeem a code</button>
                </form>
                </p>
                <p>
                    <div class="alert alert-success" style="display: none" id="redeem_code_success"></div>
                </p>
                <hr>

                <p id="newsletter_subscribe">
                    <h2 class="animation-box wow bounceIn animated text-center" style="color:black;">Subscribe to newsletter</h2>
                    <div class="virticle-line"></div>
                    <div class="circle"></div>

                    Don't worry, we'll only send you an email to remind you about the start of our ICO.
                    <br><br>
                    <form class="form-inline" id="newsletter_form" role="form">
                        <div class="form-group">
                            <label class="sr-only" for="exampleInputEmail2">Email address</label>
                            <input type="email" id="newsletter_email" class="form-control input-lg" id="exampleInputEmail2" placeholder="Enter email" required>
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="name">Your name</label>
                            <input type="text" id="newsletter_name" class="form-control input-lg" id="first_name" placeholder="Your name" required>
                        </div>
                        <button type="submit" id="newsletter_btn" class="btn btn-warning btn-lg">Subscribe</button>
                    </form>
                </p>
                <p>
                    <div class="alert alert-success" style="display: none" id="newsletter_success"></div>
                    <div class="alert alert-warning" style="display: none" id="newsletter_warning"></div>
                </p>
            </div>
            <!-- /.container -->
        </div>
        <!-- /#video -->
        <div id="speciality2" class="wrapper">
            <div class="container">
                <h2 class="animation-box wow bounceIn animated">What is Mimic?</h2>
                <div class="virticle-line"></div>
                <div class="circle"></div>
                <div class="row">
                    <div class="col-sm-12 wow animated fadeInLeft">
                        Mimic is the first split screen app for teenagers and students <br>
                        Mimic allows people to copy each other's moves, selfies or any other fun actions thus gaining popularity and followers
                        <br><br>
                        <a href="/"><img src="img/favicon/apple-icon-120x120.png" style="max-height: 70px"></a>
                        <h3><a href="/">TAKE A LOOK</a></h3>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container -->
        </div>
        <!-- /#speciality -->
        <!-- HHHHHHHHHHHHHHHHHH        Video          HHHHHHHHHHHHHHHH -->
        <div id="video" class="wrapper">
            <div class="container">
                <h2 class="animation-box wow bounceIn animated">Countdown</h2>
                <div class="virticle-line"></div>
                <div class="circle"></div>
                <p>
                <div id="countdowner-container">
                    <div id="countdowner" style="margin: 0 auto;"></div>
                </div>
                </p>
            </div>
            <!-- /.container -->
        </div>
        <!-- /#video -->
        <!-- HHHHHHHHHHHHHHHHHH        Speciality         HHHHHHHHHHHHHHHH -->
        <div id="speciality" class="wrapper">
            <div class="container">
                <h2 class="animation-box wow bounceIn animated">Money raised</h2>
                <div class="virticle-line"></div>
                <div class="circle"></div>
                <div class="row">
                    <div class="col-sm-4 wow animated fadeInLeft">
                        <img src="ico-files/img/mimic-eth-icon.png" style="max-height: 50px">
                        <h3>MimiCoin</h3>
                        <p>
                            <?=$investment['mimicoins']?>
                        </p>
                    </div>
                    <div class="col-sm-4 animation-box wow bounceIn animated">
                        <img src="ico-files/img/eth.png" style="max-height: 50px">
                        <h3>Ethereum</h3>
                        <p>
                            <?=$investment['investedEth']?>
                        </p>
                    </div>
                    <div class="col-sm-4 wow animated fadeInRight">
                        <img src="ico-files/img/dollar.png" style="max-height: 50px">
                        <h3>Dollar</h3>
                        <p>
                            <?=$investment['investedUsd']?>
                        </p>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container -->
        </div>
        <!-- /#speciality -->
        <!-- HHHHHHHHHHHHHHHHHH        Features         HHHHHHHHHHHHHHHH -->
        <?php /*
            <section id="features" class="wrapper features">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 wow animated fadeInLeft">
                            <img src="img/mb2.png" alt="" class="pull-right left-img">
                        </div>
                        <div class="col-md-6 col-sm-6 wow animated fadeInRight">
                            <div class="features-list">
                                <h3>FREE FEATURES</h3>
                                <p>
                                    Your project looks great on any device. Content can be easily read and a user understands freely what you wanted.
                                </p>
                                <ul class="right">
                                    <li class="li"><i class="fa fa-chevron-right"></i> Photo Filters x 15</li>
                                    <li class="li"><i class="fa fa-chevron-right"></i> Photo Frames x 3</li>
                                    <li class="li"><i class="fa fa-chevron-right"></i> Time Lapse</li>
                                    <li class="li"><i class="fa fa-chevron-right"></i> Photo Editor</li>
                                    <li class="li"><i class="fa fa-chevron-right"></i> Photo Gallery</li>
                                    <li class="li"><i class="fa fa-chevron-right"></i> Touch to Focus</li>
                                    <li class="li"><i class="fa fa-chevron-right"></i> White Balance</li>
                                    <li class="li"><i class="fa fa-chevron-right"></i> ISO Levels</li>
                                </ul>
                            </div> <!-- .features-list -->
                        </div>
                    </div> <!-- /.row -->
                    <div class="row">
                        <div class="col-md-6 col-sm-6 wow animated fadeInLeft">
                            <div class="features-list-left">
                                <h3>PREMIUM FEATURES</h3>
                                <p>
                                    Your project looks great on any device. Content can be easily read and a user understands freely what you wanted.
                                </p>
                                <ul class="left">
                                    <li class="li">Photo Filters x 15 <i class="fa fa-chevron-left"></i></li>
                                    <li class="li">Photo Frames x 3 <i class="fa fa-chevron-left"></i></li>
                                    <li class="li">Time Lapse <i class="fa fa-chevron-left"></i></li>
                                    <li class="li">Photo Editor <i class="fa fa-chevron-left"></i></li>
                                    <li class="li">Photo Gallery <i class="fa fa-chevron-left"></i></li>
                                    <li class="li">Touch to Focus <i class="fa fa-chevron-left"></i></li>
                                    <li class="li">White Balance <i class="fa fa-chevron-left"></i></li>
                                    <li class="li">ISO Levels <i class="fa fa-chevron-left"></i></li>
                                </ul>
                            </div> <!-- .features-list -->
                        </div>
                        <div class="col-md-6 col-sm-6 wow animated fadeInRight">
                            <img src="img/mb2v.png" alt="" class="pull-left right-img">
                        </div>
                    </div> <!-- /.row -->
                </div> <!-- /.container -->
            </section> <!-- /#features -->
            */?>
        <!-- HHHHHHHHHHHHHHHHHH      Screenshots    HHHHHHHHHHHHHHHH -->
        <?php /*
            <section id="gallery" class="wrapper">
                <div class="container">
                    <h2 class="animation-box wow bounceIn animated">SCREENSHOTS</h2>
                    <div class="virticle-line"></div>
                    <div class="circle"></div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div id="screenshots" class="owl-carousel owl-theme">
                              <a href="img/1.jpg" class="item wow flipInY animated animated" data-lightbox-gallery="screenshots">
                                <img src="img/1.jpg" class="img_res wow animated zoomIn" alt="">
                              </a>
                              <a href="img/2.jpg" class="item wow flipInY animated animated" data-lightbox-gallery="screenshots">
                                <img src="img/2.jpg" class="img_res wow animated zoomIn" alt="">
                              </a>
                              <a href="img/3.jpg" class="item wow flipInY animated animated" data-lightbox-gallery="screenshots">
                                <img src="img/3.jpg" class="img_res wow animated zoomIn" alt="">
                              </a>
                              <a href="img/4.jpg" class="item wow flipInY animated animated" data-lightbox-gallery="screenshots">
                                <img src="img/4.jpg" class="img_res wow animated zoomIn" alt="">
                              </a>
                              <a href="img/1.jpg" class="item wow flipInY animated animated" data-lightbox-gallery="screenshots">
                                <img src="img/1.jpg" class="img_res wow animated zoomIn" alt="">
                              </a>
                              <a href="img/2.jpg" class="item wow flipInY animated animated" data-lightbox-gallery="screenshots">
                                <img src="img/2.jpg" class="img_res wow animated zoomIn" alt="">
                              </a>
                              <a href="img/3.jpg" class="item wow flipInY animated animated" data-lightbox-gallery="screenshots">
                                <img src="img/3.jpg" class="img_res wow animated zoomIn" alt="">
                              </a>
                              <a href="img/4.jpg" class="item wow flipInY animated animated" data-lightbox-gallery="screenshots">
                                <img src="img/4.jpg" class="img_res wow animated zoomIn" alt="">
                              </a>
                              <a href="img/1.jpg" class="item wow flipInY animated animated" data-lightbox-gallery="screenshots">
                                <img src="img/1.jpg" class="img_res wow animated zoomIn" alt="">
                              </a>
                              <a href="img/2.jpg" class="item wow flipInY animated animated" data-lightbox-gallery="screenshots">
                                <img src="img/2.jpg" class="img_res wow animated zoomIn" alt="">
                              </a>
                            </div>
                             <div class="customNavigation row">
                              <a class="btn prev gallery-nav wow animated bounceInLeft"><i class="fa fa-chevron-left"></i></a> 
                              <a class="btn next gallery-nav wow animated bounceInRight"><i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            */ ?>
        <?php /*
            <!-- HHHHHHHHHHHHHHHHHHHH    Testimonial    HHHHHHHHHHHHHHHHHHHHHH -->
            <section id="testimonial" class="wrapper">
                        <div class="container">
                            <div class="row main_content">
                                <div class="col-md-8 col-md-offset-2">
                                    <div id="client-speech" class="owl-carousel owl-theme">
                                        <div class="item wow animated bounceInUp animated">
                                            <!-- <img class="img-circle img-responsive center-block test-img" src="img/test1.png" alt="text"> -->
                                            <p class="client-comment text-center">
                                               Good camera This is a good camera app with lots of features. Got it to replace the one that came with the phone, one which froze up often. It is a little slow to boot up, and that keeps it from getting five stars.
                                            </p>
                                            <div class="row text-center">
                                                <p class="client-name text-center">Alan Hausser</p>
                                            </div>
                                        </div>
                                        <div class="item wow animated bounceInUp animated">
                                            <!-- <img class="img-circle img-responsive center-block test-img" src="img/test2.png" alt="text"> -->
                                            <p class="client-comment text-center">
                                               Good camera This is a good camera app with lots of features. Got it to replace the one that came with the phone, one which froze up often. It is a little slow to boot up, and that keeps it from getting five stars.
                                            </p>
                                            <div class="row text-center">
                                                <p class="client-name text-center">Alan Hausser</p>
                                            </div>
                                        </div>
                                        <div class="item wow animated bounceInUp animated">
                                            <!-- <img class="img-circle img-responsive center-block test-img" src="img/test3.png" alt="text"> -->
                                            <p class="client-comment text-center">
                                               Good camera This is a good camera app with lots of features. Got it to replace the one that came with the phone, one which froze up often. It is a little slow to boot up, and that keeps it from getting five stars.
                                            </p>
                                            <div class="row text-center">
                                                <p class="client-name text-center">Alan Hausser</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>  
            </section>  <!-- clients -->
            */?>
        <!-- HHHHHHHHHHHHHHHHHH      Development Team      HHHHHHHHHHHHHHHH -->
        <div id="team" class="wrapper">
            <div class="container-fluid">
                <h2 class="animation-box wow bounceIn animated">Team</h2>
                <div class="virticle-line"></div>
                <div class="circle"></div>
                <div class="row">
                    <div class="col-md-2 col-sm-4 col-md-offset-3 wow animated fadeInLeft">
                        <img src="ico-files/img/dario.png" alt="team">
                        <h3>Dario Begonja</h3>
                        <p>CMO</p>
                        <div class="img-hover">
                            <ul class="social-icon text-center">
                                <li class="wow animated fadeInRight linkedin"><a href="https://www.linkedin.com/in/dario-begonja-232769108/" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 animation-box wow bounceIn animated">
                        <img src="ico-files/img/davor.png" alt="team">
                        <h3>Davor Kljajic</h3>
                        <p>CFO</p>
                        <div class="img-hover">
                            <ul class="social-icon text-center">
                                <li class="wow animated fadeInRight linkedin"><a href="https://www.linkedin.com/in/davor-kljajic-a6133586/" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 wow animated fadeInRight">
                        <img src="ico-files/img/aco.png" alt="team">
                        <h3>Aleksandar Atanackovic</h3>
                        <p>CTO</p>
                        <div class="img-hover">
                            <ul class="social-icon text-center">
                                <li class="wow animated fadeInRight linkedin"><a href="https://www.linkedin.com/in/aleksandar-atanackovic-136b7ba6/" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-md-2 col-sm-4 col-md-offset-3 wow animated fadeInLeft">
                        <img src="ico-files/img/tonimir.png" alt="team">
                        <h3>Tonimir Kišasondi</h3>
                        <p>Blockchain/Ethereum programmer</p>
                        <div class="img-hover">
                            <ul class="social-icon text-center">
                                <li class="wow animated fadeInRight linkedin"><a href="https://www.linkedin.com/in/tonimir-kisasondi-4022b56/" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 animation-box wow bounceIn animated">
                        <img src="ico-files/img/domagoj.png" alt="team">
                        <h3>Domagoj Cingula</h3>
                        <p>ICO marketer</p>
                        <div class="img-hover">
                            <ul class="social-icon text-center">
                                <li class="wow animated fadeInRight linkedin"><a href="https://www.linkedin.com/in/domagoj-cingula-b2125273/" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 wow animated fadeInRight">
                        <img src="ico-files/img/velimir.png" alt="team">
                        <h3>Velimir Baksa</h3>
                        <p>Blockchain/Ethereum programmer</p>
                        <div class="img-hover">
                            <ul class="social-icon text-center">
                                <li class="wow animated fadeInRight linkedin"><a href="https://www.linkedin.com/in/velimir-baksa-70382882/" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-md-2 col-sm-4 col-md-offset-3 wow animated fadeInLeft">
                    </div>
                    <div class="col-md-2 col-sm-4 animation-box wow bounceIn animated">
                        <img src="ico-files/img/dariot.png" alt="team">
                        <h3>Dario Trbovic</h3>
                        <p>CEO</p>
                        <div class="img-hover">
                            <ul class="social-icon text-center">
                                <li class="wow animated fadeInRight linkedin"><a href="https://www.linkedin.com/in/dariotrbovic/" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 wow animated fadeInRight">
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container -->
            <div style="clear:both;"></div>
        </div>
        <!-- /#team -->
        <!-- HHHHHHHHHHHHHHHHHH        Price Table          HHHHHHHHHHHHHHHH -->
        <?php /*
            <section id="pricing" class="wrapper">
                <div class="banner-overlay bg-color-grad"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-md-offset-2 col-md-8">
                            <ul class="pricing-table">
                                <li class="wow flipInY animated animated" style="visibility: visible;">
                                    <h3>Standard</h3>
                                    <span> $2.99 <small>per month</small> </span>
                                    <ul class="benefits-list">
                                        <li>Responsive</li>
                                        <li>Documentation</li>
                                        <li class="not">Multiplatform</li>
                                        <li class="not">Video background</li>
                                        <li class="not">Support</li>
                                    </ul>
                                    <a href="#" target="_blank" class="buy"><i class="fa fa-shopping-cart"></i></a>
                                </li>
                                <li class="gold wow flipInY animated animated" data-wow-delay="0.4s" style="visibility: visible; -webkit-animation-delay: 0.4s;">
                                    <div class="stamp"><i class="fa fa-star-o"></i>Best choice</div>
                                    <h3>Gold</h3>
                                    <span> $7.99 <small>per month</small> </span>
                                    <ul class="benefits-list">
                                        <li>Responsive</li>
                                        <li>Documentation</li>
                                        <li>Multiplatform</li>
                                        <li>Video background</li>
                                        <li>Support</li>
                                    </ul>
                                    <a href="#" target="_blank" class="buy"> <i class="fa fa-shopping-cart"></i></a>
                                </li>
                                <li class="silver wow flipInY animated animated" data-wow-delay="0.2s" style="visibility: visible; -webkit-animation-delay: 0.2s;">
                                    <h3>Sliver</h3>
                                    <span> $4.99 <small>per month</small> </span>
                                    <ul class="benefits-list">
                                        <li>Responsive</li>
                                        <li>Documentation</li>
                                        <li>Multiplatform</li>
                                        <li class="not">Video background</li>
                                        <li class="not">Support</li>
                                    </ul> 
                                    <a href="#" target="_blank" class="buy"> <i class="fa fa-shopping-cart"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div> <!-- /.row -->
                </div> <!-- /.container -->
            </section> <!-- /#pricing -->
            */?>
        <?php /*
            <!-- HHHHHHHHHHHHHHHHHH        Contact Us          HHHHHHHHHHHHHHHH -->
            <section id="contact" class="wrapper">
                <div  class="container">
                    <div class="row">
                        <div class="col-md-3 contact-item col-sm-6 col-xs-12 wow animated fadeInLeft">
                            <i class="fa fa-map-marker"></i>
                            <h3>Address</h3>
                            <p class="contact">
                                Lamabazar, 353 <br>
                                Monuntain View, USA
                            </p>
                        </div>
                        <div class="col-md-3 contact-item col-sm-6 col-xs-12 wow animated fadeInLeft">
                            <i class="fa fa-phone"></i>
                            <h3>Phone</h3>
                            <p class="contact">
                                Local: 1-200-123-hello <br>
                                Mobile: 2-800-123-hello
                            </p>
                        </div>
                        <div class="col-md-3 contact-item col-sm-6 col-xs-12 wow animated fadeInRight">
                            <i class="fa fa-print"></i>
                            <h3>Fax</h3>
                            <p class="contact">
                                Office: 2148-287-8300 <br>
                                Home: 2528-782-8200
                            </p>
                        </div>
                        <div class="col-md-3 contact-item col-sm-6 col-xs-12 wow animated fadeInRight">
                            <i class="fa fa-envelope"></i>
                            <h3>Email Address</h3>
                            <p class="contact">
                                <a href="mailto:info@themewagon.com">info@themewagon.com</a> <br>
                                <a href="www.themewagon.com">www.themewagon.com</a>
                            </p>
                        </div>
                    </div> <!-- /.row -->
                    <form class="row form wrapper">
                        <h3>Leave A Message</h3>
                        <div class="col-sm-4 col-xs-12 form-group">
                            <label class="sr-only">Name</label>
                            <input name="name" class="form-control" type="text" placeholder="First Name">
                        </div> <!-- /.form-group -->
                        <div class="col-sm-4 col-xs-12 form-group">
                            <label class="sr-only">Email</label>
                            <input name="email" class="form-control" type="email" placeholder="Email address">
                        </div> <!-- /.form-group -->
                        <div class="col-sm-4 col-xs-12 form-group">
                            <label class="sr-only">Website</label>
                            <input name="website" class="form-control" type="text" placeholder="Your website">
                        </div> <!-- /.form-group -->
                        <div class="row">
                            <div class="col-md-12 col-xs-12 form-group">
                                <label class="sr-only">Message</label>
                                <textarea class="message form-control" placeholder="Write message"></textarea>
                            </div> <!-- /.form-group -->
                            <input class="btn btn-sub" type="submit" value="Send Message">
                        </div>
                    </form> <!-- /.row -->
                </div> <!-- /.container -->
            </section> <!-- /#contact -->
            */?>
        <!-- HHHHHHHHHHHHHHHHHH        Footer          HHHHHHHHHHHHHHHH -->
        <section id="footer" class="wrapper">
            <div class="container text-center">
                <div class="footer-logo">
                    <h1 class="text-center animation-box wow bounceIn animated">Mimic</h1>
                </div>
                <ul class="social-icons text-center">
                    <li class="wow animated fadeInLeft facebook"><a href="https://www.facebook.com/HelloMimic/" target="_blank"><i class="fa fa-facebook"></i></a></li>
                    <li class="wow animated fadeInRight twitter"><a href="mailto:<?=config('app.official_email')?>"><i class="fa fa-envelope"></i></a>
                        <?php /*<li class="wow animated fadeInLeft linkedin"><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li class="wow animated fadeInRight googleplus"><a href="#"><i class="fa fa-google-plus"></i></a></li>
                            <li class="wow animated fadeInLeft github"><a href="#"><i class="fa fa-github"></i></a>
                            */?>
                </ul>
                <div class="copyright">
                    <div><?= date("Y") ?> Mimic, All Rights Reserved</div>
                </div>
            </div>
            <!-- container -->
        </section>
        <!-- HHHHHHHHHHHHHHHHHH        Open/Close          HHHHHHHHHHHHHHHH -->
        <div class="overlay overlay-hugeinc">
            <button type="button" class="overlay-close">Close</button>
            <nav>
                <ul>
                    <li class="hideit"><a href="#redeem_code">Reedem a code</a></li>
                    <li class="hideit"><a href="#newsletter_subscribe">Newsletter</a></li>
                    <li class="hideit"><a href="#speciality2">What is Mimic</a></li>
                    <li class="hideit"><a href="#video">Countdown</a></li>
                    <li class="hideit"><a href="#speciality">Money raised</a></li>
                    <li class="hideit"><a href="#team">Team</a></li>
                    <li class="hideit"><a href="#footer">Contact Us</a></li>
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
        <script src="ico-files/js/flipclock.min.js"></script>
        <script>
            new WOW().init();
            var redeem_code_url = '<?=app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('generate-affiliate-code')?>';
            var newsletter_url = '<?=app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('newsletter-subscribe')?>';
        </script>
        <script src="ico-files/js/ico.js"></script>
        <script>
            $(document).ready(function(){
                $(".hideit").click(function(){
                    $(".overlay").hide();
                });
                $("#trigger-overlay").click(function(){
                    $(".overlay").show();
                });
            });
        </script>
        <script>
            $(document).ready(function(){
            
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
            $(function() {
              $('a[href*=#]:not([href=#])').click(function() {
                if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
                  var target = $(this.hash);
                  target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
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