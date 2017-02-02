var copyFeed = require('./mixins/copy-feed');
var tabState = require('./../../../../spark/resources/assets/js/mixins/tab-state');

Vue.component('view-user', {
    props: ['user'],
    mixins: [tabState, copyFeed],
    mounted() {
        this.usePushStateForTabs('.user-tabs');
    },
    data() {
        return {
            viewed_user: {},
            episodes_liked: [],
            episodes_liked_loaded: false,
            shows_liked: [],
            shows_liked_loaded: false,
            playlists: [],
            playlists_loaded: false,
            connections: {
                accepted: [],
                pending: [],
            },
            connections_loaded: false,
            recommendations_accepted: [],
            recommendations_loaded: false,
        };
    },
    created() {
        this.getUser();
    },
    computed: {
        slug() {
            return window.location.href.split('/')[4].split('#')[0];
        },
        isMe() {
            return this.viewed_user.id == this.user.id;
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
            this.$http.get('/api/users/' + this.slug + '/episodes_liked')
                .then(response => {
                    self.episodes_liked = response.data;
                    self.episodes_liked_loaded = true;
                }, response => {
                    // alert('error');
                });
            this.$http.get('/api/users/' + this.slug + '/shows_liked')
                .then(response => {
                    self.shows_liked = response.data;
                    self.shows_liked_loaded = true;
                }, response => {
                    // alert('error');
                });
            this.$http.get('/api/users/' + this.slug + '/playlists')
                .then(response => {
                    self.playlists = response.data;
                    self.playlists_loaded = true;
                }, response => {
                    // alert('error');
                });
            this.$http.get('/api/users/' + this.slug + '/connections')
                .then(response => {
                    self.connections.accepted = response.data.accepted;
                    self.connections.pending = response.data.pending;
                    self.connections_loaded = true;
                }, response => {
                    // alert('error');
                });
            this.$http.get('/api/users/' + this.slug + '/recommendations_accepted')
                .then(response => {
                    self.recommendations_accepted = response.data;
                    self.recommendations_loaded = true;
                }, response => {
                    // alert('error');
                });
        }
    }
});