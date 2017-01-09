Vue.component('show', {
    props: ['user'],
    data() {
        return {
            show: {},
        };
    },
    computed: {
        slug() {
            return window.location.href.split('/')[4];
        },
        oldestPubdate() {
            var ret = '9999999999';
            if (this.show.episodes){
                for(var e in this.show.episodes){
                    if (this.show.episodes[e].pubdate < ret){
                        ret = this.show.episodes[e].pubdate;
                    }
                }
            }
            
            return ret;
        },
        displayEpisodes() {
            if (this.show.episodes){
                return this.show.episodes.sort(function(a, b){
                    return a.pubdate < b.pubdate ? 1 : -1;
                });
            }else{
                return [];
            }
        }
    },
    created() {
        var self = this;
        this.$http.get('/api/shows/' + this.slug)
            .then(response => {
                self.show = response.data;
            },
            response => {
                // alert('error');
            });
    },
    methods: {
        likeEpisode() {
            
        },
        showMore() {
            var self = this;
            this.$http.get('/api/shows/' + this.slug + '/episodes?pubdate=' + this.oldestPubdate)
                .then(response => {
                    self.show.episodes = self.show.episodes.concat(response.data);
                }, response => {
                    // alert('error');
                });
        }
    }
});

