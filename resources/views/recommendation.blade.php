@extends('spark::layouts.app')

@section('content')
<recommendation :user="user" inline-template>
    <div class="">
        <h3>From: <a :href="'/users/' + recommendation.recommender_slug">@{{recommendation.recommender}}</a></h3>
        <h3>From: <a :href="'/users/' + recommendation.recommendee_slug">@{{recommendation.recommendee}}</a></h3>
        <h3>Episode: <a :href="'/episodes/' + recommendation.episode_slug">@{{recommendation.episode_name}}</a></h3>
        <h3>Comment: @{{recommendation.comment}}</h3>
    </div>
</recommendation>
@endsection