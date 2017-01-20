@extends('spark::layouts.app')

@section('content')
<episode :user="user" inline-template>
    <div>
        <div class="container">
            <h3><a :href="'/shows/' + episode.show_slug">@{{episode.show_name}}</a></h3>
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @{{episode.name}}
                        </div>
                        <div class="panel-body">
                            @{{episode.description}}
                        </div>
                        <div class="panel-footer">
                            <div class="stream-item-footer" v-if="user.verified">
                                <div class="ProfileTweet-action ProfileTweet-action--recommend">
                                    <button class="ProfileTweet-actionButton" type="button" @click.prevent="recommendEpisode(episode)">
                                        <div class="IconContainer" title="Recommend">
                                            <i class="fa fa-reply"></i>
                                        </div>
                                        <div class="IconTextContainer">
                                            <span class="ProfileTweet-actionCount">
                                              <span class="ProfileTweet-actionCountForPresentation">@{{episode.total_recommendations}}</span>
                                            </span>
                                        </div>
                                    </button>
                                </div>
                                <div v-if="!episode.this_user_likes" class="ProfileTweet-action ProfileTweet-action--like">
                                    <button class="ProfileTweet-actionButton" type="button" @click.prevent="likeEpisode(episode)">
                                        <div class="IconContainer" title="Like">
                                            <div class="HeartAnimationContainer">
                                                <div class="HeartAnimation"></div>
                                            </div>
                                        </div>
                                        <div class="IconTextContainer">
                                            <span class="ProfileTweet-actionCount">
                                              <span class="ProfileTweet-actionCountForPresentation" aria-hidden="true">@{{episode.total_likes}}</span>
                                            </span>
                                        </div>
                                    </button>
                                </div>
                                <div v-if="episode.this_user_likes" class="ProfileTweet-action ProfileTweet-action--unlike">
                                    <button class="ProfileTweet-actionButtonUndo" type="button" @click.prevent="unlikeEpisode(episode)">
                                        <div class="IconContainer" title="Unlike">
                                            <div class="HeartAnimationContainer">
                                                <div class="HeartAnimation"></div>
                                            </div>
                                        </div>
                                        <div class="IconTextContainer">
                                            <span class="ProfileTweet-actionCount">
                                              <span class="ProfileTweet-actionCountForPresentation">@{{episode.total_likes}}</span>
                                            </span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-recommend-episode-1" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">@{{episode.name}}</h4>
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
                        <h4 class="modal-title">@{{episode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            Recommend this episode via email or twitter:
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="form-control" v-model="recommendEmail" placeholder="sam@example.com" />
                            </div>
                            <div class="col-xs-6">
                                <input class="form-control" v-model="recommendTwitter" placeholder="@twitter" />
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
                        <h4 class="modal-title">@{{episode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            Success! You have recommended this podcast episode
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</episode>
@endsection