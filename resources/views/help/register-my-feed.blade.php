<help-register-my-feed inline-template>
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Register My Feed
            </div>
            <div class="panel-body">
                <ol>
                    <li>
                        @if (Auth::user())
                            <a href="/feed/{{Auth::user()->slug}}">Copy this link</a>
                        @else
                            Once you log in, you'll see a link here - which you will copy.
                        @endif
                    </li>
                    <li>
                        Find your podcatcher in this list, and follow the link for its instructions on how to add a podcast from its RSS feed.
                        <ul>
                            @foreach($podcatchers as $p)
                                <li><a href="{{$p->url_register_feed != '' ? $p->url_register_feed : $p->url}}">{{$p->name}}</a> ({{$p->platforms_joined()}})</li>
                            @endforeach
                            
                            <li><a href="https://twitter.com/{{env('TWITTER_HANDLE')}}">Contact us</a> if your podcatcher isn't listed, or if a link is out-of-date or unhelpful.</li>
                        </ul>
                    </li>
                    <li>Paste that link into your podcatcher's RSS Feed URL field.</li>
                    <li>Then your approved recommendations will show up in your podcatcher!</li>
                </ol>
            </div>
        </div>
    </div>
</help-register-my-feed>
