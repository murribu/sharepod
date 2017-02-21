Vue.component('shows-new', {
    props: ['user'],
    data() {
        return {
            processing: false,
            error: false,
            errorMessage: '',
            feed: '',
            newShow: null,
        };
    },
    methods: {
        addShow() {
            this.newShow = null;
            var sent = {
                feed: this.feed
            };
            var self = this;
            this.processing = true;
            this.error = false;
            this.errorMessage = '';
            this.$http.post('/shows/new', sent)
                .then(response => {
                    self.processing = false;
                    self.error = false;
                    self.newShow = response.data;
                }, response => {
                    self.processing = false;
                    self.error = true;
                    self.errorMessage = response.data;
                });
        }
    },
});
