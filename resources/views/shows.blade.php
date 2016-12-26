@extends('spark::layouts.app')

@section('content')
<shows :user="user" inline-template>
    <div class="container">
        <div class="row centered">
            <h3>Shows</h3>
        </div>
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Nerdist</div>

                <div class="panel-body">
                    Nerdist description
                </div>
            </div>
        </div>
    </div>
</shows>
@endsection
