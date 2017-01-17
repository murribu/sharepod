@extends('spark::layouts.app')

@section('content')
<connections :user="user" inline-template>
    <div class="container">
        Connections
    </div>
</connections>
@endsection