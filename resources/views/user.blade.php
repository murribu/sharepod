@extends('spark::layouts.app')

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.4.6/mousetrap.min.js"></script>
@endsection

@section('content')
<view-user :user="user" inline-template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-lg-3 col-md-4">
                    <div class="panel panel-default panel-flush show-profile-card">
                        <div class="panel-heading">
                            <a class="show-image-container">
                                <img :src="viewed_user.photo_url" class="show-image" />
                            </a>
                            @{{viewed_user.name}}
                        </div>
                        <div class="panel-body">
                            <div class="user-tabs">
                                <ul class="nav left-stacked-tabs" role="tablist">
                                    <li role="presentation">
                                        <a href="#episodes-liked" aria-controls="episodes-liked" role="tab" data-toggle="tab">
                                            876 Episodes Liked
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#shows-liked" aria-controls="shows-liked" role="tab" data-toggle="tab">
                                            12 Shows Liked
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-9 col-md-8">
                    <div class="tab-content">
                        <div class="tab-pane active" role="tabpanel" id="episodes-liked">
                            @include('user.episodes-liked')
                        </div>
                        <div class="tab-pane" role="tabpanel" id="shows-liked">
                            @include('user.shows-liked')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</view-user>
@endsection
