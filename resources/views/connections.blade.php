@extends('spark::layouts.app')

@section('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
@endsection

@section('content')
<connections :user="user" inline-template>
    <div>
        <div class="container">
            <h3 class="centered">Connections</h3>
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading centered">
                            Pending<span class="hover-help" title="When these folks recommend an episode to you, you must approve it before it is added to your feed.">?</span>
                        </div>
                        <div class="panel-body" v-for="c in pending_connections.received">
                            <div class="col-xs-8">
                                <a :href="'/users/' + c.recommender_slug">@{{c.recommender_name}}</a>
                            </div>
                            <div class="col-xs-4">
                                <button class="btn btn-primary btn-approve-connection" v-tooltip title="Approve" @click.prevent="showAreYouSure(areYouSure.approveMsg, 'approve', c)" :disabled="updateBusy">✔</button>
                                <button class="btn btn-danger btn-block-connection" title="Block" @click.prevent="showAreYouSure(areYouSure.blockMsg, 'block', c)" :disabled="updateBusy">&times;</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading centered">
                            Approved<span class="hover-help" title="When these folks recommend an episode to you, it is automatically added to your feed.">?</span>
                        </div>
                        <div class="panel-body" v-for="c in approved_connections.received">
                            <div class="col-xs-8">
                                <a :href="'/users/' + c.recommender_slug">@{{c.recommender_name}}</a>
                            </div>
                            <div class="col-xs-4">
                                <button class="btn btn-warning btn-pending-connection" v-tooltip title="Move to Pending" @click.prevent="showAreYouSure(areYouSure.pendingMsg, 'pending', c)" :disabled="updateBusy">/</button>
                                <button class="btn btn-danger btn-block-connection" title="Block" @click.prevent="showAreYouSure(areYouSure.blockMsg, 'block', c)" :disabled="updateBusy">&times;</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading centered">
                            Blocked<span class="hover-help" title="These folks are not allowed to add episodes to your feed.">?</span>
                        </div>
                        <div class="panel-body" v-for="c in blocked_connections.received">
                            <div class="col-xs-8">
                                <a :href="'/users/' + c.recommender_slug">@{{c.recommender_name}}</a>
                            </div>
                            <div class="col-xs-4">
                                <button class="btn btn-warning btn-pending-connection" v-tooltip title="Move to Pending" @click.prevent="showAreYouSure(areYouSure.pendingMsg, 'pending', c)" :disabled="updateBusy">/</button>
                                <button class="btn btn-primary btn-approve-connection" title="Approve" @click.prevent="showAreYouSure(areYouSure.approveMsg, 'approve', c)" :disabled="updateBusy">✔</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-are-you-sure" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Are you sure?</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            @{{areYouSure.displayMsg}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" @click="noNeverMind">No, Never mind</button>

                        <button type="button" class="btn btn-primary" @click="yesImSure" :disabled="areYouSure.busy">
                            Yes
                        </button>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</connections>
@endsection