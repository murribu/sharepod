Vue.component('playlist', {
    props: ['user'],
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
                    // alert('error');
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
                    // alert('error');
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
                    // alert('error');
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
                    // alert('error');
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
                    // alert('error');
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
                    // alert('error');
                });
        },
        copyFeed(){
            var textArea = document.createElement("input");
            var feedUrl = window.location.href.split('/')[0]
                + '//' + window.location.href.split('/')[2]
                + '/playlists/' + this.slug + '/feed';
            textArea.style.position = 'fixed';
            textArea.style.top = 0;
            textArea.style.left = 0;
            textArea.style.width = '2em';
            textArea.style.height = '2em';
            textArea.style.padding = 0;
            textArea.style.border = 'none';
            textArea.style.outline = 'none';
            textArea.style.boxShadow = 'none';
            textArea.style.background = 'transparent';
            textArea.value = feedUrl;
            
            document.body.appendChild(textArea);
            
            textArea.select();
            
            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
                if (successful){
                    this.copyLinkText = 'Copied!'
                }else{
                    this.copyLinkText = 'There was a problem. The copy didn\'t work.';
                }
            } catch (err) {
                console.log('Oops, unable to copy');
                this.copyLinkText = 'There was a problem. The copy didn\'t work.';
            }
            
            document.body.removeChild(textArea);
            var self = this;
            setTimeout(function(){
                self.copyLinkText = 'Click here to copy RSS Feed URL';
            }, 2500);
        }
    }
});
