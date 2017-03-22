@extends('spark::layouts.app')

@section('content')
<show-edit :user="user" inline-template>
    <div>
        <div class="container">
            <div class="alert alert-danger" v-if="!user.canArchiveEpisodes">
                This isn't going to work. Your plan does not allow you to create a show. <br><a href="/settings#/subscription">Click here</a> to change your Plan.
            </div>
            <div class="alert alert-danger" v-if="user.hasReachedArchiveLimit">
                This isn't going to work. You have reached your plan's storage limit<br><a href="/settings#/subscription">Click here</a> to change your Plan. Or <a :href="'/users/' + user.slug">click here</a> to manage your storage
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="centered">{{isset($show) ? 'Edit' : 'New'}} Show</h3>
                </div>
                <div class="panel-body">
                    <form method="post">
                        {{csrf_field()}}
                        <div class="form-group row">
                            <label class="form-label control-label col-xs-12 col-sm-2" for="name">Name</label>
                            <div class="col-xs-12 col-sm-10">
                                <input class="form-control" name="name" value="{{isset($show) ? $show->name : ''}}" :disabled="!user.canArchiveEpisodes || user.hasReachedArchiveLimit" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="form-label control-label col-xs-12 col-sm-2" for="description">Description</label>
                            <div class="col-xs-12 col-sm-10">
                                <textarea class="form-control" name="description" :disabled="!user.canArchiveEpisodes || user.hasReachedArchiveLimit">{{isset($show) ? $show->description : ''}}</textarea>
                            </div>
                        </div>
                        <button class="btn btn-primary pull-right" type="submit" :disabled="!user.canArchiveEpisodes || user.hasReachedArchiveLimit">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</show-edit>
@endsection