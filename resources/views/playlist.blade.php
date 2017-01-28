@extends('spark::layouts.app')

@section('content')
<playlist :user="user" inline-template>
    <div>
        <div class="container">
            <h3 class="centered">@{{playlist.name}}</h3>
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading centered">
                            @{{playlist.name}}
                        </div>
                        <div class="panel-body panel-list-item">
                            @{{playlist.description}}
                        </div>
                        <div class="panel-body panel-list-item">
                            @{{playlist.episodes.length}} Episode@{{playlist.episodes.length == 1 ? '' : 's'}}
                        </div>
                        <div class="panel-body panel-list-item">
                            <a href="#" @click.prevent="copyFeed()">@{{copyLinkText}}</a>
                        </div>
                        <div class="panel-footer" v-if="user && playlist && playlist.user_slug == user.slug">
                            <a :href="'/playlists/' + playlist.slug + '/edit'">Edit</a>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-offset-1 col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading centered">
                            Episodes
                        </div>
                        <div class="panel-body panel-list-item" v-for="(episode, index) in playlist.episodes">
                            @{{index + 1}}. <a :href="'/episodes/' + episode.slug">@{{episode.name}}</a> from <a :href="'/podcasts/' + episode.show_slug">@{{episode.show_name}}</a>
                        </div>
                        <div class="panel-body centered" v-if="loaded && playlist.episodes.length == 0">
                            This playlist has no episodes
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</playlist>
@endsection