@extends('spark::layouts.app')

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.4.6/mousetrap.min.js"></script>
@endsection

@section('content')
<shows :user="user" inline-template>
    <div class="container-fluid">
        <div class="row">
            <!-- Tabs -->
            <div class="col-md-4">
                <div class="panel panel-default panel-flush">
                    <div class="panel-heading">
                        Shows
                    </div>

                    <div class="panel-body">
                        <div class="shows-tabs">
                            <ul class="nav left-stacked-tabs" role="tablist">
                                <!-- Search Link -->
                                <li role="presentation" class="active">
                                    <a href="#search" aria-controls="search" role="tab" data-toggle="tab">
                                        <i class="fa fa-fw fa-btn fa-bullhorn"></i>Search
                                    </a>
                                </li>

                                <!-- Browse Link -->
                                <li role="presentation">
                                    <a href="#browse" aria-controls="browse" role="tab" data-toggle="tab" @click="refreshList()">
                                        <i class="fa fa-fw fa-btn fa-bar-chart"></i>Browse
                                    </a>
                                </li>

                                @if (Auth::user() && Spark::developer(Auth::user()->email))
                                <!-- Users Link -->
                                <li role="presentation">
                                    <a href="#new" aria-controls="new" role="tab" data-toggle="tab">
                                        <i class="fa fa-fw fa-btn fa-user"></i>New
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Panels -->
            <div class="col-md-8">
                <div class="tab-content">
                    <!-- Announcements -->
                    <div role="tabpanel" class="tab-pane active" id="search">
                        @include('shows.search')
                    </div>

                    <!-- Metrics -->
                    <div role="tabpanel" class="tab-pane" id="browse">
                        @include('shows.browse')
                    </div>

                    @if (Auth::user() && Spark::developer(Auth::user()->email))
                    <!-- User Management -->
                    <div role="tabpanel" class="tab-pane" id="new">
                        @include('shows.new')
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</shows>
@endsection