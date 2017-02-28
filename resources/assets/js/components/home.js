var episodeActions = require('./mixins/episode-actions');

Vue.component('home', {
    props: ['user'],
    mixins: [episodeActions],
    data(){
        return {
            episodes:[],
            show: {},
            selectedEpisode: {},
        };
    },
    computed: {
        showGetStarted() {
            return !this.user || 
                !this.user.hasLikedSomething || 
                !this.user.hasRecommendedSomething || 
                (this.user.hasReceivedARecommendation && !this.user.hasTakenActionOnARecommendation) || 
                !this.user.hasRegisteredTheirFeed ||
                !this.user.hasCreatedAPlaylist;
        },
    },
    created(){
        this.loadPopularEpisodes();
        if (this.user){
            this.getRecentRecommendees();
            this.getPlaylists();
        }
    },
    methods:{
        loadPopularEpisodes() {
            var self = this;
            this.$http.get('/api/episodes/popular')
                .then(response => {
                    self.episodes = response.data;
                    for(var e in self.episodes){
                        $('.btn-archive-episode .icon-container').attr('data-original-title', e.this_user_archived ? 'Unarchive' : 'Archive');
                    }
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        getLikers(episode){
            var self = this;
            this.$http.get('/api/episodes/' + episode.slug + '/likers')
                .then(response => {
                    for(var e in self.episodes){
                        if (self.episodes[e].slug == episode.slug){
                            self.episodes[e].likers = response.data;
                        }
                    }
                }, response => {
                    //do nothing - nbd
                });
        },
        updateEpisode(slug, stats){
            for(var e in this.episodes){
                if (this.episodes[e].slug == slug){
                    this.episodes[e].result_slug = stats.result_slug;
                    this.episodes[e].this_user_archived = stats.this_user_archived;
                    this.episodes[e].this_user_likes = stats.this_user_likes;
                    this.episodes[e].total_likes = stats.total_likes;
                    this.episodes[e].total_playlists = stats.total_playlists;
                    this.episodes[e].total_recommendations = stats.total_recommendations;
                    this.getLikers(this.episodes[e]);
                    $('[data-slug=' + slug + '] .btn-archive-episode .icon-container').attr('data-original-title', stats.this_user_archived ? 'Unarchive' : 'Archive');
                    $('[data-slug=' + slug + '] .btn-episode-like .icon-container').attr('data-original-title', stats.this_user_likes ? 'Unlike' : 'Like');
                }
            }
        },
    }
});

