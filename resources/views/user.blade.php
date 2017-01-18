@extends('spark::layouts.app')

@section('content')
<view-user :user="user" inline-template>
    <div class="container">
        <div class="row centered">
            <h3>@{{viewed_user.name}}</h3>
        </div>
        <div class="col-md-4 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">Recommendations</div>
                <div class="panel-body">
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">Playlists</div>
                <div class="panel-body">
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">Connections</div>
                <div class="panel-body">
                </div>
            </div>
        </div>
    </div>
</view-user>
@endsection
