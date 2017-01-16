@extends('spark::layouts.app')

@section('content')
<recommendation :user="user" inline-template>
    <div class="">
        <h3>From: @{{recommendation.recommender}}</h3>
        <h3>To: @{{recommendation.recommendee}}</h3>
    </div>
</recommendation>
@endsection