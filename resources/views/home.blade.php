@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
    <div class="container">
        <div class="row" v-if="!user">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">What is {{env('APP_NAME')}}?</div>
                    <div class="panel-body">
                        Become a Podcast DJ for your friends.
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="showGetStarted">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Get Started
                    </div>
                    <div class="panel-body">
                        <ol>
                            <li :class="{strikethrough: user}"><a href="#" onclick="window.open('/auth/facebook','auth','width=500,height=450');return false;">Login with Facebook</a> or <a href="/register">with your email address</a></li>
                            <li :class="{strikethrough: user && user.hasLikedSomething}"><a href="/shows">Find a show and 'Like' it</a></li>
                            <li :class="{strikethrough: user && user.hasRecommendedSomething}"><a href="/shows">Recommend an episode</a></li>
                            <li :class="{strikethrough: user && user.hasAcceptedARecommendation}" v-if="user && user.hasReceivedARecommendation"><a href="/recommendations">Accept a recommendation</a></li>
                            <li :class="{strikethrough: user && user.hasTakenActionOnARecommendation}" v-if="!user || !user.hasReceivedARecommendation"><a href="/recommendations">Receive a recommendation</a></li>
                            <li :class="{strikethrough: user && user.hasRegisteredTheirFeed}"><a href="/help#register_my_feed">Register Your Feed</a></li>
                            <li :class="{strikethrough: user && user.hasCreatedAPlaylist}"><a href="/playlists/new">Create a Playlist</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="episodes.length > 0">
            <h3 class="centered">Recently Popular Episodes</h3>
            <div class="panel panel-default panel-list-item episode-container" v-for="episode in episodes" :key="episode.slug">
                <div class="panel-heading">
                    <a :href="'/episodes/' + episode.slug">
                        <img :src="episode.img_url" class="episode-image" />
                        <strong>@{{episode.name}}</strong>
                    </a>
                    <small v-tooltip :title="episode.pubdate_str">@{{episode.howLongAgo}}</small>
                </div>
                <div class="panel-body" v-html="episode.description"></div>
                <div class="panel-body" v-if="episode.likers.length > 0 || (episode.friend_recommenders && episode.friend_recommenders.length > 0)">
                    <div class="row" v-if="episode.likers.length > 0">
                        <div class="col-xs-12">
                            <div class="icon-container">
                                <div class="heart-container">
                                    <div class="heart" style="background-position: right;"></div>
                                </div>
                            </div>
                            Liked by:<span v-for="(liker, index) in episode.likers"><a :href="'/users/' + liker.slug">@{{liker.name}}</a>@{{index == episode.likers.length - 1 ? '' : ', '}}</span>
                        </div>
                    </div>
                    <div class="row" v-if="episode.friend_recommenders && episode.friend_recommenders.length > 0">
                        <div class="col-xs-12">
                            <div class="icon-container">
                                <i class="fa fa-reply"></i>
                            </div>
                            Recommended by:<span v-for="(recommender, index) in episode.friend_recommenders"><a :href="'/users/' + recommender.slug">@{{recommender.name}}</a>@{{index == episode.friend_recommenders.length - 1 ? '' : ', '}}</span>
                        </div>
                    </div>
                </div>
                @include('partials.episode-footer')
            </div>
        </div>
    </div>
</home>
@endsection
