@extends('spark::layouts.app')

@section('content')
<playlist-edit :user="user" inline-template>
    <div>
        <div class="container">
            <div class="alert alert-danger" v-if="!user.canAddAPlaylist">
                This isn't going to work. You have reached the maximum number of Playlists for your Subscription Plan.<br><a href="/settings#/subscription">Click here</a> to change your Plan.
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="centered">{{isset($playlist) ? 'Edit' : 'New'}} Playlist</h3>
                </div>
                <div class="panel-body">
                    <form method="post">
                        {{csrf_field()}}
                        <div class="form-group row">
                            <label class="form-label control-label col-xs-12 col-sm-2" for="name">Name</label>
                            <div class="col-xs-12 col-sm-10">
                                <input class="form-control" name="name" value="{{isset($playlist) ? $playlist->name : ''}}" :disabled="!user.canAddAPlaylist" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="form-label control-label col-xs-12 col-sm-2" for="description">Description</label>
                            <div class="col-xs-12 col-sm-10">
                                <textarea class="form-control" name="description" :disabled="!user.canAddAPlaylist">{{isset($playlist) ? $playlist->description : ''}}</textarea>
                            </div>
                        </div>
                        <button class="btn btn-primary pull-right" type="submit" :disabled="!user.canAddAPlaylist">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</playlist-edit>
@endsection