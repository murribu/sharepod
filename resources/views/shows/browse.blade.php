<shows-browse :user="user" inline-template>
    <div class="container">
        <div class="col-md-4 col-xs-12" v-for="show in shows">
            <div class="panel panel-default">
                <div class="panel-heading">@{{show.name}}</div>
                <div class="panel-body">
                    @{{show.description}}
                </div>
            </div>
        </div>
    </div>
</shows-browse>
