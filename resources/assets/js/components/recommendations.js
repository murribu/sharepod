Vue.component('recommendations', {
    props: ['user'],
    data() {
        return {
            recommendations_pending: [],
            recommendations_accepted: [],
            recommendations_pending_loaded: false,
            recommendations_accepted_loaded: false,
            updatePendingBusy: false,
            updateAcceptedBusy: false,
            
        };
    },
    created() {
        this.loadRecommendationsPending();
        this.loadRecommendationsAccepted();
    },
    computed: {
    },
    methods: {
        gotoRecommendation(r) {
            window.location.href = '/recommendations/' + r.recommendation_slug;
        },
        accept(r){
            var self = this;
            var sent = {
                slugs: []
            };
            for(user in r.users){
                sent.slugs.push(r.users[user].recommendation_slug);
            }
            this.updatePendingBusy = true;
            this.updateAcceptedBusy = true;
            this.$http.post('/api/recommendations/accept', sent)
                .then(response => {
                    self.loadRecommendationsPending();
                    self.loadRecommendationsAccepted();
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        reject(r){
            var self = this;
            var sent = {
                slugs: []
            };
            for(user in r.users){
                sent.slugs.push(r.users[user].recommendation_slug);
            }
            this.updatePendingBusy = true;
            this.updateAcceptedBusy = true;
            this.$http.post('/api/recommendations/reject', sent)
                .then(response => {
                    self.loadRecommendationsPending();
                    self.loadRecommendationsAccepted();
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        makePending(r){
            var self = this;
            var sent = {
                slugs: []
            };
            for(user in r.users){
                sent.slugs.push(r.users[user].recommendation_slug);
            }
            this.updatePendingBusy = true;
            this.updateAcceptedBusy = true;
            this.$http.post('/api/recommendations/make_pending', sent)
                .then(response => {
                    self.loadRecommendationsPending();
                    self.loadRecommendationsAccepted();
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        loadRecommendationsPending(){
            var self = this;
            this.updatePendingBusy = true;
            this.$http.get('/api/recommendations_pending')
                .then(response => {
                    self.recommendations_pending = response.data;
                    self.updatePendingBusy = false;
                    self.recommendations_pending_loaded = true;
                },
                response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        loadRecommendationsAccepted(){
            var self = this;
            this.updateAcceptedBusy = true;
            this.$http.get('/api/recommendations_accepted')
                .then(response => {
                    self.recommendations_accepted = response.data;
                    self.updateAcceptedBusy = false;
                    self.recommendations_accepted_loaded = true;
                },
                response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
    }
});