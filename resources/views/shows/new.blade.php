<shows-list :user="user" inline-template>
    <div class="container">
        <div class="col-md-4 col-xs-12">
            <div class="panel panel-default panel-flush" style="border: 0;">
                <div class="panel-body">
                    <form class="p-b-none" role="form" @submit.prevent>
                        <!-- XML Feed Field -->
                        <div class="form-group m-b-none">
                            <div class="col-xs-10" style="padding:0;">
                                <input type="text" id="new-show-feed" class="form-control"
                                        name="feed"
                                        placeholder="http://domain.com/feed.xml"
                                        @keyup.enter="addShow">
                            </div>
                            <div class="col-xs-2" style="padding:0;">
                                <button class="btn btn-primary" type="submit" style="float:right;" @click="addShow">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Feedback -->
            <div class="panel panel-default" v-if="feedback.length > 0">
                <div class="panel-heading">
                    Request Feedback
                </div>
                <div class="panel-body">
                    @{{feedback}}
                </div>
            </div>
        </div>
    </div>
</shows-list>
