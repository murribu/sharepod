@extends('spark::layouts.app')

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.4.6/mousetrap.min.js"></script>
    <script src="/js/lodash.custom.min.js"></script>
@endsection

@section('content')
<help :user="user" inline-template>
    <div class="container-fluid">
        <div class="row">
            <!-- Tabs -->
            <div class="col-md-4">
                <div class="panel panel-default panel-flush">
                    <div class="panel-heading">
                        Help
                    </div>

                    <div class="panel-body">
                        <div class="help-tabs">
                            <ul class="nav left-stacked-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#register-my-feed" aria-controls="register-my-feed" role="tab" data-toggle="tab">
                                        <i class="fa fa-fw fa-btn fa-registered"></i>Register My Feed
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#find-a-podcatcher" aria-controls="find-a-podcatcher" role="tab" data-toggle="tab">
                                        <i class="fa fa-fw fa-btn fa-search"></i>Find a podcatcher
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
                    <div role="tabpanel" class="tab-pane active" id="register-my-feed">
                        @include('help.register-my-feed')
                    </div>

                    <div role="tabpanel" class="tab-pane" id="find-a-podcatcher">
                        @include('help.find-a-podcatcher')
                    </div>

                </div>
            </div>
        </div>
    </div>
</help>
@endsection