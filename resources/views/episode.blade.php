@extends('spark::layouts.app')

@section('content')
<episode :user="user" inline-template>
    <div>
        <div class="container">
            <h3><a :href="'/shows/' + selectedEpisode.show_slug">@{{selectedEpisode.show_name}}</a></h3>
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @{{selectedEpisode.name}}
                        </div>
                        <div class="panel-body" v-html="selectedEpisode.description"></div>
                        <div class="panel-footer" v-if="user && user.verified">
                            <div class="episode-action">
                                <button class="btn-recommend" @click.prevent="recommendEpisode(selectedEpisode)">
                                    <div class="icon-container" title="Recommend">
                                        <i class="fa fa-reply"></i>
                                    </div>
                                    <div class="icon-text-container">
                                        <span>@{{selectedEpisode.total_recommendations}}</span>
                                    </div>
                                </button>
                            </div>
                            <div class="episode-action">
                                <button :class="{'btn-episode-unlike': selectedEpisode.this_user_likes, 'btn-episode-like' : !selectedEpisode.this_user_likes}" @click.prevent="toggleLikeEpisode(selectedEpisode)">
                                    <div class="icon-container" title="Like">
                                        <div class="heart-container">
                                            <div class="heart"></div>
                                        </div>
                                    </div>
                                    <div class="icon-text-container">
                                        <span>@{{selectedEpisode.total_likes}}</span>
                                    </div>
                                </button>
                            </div>
                            <div class="episode-action">
                                <button class="btn-add-to-playlist" @click.prevent="selectEpisodeForAddingToPlaylist(selectedEpisode)">
                                    <div class="icon-container" title="Add To Playlist">
                                        <i class="fa fa-plus"></i>
                                    </div>
                                    <div class="icon-text-container">
                                        <span>@{{selectedEpisode.total_playlists}}</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('modals.episode-modals')
    </div>
</episode>
@endsection