Vue.component('show', {
    props: ['user'],
    data() {
        return {
            show: {},
            selectedEpisode: {},
            sendToEmailAddress: '',
            sendForm: {
                busy: false
            }
        };
    },
    computed: {
        slug() {
            return window.location.href.split('/')[4];
        },
        oldestPubdate() {
            var ret = '9999999999';
            if (this.show.episodes){
                for(var e in this.show.episodes){
                    if (this.show.episodes[e].pubdate < ret){
                        ret = this.show.episodes[e].pubdate;
                    }
                }
            }
            
            return ret;
        },
        displayEpisodes() {
            if (this.show.episodes){
                return this.show.episodes.sort(function(a, b){
                    return a.pubdate < b.pubdate ? 1 : -1;
                });
            }else{
                return [];
            }
        }
    },
    created() {
        var self = this;
        this.$http.get('/api/shows/' + this.slug)
            .then(response => {
                self.show = response.data;
            },
            response => {
                // alert('error');
            });
    },
    methods: {
        sendEpisode(episode) {
            this.selectedEpisode = episode;
            $('#modal-send-episode-1').modal('show');
        },
        sendEpisodeViaEmailDialog() {
            $('#modal-send-episode-1').modal('hide');
            $('#modal-send-episode-via-email').modal('show');
        },
        sendEpisodeViaTwitterDialog() {
            $('#modal-send-episode-1').modal('hide');
            $('#modal-send-episode-via-twitter').modal('show');
        },
        sendEpisodeViaEmail() {
            this.sendForm.busy = true;
            this.$http.post('/send', {slug: this.selectedEpisode.slug, email_address: this.sendToEmailAddress})
                .then(response => {
                    
                }, response => {
                    
                });
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
        likeShow() {
            var self = this;
            this.$http.post('/api/shows/like', {slug: this.show.slug})
                .then(response => {
                    self.updateShow(response.data.total_likes, response.data.this_user_likes);
                }, response => {
                    //alert('error');
                })
        },
        unlikeShow() {
            var self = this;
            this.$http.post('/api/shows/unlike', {slug: this.show.slug})
                .then(response => {
                    self.updateShow(response.data.total_likes, response.data.this_user_likes);
                }, response => {
                    //alert('error');
                })
        },
        showMore() {
            var self = this;
            this.$http.get('/api/shows/' + this.slug + '/episodes?pubdate=' + this.oldestPubdate)
                .then(response => {
                    self.show.episodes = self.show.episodes.concat(response.data);
                }, response => {
                    // alert('error');
                });
        },
        updateEpisode(slug, total_likes, this_user_likes){
            for(var e in this.show.episodes){
                if (this.show.episodes[e].slug == slug){
                    this.show.episodes[e].total_likes = total_likes;
                    this.show.episodes[e].this_user_likes = this_user_likes;
                }
            }
        },
        updateShow(total_likes, this_user_likes){
            this.show.total_likes = total_likes;
            this.show.this_user_likes = this_user_likes;
        }
    }
});

