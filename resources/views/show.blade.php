@extends('spark::layouts.app')

@section('content')
<show :user="user" inline-template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-lg-3 col-md-4">
                    <div class="panel panel-default show-profile-card">
                        <div class="panel-heading">
                            <a class="show-image-container">
                                <img :src="show.img_url" class="show-image" />
                            </a>
                            @{{show.name}}
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <a :href="show.feed">Original RSS Feed</a><br>
                                    <a :href="'{{env('APP_URL')}}/' + show.slug + '/feed'">{{env('APP_NAME')}} Feed</a>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body panel-border-top">
                            <div class="row">
                                <div class="col-xs-7">
                                    @{{show.total_likes}} Like@{{show && show.total_likes != '1' ? 's' : ''}}
                                </div>
                                <div class="col-xs-5 pull-right">
                                    <button class="btn-like btn btn-sm" v-if="user && user.verified && !show.this_user_likes" @click.prevent="likeShow"></button>
                                
                                    <button class="btn-unlike btn btn-sm" v-if="user && user.verified && !!show.this_user_likes" @click.prevent="unlikeShow"></button>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body panel-border-top">
                            <div class="row">
                                <div class="col-xs-7">
                                    @{{show.episodeCount}} Episode@{{show && show.episodeCount != '1' ? 's' : ''}}
                                </div>
                            </div>
                        </div>
                        <div class="panel-body panel-border-top">
                            @{{show.description}}
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-9 col-md-8">
                    <div class="panel panel-default panel-list-item episode-container" v-for="episode in displayEpisodes" :key="episode.slug">
                        <div class="panel-heading">
                            <a :href="'/episodes/' + episode.slug">
                                <img :src="episode.img_url" class="episode-image" />
                                <strong>@{{episode.name}}</strong>
                            </a>
                            <small v-tooltip :title="episode.pubdate_str">@{{episode.howLongAgo}}</small>
                        </div>
                        <div class="panel-body" v-html="episode.description"></div>
                        <div class="panel-footer" v-if="user && user.verified">
                            <div class="episode-action">
                                <button class="btn-recommend" @click.prevent="recommendEpisode(episode)">
                                    <div class="icon-container" title="Recommend">
                                        <i class="fa fa-reply"></i>
                                    </div>
                                    <div class="icon-text-container">
                                        <span>@{{episode.total_recommendations}}</span>
                                    </div>
                                </button>
                            </div>
                            <div class="episode-action">
                                <button :class="{'btn-episode-unlike': episode.this_user_likes, 'btn-episode-like' : !episode.this_user_likes}" @click.prevent="toggleLikeEpisode(episode)">
                                    <div class="icon-container" title="Like">
                                        <div class="heart-container">
                                            <div class="heart"></div>
                                        </div>
                                    </div>
                                    <div class="icon-text-container">
                                        <span>@{{episode.total_likes}}</span>
                                    </div>
                                </button>
                            </div>
                            <div class="episode-action">
                                <button class="btn-add-to-playlist" @click.prevent="selectEpisodeForAddingToPlaylist(episode)">
                                    <div class="icon-container" title="Add To Playlist">
                                        <i class="fa fa-plus"></i>
                                    </div>
                                    <div class="icon-text-container">
                                        <span>@{{episode.total_playlists}}</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default panel-list-item clickable" v-if="show.episodes && show.episodeCount > show.episodes.length" @click="showMore">
                        <div class="panel-body centered">
                            Load more episodes...
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-select-playlist" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true" class="btn btn-primary btn-separate-from-other-buttons">&times;</button>
                        <h4 class="modal-title">@{{show.name}}<br>@{{selectedEpisode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            Add this episode to:
                        </div>
                        <button class="btn btn-primary btn-separate-from-other-buttons" v-for="playlist in playlists" @click.prevent="addSelectedEpisodeToPlaylist(playlist)">@{{playlist.name}}</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-no-playlists" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">@{{show.name}}<br>@{{selectedEpisode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        You have no Playlists. <a href="/playlists/new">Click here</a> to create one.
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-add-to-playlist-success" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">@{{show.name}}<br>@{{selectedEpisode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        Success! You added this episode to the @{{selectedPlaylist.name}} playlist. <a :href="'/playlists/' + selectedPlaylist.slug">Click here</a> to see it
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-recommend-episode-1" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">@{{show.name}}<br>@{{selectedEpisode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            Recommend this episode to:
                        </div>
                        <button class="btn btn-primary btn-separate-from-other-buttons" v-for="user in recentRecommendees" @click.prevent="recommendEpisodeToExistingUser(user.slug)">@{{user.name}}</button>
                        <button class="btn btn-primary" @click.prevent="recommendEpisodeToSomeoneElse">Someone else</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-recommend-episode-2" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">@{{show.name}}<br>@{{selectedEpisode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            Recommend this episode via email:
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <input class="form-control" v-model="recommendEmail" placeholder="sam@example.com" />
                            </div>
                        </div>
                        <div class="row" style="margin-top:10px;">
                            <button class="btn btn-primary pull-right" style="margin-right: 15px;" @click="sendRecommendation()">
                                <span>
                                    <i class="fa fa-btn" :class="{'fa-spinner fa-spin': recommendForm.busy, 'fa-check-circle': !recommendForm.busy}"></i>Send recommendation
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-recommend-success" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">@{{show.name}}<br>@{{selectedEpisode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            Success! You have recommended this podcast episode
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-max-recommendations" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Max Recommendations</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            You have reached today's maximum number of Recommendations for your Subscription Plan.<br><a href="/settings#/subscription">Click here</a> to change your Plan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</show>
@endsection
