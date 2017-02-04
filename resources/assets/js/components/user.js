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
            verbs:{
                to_have:{
                    you: 'have',
                    third_person: 'has'
                },
                to_do:{
                    you: 'do',
                    third_person: 'does'
                }
            }
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
            return this.user && this.viewed_user.id == this.user.id;
        },
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
        },
        viewed_user_name(options = false) {
            var ret = '';
            if (this.viewed_user && this.user && this.viewed_user.slug == this.user.slug){
                if (options.possessive){
                    ret = 'Your';
                }else{
                    ret = 'You';
                }
                if (options.verbs){
                    ret += ' ' + options.verbs.you;
                }
                return ret;
            }else{
                if (options.possessive){
                    if (options.generic){
                        ret = 'their';
                    }else{
                        ret = this.viewed_user.name + '\'s';
                    }
                }else{
                    ret = this.viewed_user.name;
                }
                if (options.verbs){
                    ret += ' ' + options.verbs.third_person;
                }
                return ret;
            }
        }
    }
});