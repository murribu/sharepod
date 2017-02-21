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
        
        <!-- Feedback -->
        <div class="panel panel-default" v-if="processing || feedback.length > 0">
            <div class="panel-heading">
                <span v-if="processing">Adding...</span>
                <span v-if="!processing && error">Error</span>
                <span v-if="!processing && !error">Show Added!</span>
            </div>
            <div class="panel-body">
                @{{feedback}}
            </div>
        </div>
    </div>
</shows-new>
