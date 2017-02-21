@extends('spark::layouts.app')

@section('content')
<recommendation :user="user" inline-template>
    <div class="container">
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="centered">Recommendation</h3>
                </div>
                <div class="panel-body centered" v-if="recommendation_loaded">
                    <h4>
                        <a :href="'/users/' + recommendation.recommender_slug">@{{recommendation.recommender_slug == user.slug ? 'You' : recommendation.recommender}}</a> @{{recommendation.recommender_slug == user.slug ? 'have' : 'has'}} recommended an episode to <a :href="'/users/' + recommendation.recommendee_slug">@{{recommendation.recommendee_slug == user.slug ? 'You' : recommendation.recommendee}}</a><br><br>
                        <a :href="'/episodes/' + recommendation.episode_slug">@{{recommendation.episode_name}}</a><span v-if="recommendation.show_slug"> from <a :href="'/shows/' + recommendation.show_slug">@{{recommendation.show_name}}</a></span>
                    </h4>
                </div>
                <div class="panel-footer" v-if="recommendation_loaded">
                    <div class="row">
                        <div class="col-xs-12">
                            <h3 class="centered" v-if="recommendation.action == null || recommendation.action == 'viewed'">Its status is currently pending</h3>
                            <h3 class="centered" v-if="recommendation.action == 'accepted'">It has been accepted</h3>
                            <h3 class="centered" v-if="recommendation.action == 'rejected'">It has been rejected</h3>
                        </div>
                    </div>
                    <div class="row" v-if="recommendation.recommendee_slug == user.slug">
                        <div class="col-xs-4 centered">
                            <button class="btn btn-primary" :disabled="recommendation.action == 'accepted' || busy" @click.prevent="accept">Accept</button>
                        </div>
                        <div class="col-xs-4 centered">
                            <button class="btn btn-danger" :disabled="recommendation.action == 'rejected' || busy" @click.prevent="reject">Reject</button>
                        </div>
                        <div class="col-xs-4 centered">
                            <button class="btn btn-warning" :disabled="recommendation.action == null || recommendation.action == 'viewed' || busy" @click.prevent="makePending">Make Pending</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <br><a href="/recommendations"><i class="fa fa-fw fa-btn fa-arrow-left"></i> Manage my Recommendations</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="recommendation.action == 'accepted'">
            <div class="panel panel-default">
                <div class="panel-heading centered">
                    <h4 class="centered">Now what?</h4>
                </div>
                <div class="panel-body centered">
                    <div v-if="user.hasRegisteredTheirFeed">
                        If you have <a href="/help#/register-my-feed">registered your feed</a>, this episode will automatically show up!
                    </div>
                    <div v-if="!user.hasRegisteredTheirFeed">
                        Once you <a href="/help#/register-my-feed">registered your feed</a>, this episode will automatically show up in your podcast listening app!
                    </div>
                </div>
            </div>
        </div>
    </div>
</recommendation>
@endsection