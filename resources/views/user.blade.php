@extends('spark::layouts.app')

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.4.6/mousetrap.min.js"></script>
@endsection

@section('content')
<view-user :user="user" inline-template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-lg-3 col-md-4">
                    <div class="panel panel-default panel-flush show-profile-card">
                        <div class="panel-heading">
                            <a class="show-image-container">
                                <img :src="viewed_user.photo_url" class="show-image" />
                            </a>
                            @{{viewed_user.name}}
                        </div>
                        <div class="panel-body">
                            <div class="user-tabs">
                                <ul class="nav left-stacked-tabs" role="tablist">
                                    <li role="presentation">
                                        <a href="#episodes-liked" aria-controls="episodes-liked" role="tab" data-toggle="tab">
                                            @{{episodes_liked.length}} Episodes Liked
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#shows-liked" aria-controls="shows-liked" role="tab" data-toggle="tab">
                                            @{{shows_liked.length}} Shows Liked
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#playlists" aria-controls="playlists" role="tab" data-toggle="tab">
                                            @{{playlists.length}} Playlists
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#connections" aria-controls="connections" role="tab" data-toggle="tab">
                                            @{{connections.accepted.length + connections.pending.length}} Connections
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-footer" v-if="isMe">
                            <a href="/recommendations">Manage Recommendations</a>
                        </div>
                        <div class="panel-footer" v-if="isMe">
                            <a href="/connections">Manage Connections</a>
                        </div>
                        <div class="panel-footer" v-if="isMe">
                            <a href="/settings">Edit Profile</a>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-9 col-md-8">
                    <div class="tab-content">
                        <div class="tab-pane active" role="tabpanel" id="episodes-liked">
                            <h3 class="centered" v-if="viewed_user.name">@{{viewed_user.name}}'s Liked Episodes</h3>
                            <div class="panel panel-default panel-list-item episode-container" v-for="episode in episodes_liked" :key="episode.slug">
                                <div class="panel-heading">
                                    <a :href="'/episodes/' + episode.slug">
                                        <img :src="episode.img_url" class="episode-image" />
                                        <strong>@{{episode.name}}</strong>
                                    </a>
                                    <small v-tooltip :title="episode.pubdate_str">@{{episode.howLongAgo}}</small>
                                    <small v-tooltip :title="episode.likeddate_str" class="liked-time">
                                        <div class="icon-container">
                                            <div class="heart-container">
                                                <div class="heart" style="background-position: right;"></div>
                                            </div>
                                        </div>
                                        @{{episode.likedHowLongAgo}}
                                    </small>
                                </div>
                                <div class="panel-body" v-html="episode.description"></div>
                            </div>
                            <div class="panel panel-default panel-list-item" v-if="episodes_liked_loaded && episodes_liked.length == 0">
                                <div class="panel-body">
                                    @{{viewed_user.name}} has not liked any episodes yet.
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" role="tabpanel" id="shows-liked">
                            <h3 class="centered" v-if="viewed_user.name">@{{viewed_user.name}}'s Liked Shows</h3>
                            <div class="panel panel-default panel-list-item" v-for="show in shows_liked" :key="show.slug">
                                <div class="panel-heading">
                                    <a :href="'/shows/' + show.slug">
                                        <img :src="show.img_url" class="episode-image" />
                                        <strong>@{{show.name}}</strong>
                                    </a>
                                    <small v-tooltip :title="show.likeddate_str" class="liked-time">
                                        <div class="icon-container">
                                            <div class="heart-container">
                                                <div class="heart" style="background-position: right;"></div>
                                            </div>
                                        </div>
                                        @{{show.likedHowLongAgo}}
                                    </small>
                                </div>
                                <div class="panel-body" v-html="show.description"></div>
                            </div>
                            <div class="panel panel-default panel-list-item" v-if="shows_liked.length == 0">
                                <div class="panel-body">
                                    @{{viewed_user.name}} has not liked any shows yet.
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" role="tabpanel" id="playlists">
                            <h3 class="centered" v-if="viewed_user.name">@{{viewed_user.name}}'s Playlists</h3>
                            <div class="panel panel-default panel-list-item" v-for="playlist in playlists" :key="playlists.slug">
                                <div class="panel-heading">
                                    <a :href="'/playlists/' + playlist.slug">
                                        <strong>@{{playlist.name}}</strong>
                                    </a>
                                </div>
                                <div class="panel-body">@{{playlist.description}}</div>
                            </div>
                            <div class="panel panel-default panel-list-item" v-if="playlists_loaded && playlists.length == 0">
                                <div class="panel-body">
                                    @{{viewed_user.name}} does not have any playlists yet.
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" role="tabpanel" id="connections">
                            <div class="panel panel-default panel-list-item">
                                <div class="panel-heading" v-if="viewed_user.name">
                                    @{{viewed_user.name}}'s Friends
                                </div>
                                <div class="panel-body panel-border-top" v-for="connection in connections.accepted" :key="connections.slug">
                                    <a :href="'/users/' + connection.user_slug">
                                        <strong>@{{connection.user_name}}</strong>
                                    </a>
                                </div>
                                <div class="panel-body" v-if="connections_loaded && connections.accepted.length == 0">
                                    @{{viewed_user.name}} does not have any friends yet.<br>Someone is considered a Connection when they have recommended an episode.<br>Someone is considered a Friend when that connection is approved.
                                </div>
                            </div>
                            <div class="panel panel-default panel-list-item">
                                <div class="panel-heading" v-if="viewed_user.name">
                                    @{{viewed_user.name}}'s Pending Connections
                                </div>
                                <div class="panel-body panel-border-top" v-for="connection in connections.pending" :key="connections.slug">
                                    <a :href="'/users/' + connection.user_slug">
                                        <strong>@{{connection.user_name}}</strong>
                                    </a>
                                </div>
                                <div class="panel-body" v-if="connections_loaded && connections.pending.length == 0">
                                    @{{viewed_user.name}} does not have any pending connections.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</view-user>
@endsection
