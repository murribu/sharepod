var Events = new Vue({});

require('./shows/search');
require('./shows/new');

Vue.component('shows', {
    props: ['user'],
    mixins: [require('./../../../../spark/resources/assets/js/mixins/tab-state')],
    mounted() {
        this.usePushStateForTabs('.shows-tabs');
    },
    methods: {
        refreshList() {
            Events.$emit('refreshlist');
        }
    }
});

Vue.component('shows-browse', {
    props: ['user'],
    data() {
        return {
            shows: {},
            categories: [],
            selectedCategory: 'All',
            processing: false,
            error: false,
        };
    },
    mounted() {
        Events.$on('refreshlist', () => {
            this.refreshList();
        });
    },
    created() {
        this.refreshList();
    },
    methods: {
        refreshList() {
            var self = this;
            
            this.processing = true;
            this.error = false;
            axios.get('api/shows')
                .then(response => {
                    self.processing = false;
                    self.shows = response.data.shows;
                    self.categories = response.data.categories;
                }, response => {
                    self.processing = false;
                    self.error = true;
                });
        }
    }
});
