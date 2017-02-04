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
                            <span v-for="u in r.users"><a :href="'/users/' + u.slug">@{{u.name}}</a></span>
                            recommended <a :href="'/shows/' + r.show_slug">@{{r.show_name}}</a> - <a :href="'/episodes/' + r.slug">@{{r.name}}</a>
                        </div>
                        <div class="col-xs-3">
                            <button class="btn btn-primary btn-accept-recommendation" v-tooltip title="Accept" @click.prevent="accept(r)" :disabled="updatePendingBusy">✔</button>
                            <button class="btn btn-danger btn-block-recommendation" v-tooltip title="Reject" @click.prevent="reject(r)" :disabled="updatePendingBusy">&times;</button>
                        </div>
                    </div>
                    <div class="panel-body" v-if="recommendations_pending_loaded && recommendations_pending.length == 0">
                        <div class="col-xs-12 centered">
                            You have no pending recommendations
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading centered">
                        Accepted
                    </div>
                    <div class="panel-body recommendation-list-item" v-for="r in recommendations_accepted">
                        <div class="col-xs-9">
                            <span v-for="u in r.users"><a :href="'/users/' + u.slug">@{{u.name}}</a></span>
                            recommended <a :href="'/shows/' + r.show_slug">@{{r.show_name}}</a> - <a :href="'/episodes/' + r.slug">@{{r.name}}</a>
                        </div>
                        <div class="col-xs-3">
                            <button class="btn btn-warning btn-pending-recommendation" v-tooltip title="Make Pending" @click.prevent="makePending(r)" :disabled="updateAcceptedBusy">/</button>
                            <button class="btn btn-danger btn-block-recommendation" v-tooltip title="Reject" @click.prevent="reject(r)" :disabled="updateAcceptedBusy">&times;</button>
                        </div>
                    </div>
                    <div class="panel-body" v-if="recommendations_accepted_loaded && recommendations_accepted.length == 0">
                        <div class="col-xs-12 centered">
                            You have no accepted recommendations
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</recommendations>
@endsection