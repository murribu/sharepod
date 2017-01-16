Vue.component('view-user', {
    props: ['user'],
    data() {
        return {
            viewed_user: {}
        };
    },
    created() {
        this.getUser();
    },
    computed: {
        slug() {
            return window.location.href.split('/')[4];
        }
    },
    methods: {
        getUser() {
            var self = this;
            this.$http.get('/api/users/' + this.slug)
                .then(response => {
                    self.viewed_user = response.data;
                }, response => {
                    // alert('error');
                });
        }
    }
});