@extends('spark::layouts.app')

@section('content')
<playlists :user="user" inline-template>
    <div>
        <a href="/playlists/new" class="btn btn-primary pull-right btn-plus-new">&plus;</a>
        <div class="container">
            <h3 class="centered">Playlists</h3>
            <div class="row">
                <div class="col-xs-12 col-md-6" v-if="user">
                    <div class="panel panel-default">
                        <div class="panel-heading centered">
                            Your Playlists
                        </div>
                        <div class="panel-body" v-if="userPlaylistsLoaded && userPlaylists.length == 0">
                            You have no playlists
                        </div>
                        <div class="panel-body" v-if="userPlaylists.length > 0">
                            <div v-for="playlist in userPlaylists">
                                <a :href="'/playlists' + playlist.slug">@{{playlist.name}}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12" :class="{'col-md-6': user}">
                    <div class="panel panel-default">
                        <div class="panel-heading centered">
                            Popular Playlists
                        </div>
                            <div class="panel-body" v-if="popularPlaylistsLoaded && popularPlaylists.length == 0">
                            You have no playlists
                        </div>
                        <div class="panel-body panel-list-item" v-for="playlist in popularPlaylists">
                            <a :href="'/playlists/' + playlist.slug">@{{playlist.name}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</playlists>
@endsection