Vue.component('recommendation', {
    props: ['user'],
    data() {
        return {
            recommendation: {},
            recommendation_loaded: false,
            busy: false,
        };
    },
    created() {
        this.loadRecommenation();
    },
    computed: {
        slug() {
            return window.location.href.split('/')[4];
        },
    },
    methods: {
        accept(){
            var self = this;
            var sent = {
                slugs: [this.recommendation.slug]
            };
            this.busy = true;
            this.$http.post('/api/recommendations/accept', sent)
                .then(response => {
                    self.loadRecommenation();
                    self.busy = false;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        reject(){
            var self = this;
            var sent = {
                slugs: [this.recommendation.slug]
            };
            this.busy = true;
            this.$http.post('/api/recommendations/reject', sent)
                .then(response => {
                    self.loadRecommenation();
                    self.busy = false;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        makePending(){
            var self = this;
            var sent = {
                slugs: [this.recommendation.slug]
            };
            this.busy = true;
            this.$http.post('/api/recommendations/make_pending', sent)
                .then(response => {
                    self.loadRecommenation();
                    self.busy = false;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        loadRecommenation() {
            var self = this;
            this.recommendation_loaded = false;
            this.$http.get('/api/recommendations/' + this.slug)
                .then(response => {
                    self.recommendation = response.data;
                    self.recommendation_loaded = true;
                },
                response => {
                    switch (response.status){
                        case 401:
                            window.location.href = '/';
                            break;
                        default:
                            $("#modal-error").modal('show');
                            setTimeout(function(){
                                $("#modal-error").modal('hide');
                            }, 8000);
                            break;
                    }
                });
        }
    }
});