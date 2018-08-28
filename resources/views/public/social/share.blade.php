<!DOCTYPE html>
<html>

<head>
    @include('templates.core.parts.headers.meta')
    @include('public.social.meta-tags')
    <link rel="stylesheet" type="text/css" href="/css/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="/css/slick/slick-theme.css">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/social/share.css">
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    @include('templates.core.parts.headers.favicon')
</head>

<body>
    <header>
        <a href="/" style="color: white">
            <img src="/img/logo.png" class="img-responsive" style="max-width: 35px; float: left;margin-right: 20px; padding-top: 10px;">
            <span id="logo">mimic</span>
        </a>
    </header>
    <div class="container-fluid" id="content">
        <div class="row" id="container-left">
            <div class="cols-xs-12 col-sm-12 col-md-3 Aligner height-100 text-center hidden-xs">
                <div class="Aligner-center get-the-app">
                    Share this Mimic further ;)
                    <div class="sharethis-inline-share-buttons" style="margin-top: 25px"></div>
                </div>
            </div>
            <div class="cols-xs-12 col-sm-12 col-md-6" style="padding: 15px 0 15px 0;">
                <div class="slider">
                    @if($mimic->mimic_type === $mimic::TYPE_PHOTO_STRING)
                    <div class="text-center">
                        <div class="blur-bg blur-bg-top mimic-img-video" style="background-image: url('{{$mimic->file_url}}');"></div>
                        <div class="user-info-top">
                            <div class="image-rounded">
                                <img src="{{$mimic->user->profile_picture}}">
                            </div>
                            <div class="username">{{$mimic->user->username}}</div>
                        </div>
                        <img src="{{$mimic->file_url}}" class="mimic-img-video img-responsive">
                    </div>
                    @else

                    <div class="text-center">
                        <div class="blur-bg blur-bg-top mimic-img-video" style="background-image: url('{{$mimic->video_thumb_url}}');"></div>
                        <div class="user-info-top">
                            <div class="image-rounded">
                                <img src="{{$mimic->user->profile_picture}}">
                            </div>
                            <div class="username">{{$mimic->user->username}}</div>
                        </div>
                        <video controls class="mimic-img-video">
                            <source src="{{$mimic->file_url}}" type="video/mp4"> Your browser does not support the video tag.
                        </video>
                    </div>
                    @endif
                </div>
                <div class="slider">
                    @if(false === $mimic->responses->isEmpty())
                    @foreach ($mimic->responses as $response) 
                        @if($response->mimic_type === $mimic::TYPE_PHOTO_STRING)
                        <div class="text-center">
                            <div class="blur-bg blur-bg-bottom mimic-img-video" style="background-image: url('{{$response->file_url}}');"></div>
                            <img src="{{$response->file_url}}" class="mimic-img-video img-responsive">
                            <div class="user-info-bottom">
                                <div class="image-rounded">
                                    <img src="{{$response->user->profile_picture}}">
                                </div>
                                <div class="username">{{$response->user->username}}</div>
                            </div>
                        </div>
                        @else
                        <div class="text-center">
                            <div class="blur-bg blur-bg-bottom mimic-img-video" style="background-image: url('{{$response->video_thumb_url}}');"></div>
                            <video controls class="mimic-img-video">
                                <source src="{{$response->file_url}}" type="video/mp4"> Your browser does not support the video tag.
                            </video>
                            <div class="user-info-bottom">
                                <div class="image-rounded">
                                    <img src="{{$response->user->profile_picture}}"> 
                                </div>
                                <div class="username">{{$response->user->username}}</div>
                            </div>
                        </div>
                        @endif 
                    @endforeach
                    @else
                    <div>
                        <div class="mimic-img-video no-mimics Aligner">
                            <div class="Aligner-center">
                                <img src="/img/add-new-mimic.png">
                                <span style="font-weight:bold; color: #f9c21e">No Mimics yet!</span>
                                <br><br>

                                Be first! Post a Mimic and get upvotes!
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="cols-xs-12 col-sm-12 col-md-3 Aligner height-100 text-center" id="container-right">
                <div class="Aligner-center">
                    <span class="get-the-app">Download now!</span>
                    <br>
                    <br>
                    <a href="{{env('IOS_STORE_LINK')}}" target="_blank">
                        <img src="/img/app-store.png" style="max-width:150px;">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 text-center">
                    <ul class="social-icons text-center">
                        @foreach($social as $socialName => $socialUrl)
                        <li class="wow animated fadeInLeft {{$socialName}}"><a href="{{$socialUrl}}" target="_blank"><i class="fa fa-{{$socialName}}"></i></a>
                        </li>
                        @endforeach
                        <li class="wow animated fadeInRight twitter"><a href="mailto:<?=config('app.official_email')?>"><i class="fa fa-envelope"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row"><div class="col-md-12">&nbsp;</div></div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <a href="/" style="color: white">Mimic</a> &copy; <?= date('Y') ?>
                </div>
            </div>
        </div>
    </footer>

    <script src="/js/jquery-2.0.0.min.js" type="text/javascript"></script>
    <script src="/js/slick/slick.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="/js/social/share.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="//platform-api.sharethis.com/js/sharethis.js#property=5a1db9b363750b0012e6bb1d&product=inline-share-buttons"></script>
</body>

</html>