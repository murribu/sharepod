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
                        @include('partials.episode-footer')
                    </div>
                </div>
            </div>
        </div>
        @include('modals.episode-modals')
    </div>
</episode>
@endsection