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
    </div>
</home>
@endsection
