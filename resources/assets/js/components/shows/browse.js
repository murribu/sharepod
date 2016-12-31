Vue.component('shows-browse', {
    props: ['user'],
    data() {
        return {
            shows: [],
            processing: false,
            error: false,
        };
    },
    created() {
        var self = this;
        
        this.processing = true;
        this.error = false;
        this.$http.get('api/shows')
            .then(response => {
                self.processing = false;
                self.shows = response.data;
            }, response => {
                self.processing = false;
                self.error = true;
            });
    },
    methods: {
        
    },
});
