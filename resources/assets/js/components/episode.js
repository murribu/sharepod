var episodeActions = require('./mixins/episode-actions');

Vue.component('episode', {
    props: ['user'],
    mixins: [episodeActions],
    data() {
        return {
            episodeGroups: {
                one: {
                    episodes:{}
                },
                show: {},
            },
            selectedEpisode: {},
        }
    },
    computed: {
        slug() {
            return window.location.href.split('/')[4];
        },
        episode() {
            return this.episodeGroups.one.episodes[0];
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
                    self.episodeGroups.one.episodes = [response.data];
                    self.show = {
                        name: self.episodeGroups.one.episodes.show_name,
                        slug: self.episodeGroups.one.episodes.show_slug
                    };
                    for(var s in self.episodeGroups.one.episodes.stats){
                        self.episodeGroups.one.episodes[s] = self.episodeGroups.one.episodes.stats[s];
                    }
                },
                response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        updateEpisode(slug, stats){
            this.episodeGroups.one.episodes[0].result_slug = stats.result_slug;
            this.episodeGroups.one.episodes[0].this_user_archived = stats.this_user_archived;
            this.episodeGroups.one.episodes[0].this_user_likes = stats.this_user_likes;
            this.episodeGroups.one.episodes[0].total_likes = stats.total_likes;
            this.episodeGroups.one.episodes[0].total_playlists = stats.total_playlists;
            this.episodeGroups.one.episodes[0].total_recommendations = stats.total_recommendations;
            $('[data-slug=' + slug + '] .btn-archive-episode').attr('data-original-title', stats.this_user_archived ? 'Unarchive' : 'Archive');
            $('[data-slug=' + slug + '] .btn-episode-like').attr('data-original-title', stats.this_user_likes ? 'Unlike' : 'Like');
        },
    }
});