<div class="chat">
<div class="bubble me">
    <b>{{$user->username}}</b><br>
    <span class="date">{{date('d.m.Y')}}</span>
    <br>
    {!! nl2br(e($body)) !!}
    @if($filePath)
        @foreach($filePath as $path)
        <br>
        <a href="{{asset($path)}}" target="_blank" ><img src="{{asset($path)}}" class="images"></a>
        @endforeach
    @endif
</div>
</div>