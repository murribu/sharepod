@extends('spark::layouts.app')

@section('content')
<show :user="user" inline-template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-md-3">
                    <div class="DashboardProfileCard module">
                        <div class="DashboardProfileCard-content">
                            <a class="DashboardProfileCard-avatarLink u-inlineBlock" :href="'/shows/' + show.slug" :title="show.name">
                                <img class="DashboardProfileCard-avatarImage js-action-profile-avatar" :src="show.img_url" alt="">
                            </a>
                            <div class="DashboardProfileCard-userFields">
                                <div class="DashboardProfileCard-name u-textTruncate">
                                    <a class="u-textInheritColor" :href="'/shows/' + show.slug">@{{show.name}}</a>
                                </div>
                                <button class="DashboardProfileCard-like btn btn-sm" v-if="user && user.verified && !show.this_user_likes" @click.prevent="likeShow"></button>
                                
                                <button class="DashboardProfileCard-unlike btn btn-sm" v-if="user && user.verified && !!show.this_user_likes" @click.prevent="unlikeShow"></button>
                                
                                <span class="DashboardProfileCard-screenname u-inlineBlock u-dir" dir="ltr"></span>
                            </div>
                            <div class="DashboardProfileCard-stats">
                                <ul class="DashboardProfileCard-statList Arrange Arrange--bottom Arrange--equal"><li class="DashboardProfileCard-stat Arrange-sizeFit">
                                    <a class="DashboardProfileCard-statLink u-textUserColor u-linkClean u-block" :title="show.episodeCount + ' Episodes'" :href="'/shows/' + show.slug">
                                        <span class="DashboardProfileCard-statLabel u-block">Episodes</span>
                                        <span class="DashboardProfileCard-statValue" data-is-compact="false">@{{show.episodeCount}}</span>
                                    </a>
                                  </li>
                                    <li class="DashboardProfileCard-stat Arrange-sizeFit">
                                        <span class="DashboardProfileCard-statLabel u-block">Likes</span>
                                        <span class="DashboardProfileCard-statValue likeCount" data-is-compact="false">@{{show.total_likes}}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-9">
                    <ol class="stream-items js-navigable-stream">
                        <li class="js-stream-item stream-item stream-item expanding-stream-item" v-for="episode in displayEpisodes" :key="episode.slug">
                            <div class="tweet">
                                <div class="content">
                                    <div class="stream-item-header">
                                        <a :href="'/shows/{{$show->slug}}/' + episode.slug">
                                            <img class="avatar js-action-profile-avatar" :src="episode.img_url" alt="">
                                        <strong class="fullname js-action-profile-name show-popup-with-id" data-aria-label-part="">@{{episode.name}}</strong>
                                        </a>
                                        <small class="time">
                                            <a :href="'/shows/{{$show->slug}}/' + episode.slug" class="tweet-timestamp js-permalink js-nav js-tooltip" :title="episode.pubdate_str">
                                                <span class="_timestamp js-short-timestamp js-relative-timestamp" aria-hidden="true">@{{episode.howLongAgo}}</span>
                                            </a>
                                        </small>
                                    </div>
                                    <div class="TweetTextSize  js-tweet-text tweet-text" lang="en" data-aria-label-part="0" v-html="episode.description" ></div>
                                    <div class="stream-item-footer" v-if="user.verified">
                                        <div class="ProfileTweet-action ProfileTweet-action--send">
                                            <button class="ProfileTweet-actionButton" type="button" @click.prevent="sendEpisode(episode)">
                                                <div class="IconContainer" title="Send">
                                                    <i class="fa fa-reply"></i>
                                                </div>
                                                <div class="IconTextContainer">
                                                    <span class="ProfileTweet-actionCount">
                                                      <span class="ProfileTweet-actionCountForPresentation">@{{episode.total_sends}}</span>
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
                        </li>
                        <li class="js-stream-item stream-item stream-item expanding-stream-item stream-loadmore stream-loadmore-episodes" v-if="show.episodes && show.episodeCount > show.episodes.length" @click="showMore">Load more episodes...</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-send-episode-1" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        
                        <h4 class="modal-title">@{{selectedEpisode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        Would you like to send this episode via
                        <button class="btn btn-primary" @click="sendEpisodeViaEmailDialog">Email</button>
                        or
                        <button class="btn btn-primary" @click="sendEpisodeViaTwitterDialog">Twitter</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-send-episode-via-email" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        
                        <h4 class="modal-title">Send via Email</h4>
                    </div>
                    <div class="modal-body">
                        <input class="form-control" name="" :value="sendToEmailAddress" placeholder="sam@example.com" />
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" @click="sendEpisodeViaEmail">
                            <span>
                                <i class="fa fa-btn" :class="{'fa-spinner fa-spin': sendForm.busy, 'fa-check-circle': !sendForm.busy}"></i>Send!
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-send-episode-via-twitter" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        
                        <h4 class="modal-title">Send via Twitter</h4>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" type="button" data-dismiss="modal">No, Go Back</button>
                        <button class="btn btn-danger">Yes, unlink</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</show>
@endsection
