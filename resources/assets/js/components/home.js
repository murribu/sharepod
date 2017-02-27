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
        updateEpisode(slug, total_recommendations, total_likes, total_playlists, this_user_likes, this_user_archived, result_slug){
            this.loadPopularEpisodes();
        },
    }
});

