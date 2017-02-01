Vue.component('view-user', {
    props: ['user'],
    mixins: [require('./../../../../spark/resources/assets/js/mixins/tab-state')],
    mounted() {
        this.usePushStateForTabs('.user-tabs');
    },
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
        },
        isMe() {
            return viewed_user.id == user.id;
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