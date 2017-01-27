Vue.component('playlists', {
    props: ['user'],
    data() {
        return {
            userPlaylists: [],
            userPlaylistsLoaded: false,
            popularPlaylists: [],
            popularPlaylistsLoaded: false,
        };
    },
    created() {
        this.loadUserPlaylists();
        this.loadPopularPlaylists();
    },
    methods: {
        loadUserPlaylists() {
            var self = this;
            this.userPlaylistsLoaded = false;
            this.$http.get('/api/playlists')
                .then(response => {
                    self.userPlaylists = response.data;
                    self.userPlaylistsLoaded = true;
                }, response => {
                    // alert('error');
                });
        },
        loadPopularPlaylists() {
            var self = this;
            this.popularPlaylistsLoaded = false;
            this.$http.get('/api/playlists/popular')
                .then(response => {
                    self.popularPlaylists = response.data;
                    self.popularPlaylistsLoaded = true;
                }, response => {
                    // alert('error');
                });
        }
    },
});

//document.execCommand('copy')