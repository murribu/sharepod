@extends('spark::layouts.app')

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.4.6/mousetrap.min.js"></script>
    <script src="/js/lodash.custom.min.js"></script>
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
                                <li role="presentation">
                                    <a href="#browse" aria-controls="browse" role="tab" data-toggle="tab" @click="refreshList()">
                                        <i class="fa fa-fw fa-btn fa-folder-open"></i>Browse
                                    </a>
                                </li>
                                <li role="presentation" class="active">
                                    <a href="#search" aria-controls="search" role="tab" data-toggle="tab">
                                        <i class="fa fa-fw fa-btn fa-search"></i>Search
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#new" aria-controls="new" role="tab" data-toggle="tab">
                                        <i class="fa fa-fw fa-btn fa-plus-square"></i>New
                                    </a>
                                </li>
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

                    <!-- User Management -->
                    <div role="tabpanel" class="tab-pane" id="new">
                        @include('shows.new')
                    </div>
                </div>
            </div>
        </div>
    </div>
</shows>
@endsection