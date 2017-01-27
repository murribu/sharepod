module.exports = {
    data() {
        return {
            recommendToEmailAddress: '',
            recommendForm: {
                busy: false
            },
            recentRecommendees: [],
            recommendEmail: '',
            recommendTwitter: '',
            playlists: [],
            selectedPlaylist: {}
        };
    },
    methods: {
        getPlaylists() {
            var self = this;
            this.$http.get('/api/playlists')
                .then(response => {
                    self.playlists = response.data;
                }, response => {
                    // alert('error');
                });
        },
        selectEpisodeForAddingToPlaylist(episode){
            this.selectedEpisode = episode;
            if (this.playlists.length == 0){
                $("#modal-no-playlists").modal('show');
            }else if (this.playlists.length == 1){
                this.addSelectedEpisodeToPlaylist(this.playlists[0]);
            }else{
                $("#modal-select-playlist").modal('show');
            }
        },
        addSelectedEpisodeToPlaylist(playlist){
            var self = this;
            var sent = {
                slug: this.selectedEpisode.slug,
            };
            this.selectedPlaylist = playlist;
            this.$http.post('/api/playlists/' + playlist.slug + '/add_episode', sent)
                .then(response => {
                    $("#modal-add-to-playlist-success").modal('show');
                    setTimeout(function(){
                        $("#modal-add-to-playlist-success").modal('hide');
                    }, 7000);
                }, response => {
                    // alert('error');
                })
        },
        getRecentRecommendees(){
            var self = this;
            this.$http.get('/api/recent_recommendees')
                .then(response => {
                    self.recentRecommendees = response.data;
                }, response => {
                    // alert('error');
                })
        },
        recommendEpisodeToExistingUser(user_slug) {
            this.recommendForm.busy = true;
            this.$http.post('/recommend', {slug: this.selectedEpisode.slug, user_slug: user_slug})
                .then(response => {
                    this.recommendForm.busy = false;
                    this.showSuccessModal();
                    this.getRecentRecommendees();
                }, response => {
                    // alert('error');
                });
        },
        recommendEpisode(episode) {
            this.selectedEpisode = episode;
            if (this.recentRecommendees.length == 0){
                $('#modal-recommend-episode-2').modal('show');
            }else{
                $('#modal-recommend-episode-1').modal('show');
            }
        },
        recommendEpisodeToSomeoneElse(){
            $('#modal-recommend-episode-1').modal('hide');
            $('#modal-recommend-episode-2').modal('show');
        },
        sendRecommendation(){
            this.$http.post('/recommend', {slug: this.selectedEpisode.slug, email_address: this.recommendEmail, twitter_handle: this.recommendTwitter})
                .then(response => {
                    this.recommendForm.busy = false;
                    this.showSuccessModal();
                    this.getRecentRecommendees();
                }, response => {
                    // alert('error');
                });
        },
        showSuccessModal(){
            $('#modal-recommend-episode-1').modal('hide');
            $('#modal-recommend-episode-2').modal('hide');
            $('#modal-recommend-success').modal('show');
            setTimeout(function(){
                $('#modal-recommend-success').modal('hide');
            }, 2500);
        },
        toggleLikeEpisode(episode){
            if (episode.this_user_likes){
                return this.unlikeEpisode(episode);
            }else{
                return this.likeEpisode(episode);
            }
        },
        likeEpisode(episode) {
            var self = this;
            this.$http.post('/api/episodes/like', {slug: episode.slug})
                .then(response => {
                    self.updateEpisode(episode.slug, response.data.total_likes, response.data.this_user_likes);
                }, response => {
                    //alert('error');
                })
        },
        unlikeEpisode(episode) {
            var self = this;
            this.$http.post('/api/episodes/unlike', {slug: episode.slug})
                .then(response => {
                    self.updateEpisode(episode.slug, response.data.total_likes, response.data.this_user_likes);
                }, response => {
                    //alert('error');
                })
        },
    }
}