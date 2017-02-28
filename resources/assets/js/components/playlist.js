var episodeActions = require('./mixins/episode-actions');
var copyFeed = require('./mixins/copy-feed');

Vue.component('playlist', {
    props: ['user'],
    mixins: [episodeActions, copyFeed],
    data() {
        return {
            episodeGroups: {
                playlist: {
                    name: '',
                    description: '',
                    episodes: [],
                },
                show: {},
            },
            copyLinkText: 'Click here to copy RSS Feed URL',
            loaded: false,
            areYouSure: {
                busy: false,
                episode_slug: ''
            },
            selectedEpisode: {},
        };
    },
    created() {
        this.loadPlaylist();
        if (this.user){
            this.getRecentRecommendees();
            this.getPlaylists();
        }
    },
    computed: {
        slug() {
            return window.location.href.split('/')[4];
        },
        feedUrl() {
            return window.location.href.split('/')[0] 
                + '//' + window.location.href.split('/')[2] + '/playlists/' 
                + this.slug + '/feed';
        }
    },
    methods: {
        noNeverMind(){
            $('#modal-are-you-sure').modal('hide');
            this.areYouSure.busy = false;
            this.areYouSure.episode_slug = '';
        },
        remove(episode){
            $('#modal-are-you-sure').modal('show');
            this.areYouSure.busy = false;
            this.areYouSure.episode_slug = episode.slug;
        },
        moveToTop(episode){
            var self = this;
            this.loaded = false;
            var sent = {
                slug: episode.slug
            };
            this.$http.post('/api/playlists/' + this.slug + '/move_to_top', sent)
                .then(response => {
                    self.episodeGroups.playlist.episodes = response.data;
                    self.loaded = true;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        moveUp(episode){
            var self = this;
            this.loaded = false;
            var sent = {
                slug: episode.slug
            };
            this.$http.post('/api/playlists/' + this.slug + '/move_up', sent)
                .then(response => {
                    self.episodeGroups.playlist.episodes = response.data;
                    self.loaded = true;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        moveDown(episode){
            var self = this;
            this.loaded = false;
            var sent = {
                slug: episode.slug
            };
            this.$http.post('/api/playlists/' + this.slug + '/move_down', sent)
                .then(response => {
                    self.episodeGroups.playlist.episodes = response.data;
                    self.loaded = true;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        moveToBottom(episode){
            var self = this;
            this.loaded = false;
            var sent = {
                slug: episode.slug
            };
            this.$http.post('/api/playlists/' + this.slug + '/move_to_bottom', sent)
                .then(response => {
                    self.episodeGroups.playlist.episodes = response.data;
                    self.loaded = true;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        yesImSureRemoveEpisode(){
            $('#modal-are-you-sure').modal('hide');
            this.areYouSure.busy = true;
            var self = this;
            this.loaded = false;
            var sent = {
                slug: this.areYouSure.episode_slug
            };
            this.$http.post('/api/playlists/' + this.slug + '/remove', sent)
                .then(response => {
                    this.areYouSure.busy = false;
                    self.episodeGroups.playlist.episodes = response.data;
                    self.loaded = true;
                }, response => {
                    this.areYouSure.busy = false;
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        loadPlaylist() {
            var self = this;
            this.loaded = false;
            this.$http.get('/api/playlists/' + this.slug)
                .then(response => {
                    self.episodeGroups.playlist = response.data;
                    self.loaded = true;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        updateEpisode(slug, stats){
            for(var e in this.episodeGroups.playlist.episodes){
                if (this.episodeGroups.playlist.episodes[e].slug == slug){
                    this.episodeGroups.playlist.episodes[e].result_slug = stats.result_slug;
                    this.episodeGroups.playlist.episodes[e].this_user_archived = stats.this_user_archived;
                    this.episodeGroups.playlist.episodes[e].this_user_likes = stats.this_user_likes;
                    this.episodeGroups.playlist.episodes[e].total_likes = stats.total_likes;
                    this.episodeGroups.playlist.episodes[e].total_playlists = stats.total_playlists;
                    this.episodeGroups.playlist.episodes[e].total_recommendations = stats.total_recommendations;
                    $('[data-slug=' + slug + '] .btn-archive-episode').attr('data-original-title', stats.this_user_archived ? 'Unarchive' : 'Archive');
                    $('[data-slug=' + slug + '] .btn-episode-like').attr('data-original-title', stats.this_user_likes ? 'Unlike' : 'Like');
                }
            }
        },
    }
});
