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
        updateEpisode(slug, total_likes, this_user_likes){
            this.selectedEpisode.total_likes = total_likes;
            this.selectedEpisode.this_user_likes = this_user_likes;
        },
    }
});