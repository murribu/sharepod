@extends('spark::layouts.app')

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.4.6/mousetrap.min.js"></script>
    <script src="/js/lodash.custom.min.js"></script>
@endsection

@section('content')
<help :user="user" inline-template>
    <div class="container-fluid">
        <div class="row">
            <!-- Tabs -->
            <div class="col-md-4">
                <div class="panel panel-default panel-flush">
                    <div class="panel-heading">
                        Help
                    </div>

                    <div class="panel-body">
                        <div class="help-tabs">
                            <ul class="nav left-stacked-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#register-my-feed" aria-controls="register-my-feed" role="tab" data-toggle="tab">
                                        <i class="fa fa-fw fa-btn fa-registered"></i>Register My Feed
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#find-a-podcatcher" aria-controls="find-a-podcatcher" role="tab" data-toggle="tab">
                                        <i class="fa fa-fw fa-btn fa-search"></i>Find a podcatcher
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Panels -->
            <div class="col-md-8">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="register-my-feed">
                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Register My Feed
                                </div>
                                <div class="panel-body">
                                    <ol>
                                        <li>
                                            <div v-if="user">
                                                <a :href="'{{env('APP_URL')}}/feed/' + user.slug" @click.prevent="copyFeed('{{env('APP_URL')}}/feed/' + user.slug, 'copy-feed')" id="copy-feed">Click here to copy your Feed URL</a>
                                                <input type="text" id="copy-feed-fallback" class="fallback" :value="'{{env('APP_URL')}}/feed/' + user.slug" v-tooltip title="Copy this text" />
                                            </div>
                                            <div v-if="!user">
                                                Once you log in, you'll see a link here - which you will copy.
                                            </div>
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
                                        <li>Paste the link that you copied in Step 1 into your podcatcher's RSS Feed URL field.</li>
                                        <li>Then your approved recommendations will show up in your podcatcher!</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="find-a-podcatcher">
                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Find a Podcatcher
                                </div>
                                <div class="panel-body">
                                    These are applications that make it easy to listen to podcasts.
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</help>
@endsection