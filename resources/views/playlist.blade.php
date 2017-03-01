@extends('spark::layouts.app')

@section('title')
{{config('app.name').' - '.$title}}
@endsection

@section('content')
<playlist :user="user" inline-template>
    <div>
        <div class="container">
            <h3 class="centered">@{{episodeGroups.playlist.name}}</h3>
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading centered">
                            @{{episodeGroups.playlist.name}}
                        </div>
                        <div class="panel-body panel-list-item">
                            @{{episodeGroups.playlist.description}}
                        </div>
                        <div class="panel-body panel-list-item">
                            @{{episodeGroups.playlist.episodes.length}} Episode@{{episodeGroups.playlist.episodes.length == 1 ? '' : 's'}}
                        </div>
                        <div class="panel-body panel-list-item">
                            <a href="#" @click.prevent="copyFeed(feedUrl, 'copy-link')" id="copy-link">@{{copyLinkText}}</a>
                            <input type="text" id="copy-link-fallback" :value="feedUrl" class="fallback"/>
                        </div>
                        <div class="panel-footer" v-if="user && episodeGroups.playlist && episodeGroups.playlist.user_slug == user.slug">
                            <a :href="'/playlists/' + episodeGroups.playlist.slug + '/edit'">Edit</a>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-8">
                    <div class="panel panel-default panel-list-item" v-for="(episode, index) in episodeGroups.playlist.episodes" :data-slug="episode.slug">
                        <div class="panel-heading">
                            <a :href="'/shows/' + episode.show_slug">@{{episode.show_name}}</a>
                            <br>
                            <a :href="'/episodes/' + episode.slug">
                                <img :src="episode.img_url" class="episode-image" />
                                <strong>@{{episode.name}}</strong>
                            </a>
                            <small v-tooltip :title="episode.pubdate_str">@{{episode.howLongAgo}}</small>
                        </div>
                        <div class="panel-body">
                            <div :class="{'col-xs-8': user, 'col-xs-12': !user}" v-html="episode.description"></div>
                            <div class="col-xs-4" v-if="user">
                                <div class="episode-action col-xs-2">
                                    <button class="btn-move-to-top" @click.prevent="moveToTop(episode)" :disabled="!loaded">
                                        <div class="icon-container" title="Move Episode to the Top" v-tooltip v-if="index != 0">
                                            <i class="fa fa-long-arrow-up"></i>
                                        </div>
                                    </button>
                                </div>
                                <div class="episode-action col-xs-2">
                                    <button class="btn-move-up" @click.prevent="moveUp(episode)" :disabled="!loaded">
                                        <div class="icon-container" title="Move Episode Up" v-tooltip v-if="index != 0">
                                            <i class="fa fa-arrow-up"></i>
                                        </div>
                                    </button>
                                </div>
                                <div class="episode-action col-xs-2">
                                    <button class="btn-move-down" @click.prevent="moveDown(episode)" :disabled="!loaded">
                                        <div class="icon-container" title="Move Episode Down" v-tooltip v-if="index != episodeGroups.playlist.episodes.length - 1">
                                            <i class="fa fa-arrow-down"></i>
                                        </div>
                                    </button>
                                </div>
                                <div class="episode-action col-xs-2">
                                    <button class="btn-move-to-bottom" @click.prevent="moveToBottom(episode)" :disabled="!loaded">
                                        <div class="icon-container" title="Move Episode to the Bottom" v-tooltip v-if="index != episodeGroups.playlist.episodes.length - 1">
                                            <i class="fa fa-long-arrow-down"></i>
                                        </div>
                                    </button>
                                </div>
                                <div class="episode-action col-xs-2" @click.prevent="remove(episode)" :disabled="!loaded">
                                    <button class="btn-remove">
                                        <div class="icon-container" title="Remove Episode from this Playlist" v-tooltip>
                                            <i class="fa fa-times"></i>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @include('partials.episode-footer')
                    </div>
                    <div class="panel" v-if="loaded && episodeGroups.playlist.episodes.length == 0">
                        <div class="panel-body centered">
                            This playlist has no episodes
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-are-you-sure" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Are you sure?</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            Are you sure you want to remove this episode?
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" @click="noNeverMind">No, Never mind</button>

                        <button type="button" class="btn btn-danger" @click="yesImSureRemoveEpisode" :disabled="areYouSure.busy">
                            Yes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @include('modals.episode-modals')
    </div>
</playlist>
@endsection