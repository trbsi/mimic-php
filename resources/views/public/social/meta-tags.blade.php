<!-- Place this data between the <head> tags of your website -->
<title>{{$mimic->user->username}}'s Mimic</title>
<meta name="description" content="{{$mimic->user->username}} shared a Mimic. Check it out!" />

<!-- Schema.org markup for Google+ -->
<meta itemprop="name" content="{{$mimic->user->username}}'s Mimic">
<meta itemprop="description" content="{{__('web/share.metatag_description')}}">
<meta itemprop="image" content="{{$socialImageUrl}}">

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@app_mimic">
<meta name="twitter:title" content="{{$mimic->user->username}}'s Mimic">
<meta name="twitter:description" content="{{__('web/share.metatag_description')}}">
<meta name="twitter:image" content="{{$socialImageUrl}}">

<!-- Open Graph data -->
<meta property="og:title" content="{{$mimic->user->username}}'s Mimic" />
<meta property="og:url" content="{{route('share.mimic', ['id' => $mimic->id])}}" />
<meta property="og:image" content="{{$socialImageUrl}}" />
<meta property="og:description" content="{{__('web/share.metatag_description')}}" />
<meta property="og:site_name" content="{{config('app.name')}}" />