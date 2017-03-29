var episodeActions = require('./mixins/episode-actions');

Vue.component('home', {
    props: ['user'],
    mixins: [episodeActions],
    data(){
        return {
            episodeGroups: {
                popular: {
                    episodes:[]
                },
                show: {},
            },
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
            axios.get('/api/episodes/popular')
                .then(response => {
                    self.episodeGroups.popular.episodes = response.data;
                    for(var e in self.episodeGroups.popular.episodes){
                        self.episodeGroups.popular.episodes[e].like_busy = false;
                        self.episodeGroups.popular.episodes[e].archive_busy = false;
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
            axios.get('/api/episodes/' + episode.slug + '/likers')
                .then(response => {
                    for(var e in self.episodeGroups.popular.episodes){
                        if (self.episodeGroups.popular.episodes[e].slug == episode.slug){
                            self.episodeGroups.popular.episodes[e].likers = response.data;
                        }
                    }
                }, response => {
                    //do nothing - nbd
                });
        },
        updateEpisode(slug, stats){
            for(var e in this.episodeGroups.popular.episodes){
                if (this.episodeGroups.popular.episodes[e].slug == slug){
                    this.episodeGroups.popular.episodes[e].result_slug = stats.result_slug;
                    this.episodeGroups.popular.episodes[e].this_user_archived = stats.this_user_archived;
                    this.episodeGroups.popular.episodes[e].this_user_likes = stats.this_user_likes;
                    this.episodeGroups.popular.episodes[e].total_likes = stats.total_likes;
                    this.episodeGroups.popular.episodes[e].total_playlists = stats.total_playlists;
                    this.episodeGroups.popular.episodes[e].total_recommendations = stats.total_recommendations;
                    this.getLikers(this.episodeGroups.popular.episodes[e]);
                    $('[data-slug=' + slug + '] .btn-archive-episode').attr('data-original-title', stats.this_user_archived ? 'Unarchive' : 'Archive');
                    $('[data-slug=' + slug + '] .btn-episode-like').attr('data-original-title', stats.this_user_likes ? 'Unlike' : 'Like');
                }
            }
        },
    }
});

