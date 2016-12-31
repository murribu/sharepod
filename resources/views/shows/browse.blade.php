<shows-browse :user="user" inline-template>
    <div class="container">
        <a class="col-md-4 col-xs-12 clickable" v-for="show in shows" :href="'/shows/' + show.slug">
            <div class="panel panel-default">
                <div class="panel-heading">@{{show.name}}</div>
                <div class="panel-body">
                    @{{show.description}}
                </div>
            </div>
        </a>
    </div>
</shows-browse>
