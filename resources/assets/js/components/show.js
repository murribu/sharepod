Vue.component('show', {
    props: ['user'],
    data() {
        return {
            show: {},
            selectedEpisode: {},
            recommendToEmailAddress: '',
            recommendForm: {
                busy: false
            },
            recentRecommendees: [],
            recommendEmail: '',
            recommendTwitter: ''
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
        this.getRecentRecommendees();
    },
    methods: {
        getRecentRecommendees(){
            var self = this;
            this.$http.get('/recent_recommendees')
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

