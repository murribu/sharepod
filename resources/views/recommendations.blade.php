@extends('spark::layouts.app')

@section('content')
<recommendations :user="user" inline-template>
    <div class="container">
        <h3 style="text-align:center">Recommendations</h3>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading centered">
                        Pending
                    </div>
                    <div class="panel-body recommendation-list-item" v-for="r in recommendations_pending">
                        <div class="col-xs-9">
                            <span v-for="u in r.users"><a href="'/users/' + u.slug">@{{u.name}}</a></span>
                            recommended <a :href="'/episodes/' + r.slug">@{{r.name}}</a>
                        </div>
                        <div class="col-xs-3">
                            <button class="btn btn-primary btn-approve-recommendation" v-tooltip title="Approve" @click.prevent="" :disabled="updateBusy">✔</button>
                            <button class="btn btn-danger btn-block-recommendation" title="Reject" @click.prevent="" :disabled="updateBusy">&times;</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading centered">
                        Given<span v-if="recommendations_given_count > 0">&nbsp;(@{{recommendations_given_count}})</span>
                    </div>
                    <div class="panel-body recommendation-list-item" v-for="r in recommendations_given" @click="gotoRecommendation(r)">
                        <div class="col-xs-8">
                            @{{r.episode_name}}
                        </div>
                        <div class="col-xs-4">
                            @{{r.user_name}}
                        </div>
                    </div>
                    <div class="panel-body recommendation-list-item" v-if="recommendations_given_count > recommendations_given.length" @click="loadRecommendationsGiven">
                        <div class="col-xs-12 centered">
                            Show more
                        </div>
                    </div>
                    <div class="panel-body recommendation-list-item" v-if="recommendations_given_loaded && recommendations_given.length == 0" style="cursor:initial;">
                        <div class="col-xs-12 centered">
                            None Given Yet
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading centered">
                        Received
                    </div>
                    <div class="panel-body recommendation-list-item" v-for="r in recommendations_received" @click="gotoRecommendation(r)">
                        <div class="col-xs-8">
                            @{{r.episode_name}}
                        </div>
                        <div class="col-xs-4">
                            @{{r.user_name}}
                        </div>
                    </div>
                    <div class="panel-body recommendation-list-item" v-if="recommendations_received_count > recommendations_received.length" @click="loadRecommendationsReceived">
                        <div class="col-xs-12 centered">
                            Show more
                        </div>
                    </div>
                    <div class="panel-body recommendation-list-item" v-if="recommendations_received_loaded && recommendations_received.length == 0" style="cursor:initial;">
                        <div class="col-xs-12 centered">
                            None Received Yet
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</recommendations>
@endsection