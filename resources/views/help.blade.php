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
                                    <a href="#what" aria-controls="what" role="tab" data-toggle="tab">
                                        <i class="fa fa-fw fa-btn fa-question"></i>What is {{env('APP_NAME')}}?
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#rss" aria-controls="rss" role="tab" data-toggle="tab">
                                        <i class="fa fa-fw fa-btn fa-rss"></i>What is an RSS feed?
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#archive" aria-controls="archive" role="tab" data-toggle="tab">
                                        <i class="fa fa-fw fa-btn fa-save"></i>What does it mean to archive an episode?
                                    </a>
                                </li>
                                <li role="presentation">
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
                    <div role="tabpanel" class="tab-pane active" id="what">
                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    What is {{env('APP_NAME')}}?
                                </div>
                                <div class="panel-body">
                                    Do you want the <a href="#" @click.prevent="showStory('short')">Short Story</a>, the <a href="#" @click.prevent="showStory('long')">Long Story</a>, or the <a href="#" @click.prevent="showStory('extra-stuff')">Extra Stuff</a>?
                                </div>
                                <div class="panel-body story" id="short-story">
                                    <h4>Short Story</h4>
                                    When somebody sends me a podcast, they text me a link to the mp3.<br><br>
                                    I'm never going to listen to that.<br><br>
                                    With {{env('APP_NAME')}}, my friend can recommend an episode to me and it will show up where I listen to my podcasts.<br><br>
                                    There's a lot more to {{env('APP_NAME')}}, but you'll have to click on the <a href="#" @click.prevent="showStory('long')">long story</a> to learn about it.
                                </div>
                                <div class="panel-body story" id="long-story">
                                    <h4>Long Story</h4>
                                    Ok, the setup can be a little laborious. Wait! Come back! It's totally worth it.<br><br>
                                    When you want to recommend a podcast, here's what you do:
                                    <ol>
                                        <li><a href="#" onclick="window.open('/auth/facebook','auth','width=500,height=450');return false;">Login with facebook</a></li>
                                        <li><a href="/shows" target="_new">Find an episode that you want to recommend</a>
                                            <ul>
                                                <li>If you don't find the show you want, you can <a href="/shows#/new">add it</a> - using the show's <a href="#/rss">RSS Feed</a></li>
                                            </ul>
                                        </li>
                                        <li>Enter your friend's email address</li>
                                        <li>You're done. Your friend will receive an email from us.</li>
                                    </ol>
                                    When you receive a recommendation, here's what you do:
                                    <ol>
                                        <li>Click on the link in your email.</li>
                                        <li>Accept the Recommendation (if you want)</li>
                                        <li>Register your custom RSS Recommendation Feed with your podcatcher.
                                            <ul>
                                                <li>This will likely be the sticky point. But you only have to do it once.</li>
                                                <li>If you need help, <a href="/help#/register-my-feed">try this page</a></li>
                                            </ul>
                                        </li>
                                        <li>Once it is registered, any recommendations that you accept will automatically show up in your podcatcher.</li>
                                        <li>You can then "approve" that friend (if you want) - and their recommendations will automatically be accepted in the future.
                                            <ul>
                                                <li>You can <a href="/connections" v-if="user">revoke this approval</a><span v-if="!user">revoke this approval</span> at any time</li>
                                                <li>This means that any time your friend recommends some episode to you, it will just show up in your podcatcher!</li>
                                            </ul>
                                        </li>
                                    </ol>
                                    Well, you've made it this far. Do you want the <a href="#" @click.prevent="showStory('extra-stuff')">extra stuff</a>?
                                </div>
                                <div class="panel-body story" id="extra-stuff-story">
                                    <h4>Extra Stuff</h4>
                                    Ok. I'm assuming you've figured out how to register your feed. And how to send and receive recommendations.<br>Cool, huh?<br><br>
                                    Here are some more cool features:
                                    <ul>
                                        <li>Playlists
                                            <ul>
                                                <li>Let's say you are frequently a guest on different podcasts.<br>You could <a href="/playlists/new" v-if="user">Create a Playlist</a><a href="/playlists" v-if="!user">create a Playlist</a> that your fans could subscribe to.<br>Whenever you're on an episode, you just add it to your playlist and your subscribers would automatically see it.</li>
                                                <li>You register a Playlist the same way you registered your Recommendation feed.</li>
                                                <li>This is a great way for listeners to find new podcasts that they might like.</li>
                                            </ul>
                                        </li>
                                        <li><span v-if="!user">Once you <a href="#" onclick="window.open('/auth/facebook','auth','width=500,height=450');return false;">login</a>, you will have a profile page</span><span v-if="user">Your <a :href="'/users/' + user.slug">Profile Page</a></span>
                                            <ul>
                                                <li>Shows and Episodes that you have liked
                                                <ul>
                                                    <li>You can use these as <em>bookmarks</em> for shows and episodes that you like</li>
                                                    <li>Your <em>likes</em> also affect the list of <em>Recently Popular Episodes</em> on the <a href="/">{{env('APP_NAME')}} home page</a></li>
                                                </ul></li>
                                                <li>Connections
                                                    <ul>
                                                        <li>This is the list of users who have recommended an episode to you</li>
                                                        <li>(Except for the folks you have blocked)</li>
                                                    </ul>
                                                </li>
                                                <li>You can also see your Recommendation List, Playlists, and more</li>
                                                <li>Profile pages are public. So if you visit your friend's page, you could discover new shows or episodes that you might like.</li>
                                            </ul>
                                        </li>
                                        <li>Archive Episodes
                                            <ul>
                                                <li>Have you ever thought about an episode that you heard years ago - and when you search for it you can't find it. Grrr. Either the podcast disappeared or they just took the episode down. Welp, you're out of luck.</li>
                                                <li>Until now! If you want to hang on to an episode, just archive it and you can download it again whenever you want!</li>
                                                <li>As you might imagine, this carries a recurring cost - and there is a limit on how much you can archive.</li>
                                                <li>For more details, see the <a href="#/archive">archiving help page</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                    <br><br>
                                    Here are some project we hope to release soon:
                                    <ul>
                                        <li>
                                            Archiving Shows
                                            <ul>
                                                <li>This will mean that whenever a new episode drops on an archived show, it gets archived to your account</li>
                                                <li>(assuming your plan allows for it and you have enough space left)</li>
                                            </ul>
                                        </li>
                                        <li>
                                            Search a show's episodes (for a word in the title or description)
                                        </li>
                                        <li>
                                            Add your own private comments - to help you remember what you liked about a certain episode
                                        </li>
                                        <li>Do you have other ideas for us? <a target="_new" href="https://twitter.com/{{env('TWITTER_HANDLE')}}">Let us know!</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="archive">
                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    What does it mean to archive an episode?
                                </div>
                                <div class="panel-body">
                                    Ok, when you request to archive an episode, here's what happens:
                                    <ol>
                                        <li>We check to see if you're signed up for a plan that allows you to archive.</li>
                                        <li>If so, we put the request in a queue to be processed later.
                                            <ul>
                                                <li>We do this because archiving can take a few minutes. And we don't want you to have to wait around for it to finish.</li>
                                            </ul>
                                        </li>
                                        <li>When that request is processed, we'll send you a notification (see that little bell in the top right corner?)
                                            <ul>
                                                <li>There is a limit to how much storage you can use.
                                                    <ul>
                                                        <li>The Free plan does not allow archiving</li>
                                                        <li>The Basic plan allows up to {{(intval(env('PLAN_BASIC_STORAGE_LIMIT'))/pow(2,30))}} GB</li>
                                                        <li>The Premium plan allows up to {{(intval(env('PLAN_PREMIUM_STORAGE_LIMIT'))/pow(2,30))}} GB</li>
                                                        <li><span v-if="user">You can manage how much storage you're using on your <a :href="'/users/' + user.slug">Profile Page</a></span><span v-if="!user">Once you're logged in, you can manage how much storage you're using on your Profile Page</span></li>
                                                    </ul>
                                                </li>
                                                <li>If your request would put you over your limit, the notification will let you know that the episode was not archived.</li>
                                            </ul>
                                        </li>
                                        <li>If your request is processed successfully, hooray!
                                            <ul>
                                                <li>This means that {{env('APP_NAME')}} has copied the episode to our servers</li>
                                                <li>Any time you add that episode to a playlist, it will use {{env('APP_NAME')}}'s copy - instead of relying on the original.</li>
                                                <li>This does not apply to recommendations.</li>
                                            </ul>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="rss">
                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    What is RSS?
                                </div>
                                <div class="panel-body">
                                    Well, <a target="_new" href="https://en.wikipedia.org/wiki/RSS">here is the wikipedia page</a>.<br><br>
                                    If that's too much, here's a short version:<br><br>
                                    A Show's RSS Feed is a way for the Show to describe itself and list its episodes.<br><br>
                                    It uses <a href="https://en.wikipedia.org/wiki/XML">XML</a>, which is a language that a computer will understand. <a target="_new" href="/playlists/me/feed">Click here to see an example.</a><br><br>Yeah, it's pretty ugly. But to a podcatcher, it makes total sense.<br><br>
                                    When you regsiter an RSS feed with your podcatcher, it periodically checks to see if there are any new items in the list. If so, it'll tell you about them.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="register-my-feed">
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
                                                Once you log in, you'll see a link here - which you will need to copy.
                                            </div>
                                        </li>
                                        <li>
                                            Find your podcatcher in this list, and follow the link for its instructions on how to add a podcast from its RSS feed.
                                            <ul>
                                                @foreach($podcatchers as $p)
                                                    <li><a target="_new" href="{{$p->url_register_feed != '' ? $p->url_register_feed : $p->url}}">{{$p->name}}</a> ({{$p->platforms_joined()}})</li>
                                                @endforeach
                                                
                                                <li><a target="_new" href="https://twitter.com/{{env('TWITTER_HANDLE')}}">Contact us</a> if your podcatcher isn't listed, or if a link is out-of-date or unhelpful.</li>
                                            </ul>
                                        </li>
                                        <li>Paste the link that you copied in Step 1 into your podcatcher's RSS Feed URL field.</li>
                                        <li>Then your accepted recommendations will show up in your podcatcher!</li>
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
                                                    <li><a target="_new" href="{{$p->url}}">{{$p->name}}</a></li>
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