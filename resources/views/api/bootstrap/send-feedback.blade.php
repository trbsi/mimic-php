@if($firstTimeCreation)
<style>
    .chat {
    width: 400px;
}

.bubble{
    background-color: #F2F2F2;
    border-radius: 5px;
    box-shadow: 0 0 6px #B2B2B2;
    display: inline-block;
    padding: 10px 18px;
    position: relative;
    vertical-align: top;
}

.bubble::before {
    background-color: #F2F2F2;
    content: "\00a0";
    display: block;
    height: 16px;
    position: absolute;
    top: 11px;
    transform:             rotate( 29deg ) skew( -35deg );
        -moz-transform:    rotate( 29deg ) skew( -35deg );
        -ms-transform:     rotate( 29deg ) skew( -35deg );
        -o-transform:      rotate( 29deg ) skew( -35deg );
        -webkit-transform: rotate( 29deg ) skew( -35deg );
    width:  20px;
}

.me {
    float: left;   
    margin: 5px 45px 5px 20px;         
}

.me::before {
    box-shadow: -2px 2px 2px 0 rgba( 178, 178, 178, .4 );
    left: -9px;           
}

.you {
    float: right;    
    margin: 5px 20px 5px 45px;         
}

.you::before {
    box-shadow: 2px -2px 2px 0 rgba( 178, 178, 178, .4 );
    right: -9px;    
}
.date {
    font-size: 11px;
}
.images {
    max-height: 200px;
    width: auto;
}
</style>
@endif

<div class="chat">
<div class="bubble me">
    <b>{{$user->username}}</b><br>
    <span class="date">{{date('d.m.Y')}}</span>
    <br>
    {{$body}}
    @if($filePath)
        @foreach($filePath as $path)
        <br>
        <a href="{{asset($path)}}" target="_blank" ><img src="{{asset($path)}}" class="images"></a>
        @endforeach
    @endif
</div>
</div>