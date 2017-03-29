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
        if (this.user){
            this.loadUserPlaylists();
        }
        this.loadPopularPlaylists();
    },
    methods: {
        addPlaylist(){
            if (this.user){
                if (this.user.canAddAPlaylist){
                    window.location.href = '/playlists/new';
                }else{
                    $("#modal-max-playlists").modal('show');
                }
            }
        },
        loadUserPlaylists() {
            var self = this;
            this.userPlaylistsLoaded = false;
            axios.get('/api/playlists')
                .then(response => {
                    self.userPlaylists = response.data;
                    self.userPlaylistsLoaded = true;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        loadPopularPlaylists() {
            var self = this;
            this.popularPlaylistsLoaded = false;
            axios.get('/api/playlists/popular')
                .then(response => {
                    self.popularPlaylists = response.data;
                    self.popularPlaylistsLoaded = true;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        }
    },
});