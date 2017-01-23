@extends('spark::layouts.app')

@section('content')
<show :user="user" inline-template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-lg-3 col-md-4">
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
                <div class="col-xs-12 col-lg-9 col-md-8">
                    <ol class="stream-items js-navigable-stream">
                        <li class="js-stream-item stream-item stream-item expanding-stream-item" v-for="episode in displayEpisodes" :key="episode.slug">
                            <div class="tweet">
                                <div class="content">
                                    <div class="stream-item-header">
                                        <a :href="'/episodes/' + episode.slug">
                                            <img class="avatar js-action-profile-avatar" :src="episode.img_url" alt="">
                                        <strong class="fullname js-action-profile-name show-popup-with-id" data-aria-label-part="">@{{episode.name}}</strong>
                                        </a>
                                        <small class="time">
                                            <a :href="'/episodes/' + episode.slug" class="tweet-timestamp js-permalink js-nav js-tooltip" :title="episode.pubdate_str">
                                                <span class="_timestamp js-short-timestamp js-relative-timestamp" aria-hidden="true">@{{episode.howLongAgo}}</span>
                                            </a>
                                        </small>
                                    </div>
                                    <div class="TweetTextSize  js-tweet-text tweet-text" lang="en" data-aria-label-part="0" v-html="episode.description" ></div>
                                    <div class="stream-item-footer" v-if="user && user.verified">
                                        <div class="ProfileTweet-action">
                                            <button class="ProfileTweet-actionButton  ProfileTweet-action--recommend" type="button" @click.prevent="recommendEpisode(episode)">
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
                        </li>
                        <li class="js-stream-item stream-item stream-item expanding-stream-item stream-loadmore stream-loadmore-episodes" v-if="show.episodes && show.episodeCount > show.episodes.length" @click="showMore">Load more episodes...</li>
                    </ol>
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
    </div>
</show>
@endsection
