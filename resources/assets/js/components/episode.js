var episodeActions = require('./mixins/episode-actions');

Vue.component('episode', {
    props: ['user'],
    mixins: [episodeActions],
    data() {
        return {
            selectedEpisode: {},
            show: false
        }
    },
    computed: {
        slug() {
            return window.location.href.split('/')[4];
        },
        episode() {
            return this.selectedEpisode;
        }
    },
    created() {
        this.loadEpisode();
        if (this.user){
            this.getRecentRecommendees();
            this.getPlaylists();
        }
    },
    methods: {
        loadEpisode() {
            var self = this;
            this.$http.get('/api/episodes/' + this.slug)
                .then(response => {
                    self.selectedEpisode = response.data;
                    self.show = {
                        name: self.selectedEpisode.show_name,
                        slug: self.selectedEpisode.show_slug
                    };
                },
                response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        updateEpisode(slug, stats){
            this.selectedEpisode.result_slug = stats.result_slug;
            this.selectedEpisode.this_user_archived = stats.this_user_archived;
            this.selectedEpisode.this_user_likes = stats.this_user_likes;
            this.selectedEpisode.total_likes = stats.total_likes;
            this.selectedEpisode.total_playlists = stats.total_playlists;
            this.selectedEpisode.total_recommendations = stats.total_recommendations;
            $('.btn-archive-episode .icon-container').attr('data-original-title', this_user_archived ? 'Unarchive' : 'Archive');
        },
    }
});