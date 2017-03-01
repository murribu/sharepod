@extends('spark::layouts.app')

@section('title')
{{config('app.name').' - '.$title}}
@endsection

@section('content')
<episode :user="user" inline-template>
    <div>
        <div class="container" v-if="episodeGroups.one.episodes[0]">
            <h3><a :href="'/shows/' + episodeGroups.one.episodes[0].show_slug">@{{episodeGroups.one.episodes[0].show_name}}</a></h3>
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-default" :data-slug="episodeGroups.one.episodes[0].slug">
                        <div class="panel-heading">
                            @{{episodeGroups.one.episodes[0].name}}
                        </div>
                        <div class="panel-body" v-html="episodeGroups.one.episodes[0].description"></div>
                        @include('partials.episode-footer')
                    </div>
                </div>
            </div>
        </div>
        @include('modals.episode-modals')
    </div>
</episode>
@endsection