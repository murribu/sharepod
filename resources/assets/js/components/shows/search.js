Vue.component('shows-search', {
    props: ['user'],
    data() {
        return {
            shows: [],
            searchText: '',
            holdText: ''
        };
    },
    watch: {
        searchText: function(newText){
            this.holdText = 'Waiting for you to stop typing...';
            this.search();
        }
    },
    computed: {
        searchTextEncoded() {
            return encodeURIComponent(this.searchText + ' rss');
        }
    },
    methods: {
        search: _.debounce(function(){
            if (this.searchText != ''){
                this.holdText = 'Searching...';
                var self = this;
                this.$http.get('/api/shows/search?s=' + this.searchText)
                    .then(response => {
                        self.shows = response.data;
                        self.holdText = '';
                    }, response => {
                        $("#modal-error").modal('show');
                        setTimeout(function(){
                            $("#modal-error").modal('hide');
                        }, 8000);
                    })
            }
        }, 500)
    },
});
