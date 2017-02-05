<shows-browse :user="user" inline-template>
    <div>
        <div class="categories-tabs">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" v-for="c in categories" :class="{'active': selectedCategory == c}">
                    <a href="#" @click.prevent="selectedCategory = c">@{{c}}</a>
                </li>
            </ul>
        </div>
        <div v-for="c in categories" v-if="selectedCategory == c">
            <div class="row" v-for="show in shows[c]">
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
    </div>
</shows-browse>
