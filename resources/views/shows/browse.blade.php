<shows-browse :user="user" inline-template>
    <div>
        <div class="row" v-for="show in shows">
            <a class="col-xs-12 col-md-8 col-md-offset-2 clickable" :href="'/shows/' + show.slug">
                <div class="panel panel-default">
                    <div class="panel-heading">@{{show.name}}</div>
                    <div class="panel-body">
                        @{{show.description}}
                    </div>
                </div>
            </a>
        </div>
    </div>
</shows-browse>
