@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
    <div class="container">
        <div class="row centered">
            <h3>Shows</h3>
        </div>
            <div class="col-md-4 col-xs-1">
                <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @{{ test }}
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
