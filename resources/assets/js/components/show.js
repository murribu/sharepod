var episodeActions = require('./mixins/episode-actions');
var copyFeed = require('./mixins/copy-feed');

Vue.component('show', {
    props: ['user'],
    mixins: [episodeActions, copyFeed],
    data() {
        return {
            show: {},
            selectedEpisode: {},
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
                $("#modal-error").modal('show');
                setTimeout(function(){
                    $("#modal-error").modal('hide');
                }, 8000);
            });
        if (this.user){
            this.getRecentRecommendees();
            this.getPlaylists();
        }
    },
    methods: {
        likeShow() {
            var self = this;
            this.$http.post('/api/shows/like', {slug: this.show.slug})
                .then(response => {
                    self.updateShow(response.data.total_likes, response.data.this_user_likes);
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        unlikeShow() {
            var self = this;
            this.$http.post('/api/shows/unlike', {slug: this.show.slug})
                .then(response => {
                    self.updateShow(response.data.total_likes, response.data.this_user_likes);
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        showMore() {
            var self = this;
            this.$http.get('/api/shows/' + this.slug + '/episodes?pubdate=' + this.oldestPubdate)
                .then(response => {
                    self.show.episodes = self.show.episodes.concat(response.data);
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        updateShow(total_likes, this_user_likes){
            this.show.total_likes = total_likes;
            this.show.this_user_likes = this_user_likes;
        },
        updateEpisode(slug, stats){
            for(var e in this.show.episodes){
                if (this.show.episodes[e].slug == slug){
                    this.show.episodes[e].result_slug = stats.result_slug;
                    this.show.episodes[e].this_user_archived = stats.this_user_archived;
                    this.show.episodes[e].this_user_likes = stats.this_user_likes;
                    this.show.episodes[e].total_likes = stats.total_likes;
                    this.show.episodes[e].total_playlists = stats.total_playlists;
                    this.show.episodes[e].total_recommendations = stats.total_recommendations;
                    this.getLikers(this.episodes[e]);
                }
            }
        },
    }
});