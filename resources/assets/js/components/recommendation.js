Vue.component('recommendation', {
    props: ['user'],
    data() {
        return {
            recommendation: {},
        };
    },
    created() {
        var self = this;
        this.$http.get('/api/recommendation/' + this.slug)
            .then(response => {
                self.recommendation = response.data;
            },
            response => {
                // alert('error');
            });
    },
    computed: {
        slug() {
            return window.location.href.split('/')[4];
        },
    }
});