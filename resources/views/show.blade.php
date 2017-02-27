@extends('spark::layouts.app')

@section('content')
<show :user="user" inline-template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-lg-3 col-md-4">
                    <div class="panel panel-default show-profile-card">
                        <div class="panel-heading">
                            <a class="show-image-container">
                                <img :src="show.img_url" class="show-image" />
                            </a>
                            @{{show.name}}
                        </div>
                        <div class="panel-body">
                            <div class="row" v-if="show.feed">
                                <div class="col-xs-12">
                                    <a href="#" @click.prevent="copyFeed(show.feed, 'copy-feed-original')" id="copy-feed-original">Copy original RSS feed</a>
                                    <input type="text" id="copy-feed-original-fallback" class="fallback" :value="show.feed" v-tooltip title="Copy this text" /><br>
                                    <a href="#" @click.prevent="copyFeed('{{env('APP_URL')}}/shows/' + show.slug + '/feed', 'copy-feed-app')" id="copy-feed-app">Copy {{env('APP_NAME')}} feed</a>
                                    <input type="text" id="copy-feed-app-fallback" class="fallback" :value="'{{env('APP_URL')}}/shows/' + show.slug + '/feed'" v-tooltip title="Copy this text"/>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body panel-border-top">
                            <div class="row" v-if="show.feed">
                                <div class="col-xs-7">
                                    @{{show.total_likes}} Like@{{show && show.total_likes != '1' ? 's' : ''}}
                                </div>
                                <div class="col-xs-5 pull-right">
                                    <button class="btn-like btn btn-sm" v-if="user && user.verified && !show.this_user_likes" @click.prevent="likeShow"></button>
                                
                                    <button class="btn-unlike btn btn-sm" v-if="user && user.verified && !!show.this_user_likes" @click.prevent="unlikeShow"></button>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body panel-border-top">
                            <div class="row">
                                <div class="col-xs-7">
                                    @{{show.episodeCount}} Episode@{{show && show.episodeCount != '1' ? 's' : ''}}
                                </div>
                            </div>
                        </div>
                        <div class="panel-body panel-border-top">
                            @{{show.description}}
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-9 col-md-8">
                    <div class="panel panel-default panel-list-item episode-container" v-for="episode in displayEpisodes" :key="episode.slug" :data-slug="episode.slug">
                        <div class="panel-heading">
                            <a :href="'/episodes/' + episode.slug">
                                <img :src="episode.img_url" class="episode-image" />
                                <strong>@{{episode.name}}</strong>
                            </a>
                            <small v-tooltip :title="episode.pubdate_str">@{{episode.howLongAgo}}</small>
                        </div>
                        <div class="panel-body" v-html="episode.description"></div>
                        @include('partials.episode-footer')
                    </div>
                    <div class="panel panel-default panel-list-item clickable" v-if="show.episodes && show.episodeCount > show.episodes.length" @click="showMore">
                        <div class="panel-body centered">
                            Load more episodes...
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('modals.episode-modals')
    </div>
</show>
@endsection
