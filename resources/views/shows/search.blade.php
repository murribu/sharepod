<shows-search :user="user" inline-template>
    <div>
        <div class="row">
            <div class="col-xs-12 col-md-8">
                <input class="form-control" v-model="searchText" placeholder="search..." style="margin-bottom:20px" />
            </div>
        </div>
        <div class="row" v-if="holdText != '' && searchText != ''">
            <div class="col-xs-12">
                @{{holdText}}
            </div>
        </div>
        <div class="row" v-for="show in shows">
            <a class="col-xs-12 col-md-8 clickable" :href="'/shows/' + show.slug">
                <div class="panel panel-default">
                    <div class="panel-heading">@{{show.name}}</div>
                    <div class="panel-body">
                        @{{show.description}}
                    </div>
                </div>
            </a>
        </div>
        <div class="row" v-if="searchText != '' && holdText == '' && shows.length == 0">
            <div class="col-xs-12">
                No results. <a target="_new" href="https://twitter.com/{{env('TWITTER_HANDLE')}}">Contact us on twitter</a> to tell us about a show.
            </div>
        </div>
    </div>
</shows-search>
