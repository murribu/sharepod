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
            recommendationComment: '',
            playlists: [],
            selectedPlaylist: {},
            archiveResultMessage: '',
            archiveResultHeader: '',
        };
    },
    methods: {
        toggleArchiveEpisode(episode) {
            var self = this;
            var sent = {
                slug: episode.slug
            };
            if (episode.this_user_archived){
                $("#modal-unarchive-are-you-sure").modal('show');
                this.selectedEpisode = episode;
            }else{
                this.$http.post('/api/episodes/archive', sent)
                    .then(response => {
                        $("#modal-archive-result").modal('show');
                        self.archiveResultHeader = response.data.header;
                        self.archiveResultMessage = response.data.message;
                        setTimeout(function(){
                            $("#modal-archive-result").modal('hide');
                        }, 10000);
                        self.updateEpisode(episode.slug, response.data.stats);
                    }, response => {
                        $("#modal-error").modal('show');
                        setTimeout(function(){
                            $("#modal-error").modal('hide');
                        }, 8000);
                    });
            }
        },
        unArchive() {
            var episode = this.selectedEpisode;
            var self = this;
            var sent = {
                slug: episode.slug
            };
            this.$http.post('/api/episodes/unarchive', sent)
                .then(response => {
                    $("#modal-unarchive-are-you-sure").modal('hide');
                    self.updateEpisode(self.selectedEpisode.slug, response.data.stats);
                    $("#modal-unarchive-success").modal('show');
                    setTimeout(function(){
                        $("#modal-unarchive-success").modal('hide');
                    }, 8000);
                }, response => {
                    $("#modal-unarchive-are-you-sure").modal('hide');
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        getPlaylists() {
            var self = this;
            this.$http.get('/api/playlists')
                .then(response => {
                    self.playlists = response.data;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
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
            var episode = this.selectedEpisode;
            var sent = {
                slug: episode.slug,
            };
            this.selectedPlaylist = playlist;
            $("#modal-select-playlist").modal('hide');
            this.$http.post('/api/playlists/' + playlist.slug + '/add_episode', sent)
                .then(response => {
                    $("#modal-add-to-playlist-success").modal('show');
                    setTimeout(function(){
                        $("#modal-add-to-playlist-success").modal('hide');
                    }, 7000);
                    self.updateEpisode(episode.slug, response.data.stats);
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        getRecentRecommendees(){
            var self = this;
            this.$http.get('/api/recent_recommendees')
                .then(response => {
                    self.recentRecommendees = response.data;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        recommendEpisodeToExistingUser(user_slug) {
            var self = this;
            this.recommendForm.busy = true;
            this.$http.post('/recommend', {slug: this.selectedEpisode.slug, user_slug: user_slug, comment: this.recommendationComment})
                .then(response => {
                    self.recommendForm.busy = false;
                    self.showSuccessModal();
                    self.updateEpisode(self.selectedEpisode.slug, response.data.stats);
                    self.getRecentRecommendees();
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        recommendEpisode(episode) {
            if (this.user.canRecommend){
                this.selectedEpisode = episode;
                if (this.recentRecommendees.length == 0){
                    $('#modal-recommend-episode-2').modal('show');
                }else{
                    $('#modal-recommend-episode-1').modal('show');
                }
            }else{
                $("#modal-max-recommendations").modal('show');
            }
        },
        recommendEpisodeToSomeoneElse(){
            $('#modal-recommend-episode-1').modal('hide');
            $('#modal-recommend-episode-2').modal('show');
        },
        sendRecommendation(){
            var self = this;
            this.$http.post('/recommend', {slug: this.selectedEpisode.slug, email_address: this.recommendEmail, twitter_handle: this.recommendTwitter})
                .then(response => {
                    self.recommendForm.busy = false;
                    self.showSuccessModal();
                    self.getRecentRecommendees();
                    self.updateEpisode(self.selectedEpisode.slug, response.data.stats);
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
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
                    self.updateEpisode(episode.slug, response.data.stats);
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        unlikeEpisode(episode) {
            var self = this;
            this.$http.post('/api/episodes/unlike', {slug: episode.slug})
                .then(response => {
                    self.updateEpisode(episode.slug, response.data.stats);
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
    }
}