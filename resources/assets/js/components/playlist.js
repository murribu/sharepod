var copyFeed = require('./mixins/copy-feed');

Vue.component('playlist', {
    props: ['user'],
    mixins: [copyFeed],
    data() {
        return {
            playlist: {
                name: '',
                description: '',
                episodes: [],
            },
            copyLinkText: 'Click here to copy RSS Feed URL',
            loaded: false,
            areYouSure: {
                busy: false,
                episode_slug: ''
            }
        };
    },
    created() {
        this.loadPlaylist();
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
                    self.playlist.episodes = response.data;
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
                    self.playlist.episodes = response.data;
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
                    self.playlist.episodes = response.data;
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
                    self.playlist.episodes = response.data;
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
                    self.playlist.episodes = response.data;
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
                    self.playlist = response.data;
                    self.loaded = true;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
    }
});
