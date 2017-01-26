<help-find-a-podcatcher inline-template>
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Find a Podcatcher
            </div>
            <div class="panel-body">
                This are applications that allow you to listen to podcasts.
                <ul>
                    @foreach($platforms as $platform)
                        <li>{{$platform->platform}}</li>
                        <ul>
                            @foreach($platform->podcatchers as $p)
                                <li><a href="{{$p->url}}">{{$p->name}}</a></li>
                            @endforeach
                        </ul>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</help-find-a-podcatcher>
