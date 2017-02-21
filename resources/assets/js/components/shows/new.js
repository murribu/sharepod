Vue.component('shows-new', {
    props: ['user'],
    data() {
        return {
            processing: false,
            error: false,
            feedback: '',
            feed: '',
        };
    },
    methods: {
        addShow() {
            var sent = {
                feed: this.feed
            };
            var self = this;
            this.processing = true;
            this.error = false;
            this.feedback = '';
            this.$http.post('/shows/new', sent)
                .then(response => {
                    self.processing = false;
                    self.error = false;
                    self.feedback = response.data;
                }, response => {
                    self.processing = false;
                    self.error = true;
                    self.feedback = response.data;
                });
        }
    },
});
