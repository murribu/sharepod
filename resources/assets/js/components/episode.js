var episodeActions = require('./mixins/episode-actions');

Vue.component('episode', {
    props: ['user'],
    mixins: [episodeActions],
    data() {
        return {
            episode: {},
        }
    },
    computed: {
        slug() {
            return window.location.href.split('/')[4];
        },
    },
    created() {
        this.loadEpisode();
    },
    methods: {
        loadEpisode() {
            var self = this;
            this.$http.get('/api/episodes/' + this.slug)
                .then(response => {
                    self.episode = response.data;
                },
                response => {
                    // alert('error');
                });
        },
        updateEpisode(slug, total_likes, this_user_likes){
            this.episode.total_likes = total_likes;
            this.episode.this_user_likes = this_user_likes;
        },
    }
});