@extends('spark::layouts.app')

@section('content')
<playlists :user="user" inline-template>
    <div>
        <button class="btn btn-primary pull-right btn-plus-new" @click.prevent="addPlaylist">&plus;</button>
        <div class="container">
            <h3 class="centered">Playlists</h3>
            @if (session('msg'))
                <div class="alert {{session('statusClass')}}">
                    {!! session('msg') !!}
                </div>
            @endif
            <div class="row">
                <div class="col-xs-12 col-md-6" v-if="user">
                    <div class="panel panel-default">
                        <div class="panel-heading centered">
                            Your Playlists
                        </div>
                        <div class="panel-body" v-if="userPlaylistsLoaded && userPlaylists.length == 0">
                            You have no playlists
                        </div>
                        <div class="panel-body panel-list-item" v-for="playlist in userPlaylists">
                            <a :href="'/playlists/' + playlist.slug">@{{playlist.name}}</a>
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
        <div class="modal fade" id="modal-max-playlists" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Max Playlists</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            You have reached the maximum number of Playlists for your Subscription Plan.<br><a href="/settings#/subscription">Click here</a> to change your Plan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</playlists>
@endsection