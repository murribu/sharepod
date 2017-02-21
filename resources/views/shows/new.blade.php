<shows-new inline-template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">
                Add a Podcast Show to {{env('APP_NAME')}}
            </div>
            <div class="panel-body">
                <label for="new-show-feed">Enter the Show's RSS feed <i class="fa fa-fw fa-btn fa-rss"></i></label>
                <div class="col-xs-12 col-sm-10" style="padding:0;">
                    <input type="text" id="new-show-feed" class="form-control"
                            name="feed"
                            v-model="feed"
                            placeholder="http://domain.com/feed.xml"
                            @keyup.enter="addShow">
                </div>
                <div class="col-xs-12 col-sm-2" style="padding:0;text-align:center;">
                    <button class="btn btn-primary" type="submit" style="width:100%;" @click="addShow">Add</button>
                </div>
            </div>
        </div>
        
        <div class="panel panel-default" v-if="processing">
            <div class="panel-heading">
                Adding...
            </div>
        </div>
        <div class="panel panel-default" v-if="!processing && error">
            <div class="panel-heading">
                Error
            </div>
            <div class="panel-body">
                @{{errorMessage}}
            </div>
        </div>
        <div class="panel panel-default" v-if="!processing && !error && newShow">
            <div class="panel-heading">
                Show Added!
            </div>
            <div class="panel-body">
                <a :href="'/shows/' + newShow.slug">@{{newShow.name}}</a> was successfully added!
            </div>
        </div>
    </div>
</shows-new>
