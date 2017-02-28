var episodeActions = require('./mixins/episode-actions');
var copyFeed = require('./mixins/copy-feed');

Vue.component('show', {
    props: ['user'],
    mixins: [episodeActions, copyFeed],
    data() {
        return {
            episodeGroups: {
                show: {
                    episodes: []
                }
            },
            selectedEpisode: {},
        };
    },
    computed: {
        slug() {
            return window.location.href.split('/')[4];
        },
        oldestPubdate() {
            var ret = '9999999999';
            if (this.episodeGroups.show.episodes){
                for(var e in this.episodeGroups.show.episodes){
                    if (this.episodeGroups.show.episodes[e].pubdate < ret){
                        ret = this.episodeGroups.show.episodes[e].pubdate;
                    }
                }
            }
            
            return ret;
        },
        displayEpisodes() {
            if (this.episodeGroups.show.episodes){
                return this.episodeGroups.show.episodes.sort(function(a, b){
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
                self.episodeGroups.show = response.data;
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
            this.$http.post('/api/shows/like', {slug: this.episodeGroups.show.slug})
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
            this.$http.post('/api/shows/unlike', {slug: this.episodeGroups.show.slug})
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
                    self.episodeGroups.show.episodes = self.episodeGroups.show.episodes.concat(response.data);
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        updateShow(total_likes, this_user_likes){
            this.episodeGroups.show.total_likes = total_likes;
            this.episodeGroups.show.this_user_likes = this_user_likes;
        },
        updateEpisode(slug, stats){
            for(var e in this.episodeGroups.show.episodes){
                if (this.episodeGroups.show.episodes[e].slug == slug){
                    this.episodeGroups.show.episodes[e].result_slug = stats.result_slug;
                    this.episodeGroups.show.episodes[e].this_user_archived = stats.this_user_archived;
                    this.episodeGroups.show.episodes[e].this_user_likes = stats.this_user_likes;
                    this.episodeGroups.show.episodes[e].total_likes = stats.total_likes;
                    this.episodeGroups.show.episodes[e].total_playlists = stats.total_playlists;
                    this.episodeGroups.show.episodes[e].total_recommendations = stats.total_recommendations;
                    $('[data-slug=' + slug + '] .btn-archive-episode').attr('data-original-title', stats.this_user_archived ? 'Unarchive' : 'Archive');
                    $('[data-slug=' + slug + '] .btn-episode-like').attr('data-original-title', stats.this_user_likes ? 'Unlike' : 'Like');
                }
            }
        },
    }
});