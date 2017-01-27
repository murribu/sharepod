Vue.component('playlist', {
    props: ['user'],
    data() {
        return {
            playlist: {
                name: '',
                description: '',
                episodes: [],
            },
            loaded: false,
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
        }
    }
});
