var copyFeed = require('./mixins/copy-feed');
var tabState = require('./../../../../spark/resources/assets/js/mixins/tab-state');
var episodeActions = require('./mixins/episode-actions');

Vue.component('view-user', {
    props: ['user'],
    mixins: [episodeActions, tabState, copyFeed],
    mounted() {
        this.usePushStateForTabs('.user-tabs');
    },
    data() {
        return {
            episodeGroups: {
                episodes_liked: {
                    episodes: [],
                },
                recommendations_accepted: {
                    episodes: [],
                },
                episodes_archived: {
                    episodes: [],
                },
                show: {},
            },
            viewed_user: {},
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
            recommendations_loaded: false,
            episodes_archived_loaded: false,
            verbs:{
                to_have:{
                    you: 'have',
                    third_person: 'has'
                },
                to_do:{
                    you: 'do',
                    third_person: 'does'
                }
            },
            selectedEpisode: {},
            show: false
        };
    },
    created() {
        this.getUser();
        if (this.user){
            this.getRecentRecommendees();
            this.getPlaylists();
        }
    },
    computed: {
        slug() {
            return window.location.href.split('/')[4].split('#')[0];
        },
        isMe() {
            return this.user && this.viewed_user.id == this.user.id;
        },
        percentStorageUsed(){
            return Math.ceil(parseInt(this.viewed_user.storage_used)*10 / parseInt(this.viewed_user.plan_storage_limit)) / 10;
        }
    },
    methods: {
        formatStorage(s){
            s = parseInt(s);
            if (s > 1000000000){
                return Math.ceil(s/10000000)/100 + ' GB';
            }
            if (s > 1000000){
                return Math.ceil(s/10000)/100 + ' MB';
            }
            if (s > 1000){
                return Math.ceil(s/10)/100 + ' KB';
            }
            return s + ' Bytes';
        },
        getEpisodesLiked() {
            var self = this;
            this.$http.get('/api/users/' + this.slug + '/episodes_liked')
                .then(response => {
                    self.episodeGroups.episodes_liked.episodes = response.data;
                    self.episodes_liked_loaded = true;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        getShowsLiked() {
            var self = this;
            this.$http.get('/api/users/' + this.slug + '/shows_liked')
                .then(response => {
                    self.shows_liked = response.data;
                    self.shows_liked_loaded = true;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        getPlaylists() {
            var self = this;
            this.$http.get('/api/users/' + this.slug + '/playlists')
                .then(response => {
                    self.playlists = response.data;
                    self.playlists_loaded = true;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        getConnections() {
            var self = this;
            this.$http.get('/api/users/' + this.slug + '/connections')
                .then(response => {
                    self.connections.accepted = response.data.accepted;
                    self.connections.pending = response.data.pending;
                    self.connections_loaded = true;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        getRecommendationsAccepted() {
            var self = this;
            this.$http.get('/api/users/' + this.slug + '/recommendations_accepted')
                .then(response => {
                    self.episodeGroups.recommendations_accepted.episodes = response.data;
                    self.recommendations_loaded = true;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        getArchivedEpisodes() {
            var self = this;
            this.$http.get('/api/archived_episodes')
                .then(response => {
                    self.episodeGroups.episodes_archived.episodes = response.data;
                    self.episodes_archived_loaded = true;
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
        },
        getUser() {
            var self = this;
            this.$http.get('/api/users/' + this.slug)
                .then(response => {
                    self.viewed_user = response.data;
                    if (self.isMe){
                        this.getArchivedEpisodes();
                    }else{
                        $("[aria-controls='archived-episodes']").parent().hide();
                    }
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                });
            this.getEpisodesLiked();
            this.getShowsLiked();
            this.getPlaylists();
            this.getRecommendationsAccepted();
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
        },
        updateEpisode(slug, stats){
            //episodes_liked
            var found = false;
            for(var e in this.episodeGroups.episodes_liked.episodes){
                if (this.episodeGroups.episodes_liked.episodes[e].slug == slug){
                    found = true;
                    if (stats.this_user_likes != this.episodeGroups.episodes_liked.episodes[e].this_user_likes){
                        this.getEpisodesLiked();
                    }else{
                        this.episodeGroups.episodes_liked.episodes[e].result_slug = stats.result_slug;
                        this.episodeGroups.episodes_liked.episodes[e].this_user_archived = stats.this_user_archived;
                        this.episodeGroups.episodes_liked.episodes[e].this_user_likes = stats.this_user_likes;
                        this.episodeGroups.episodes_liked.episodes[e].total_likes = stats.total_likes;
                        this.episodeGroups.episodes_liked.episodes[e].total_playlists = stats.total_playlists;
                        this.episodeGroups.episodes_liked.episodes[e].total_recommendations = stats.total_recommendations;
                        $('[data-slug=' + slug + '] .btn-archive-episode .icon-container').attr('data-original-title', stats.this_user_archived ? 'Unarchive' : 'Archive');
                        $('[data-slug=' + slug + '] .btn-episode-like .icon-container').attr('data-original-title', stats.this_user_likes ? 'Unlike' : 'Like');
                    }
                }
            }
            if (!found){
                this.getEpisodesLiked();
            }
            //recommendations_accepted
            for(var e in this.episodeGroups.recommendations_accepted.episodes){
                if (this.episodeGroups.recommendations_accepted.episodes[e].slug == slug){
                    this.episodeGroups.recommendations_accepted.episodes[e].result_slug = stats.result_slug;
                    this.episodeGroups.recommendations_accepted.episodes[e].this_user_archived = stats.this_user_archived;
                    this.episodeGroups.recommendations_accepted.episodes[e].this_user_likes = stats.this_user_likes;
                    this.episodeGroups.recommendations_accepted.episodes[e].total_likes = stats.total_likes;
                    this.episodeGroups.recommendations_accepted.episodes[e].total_playlists = stats.total_playlists;
                    this.episodeGroups.recommendations_accepted.episodes[e].total_recommendations = stats.total_recommendations;
                    $('[data-slug=' + slug + '] .btn-archive-episode .icon-container').attr('data-original-title', stats.this_user_archived ? 'Unarchive' : 'Archive');
                    $('[data-slug=' + slug + '] .btn-episode-like .icon-container').attr('data-original-title', stats.this_user_likes ? 'Unlike' : 'Like');
                }
            }
            //episodes_archived
            found = false;
            for(var e in this.episodeGroups.episodes_archived.episodes){
                if (this.episodeGroups.episodes_archived.episodes[e].slug == slug){
                    found = true;
                    if (stats.this_user_archived != this.episodeGroups.episodes_archived.episodes[e].this_user_archived){
                        this.getArchivedEpisodes();
                    }else{
                        this.episodeGroups.episodes_archived.episodes[e].result_slug = stats.result_slug;
                        this.episodeGroups.episodes_archived.episodes[e].this_user_archived = stats.this_user_archived;
                        this.episodeGroups.episodes_archived.episodes[e].this_user_likes = stats.this_user_likes;
                        this.episodeGroups.episodes_archived.episodes[e].total_likes = stats.total_likes;
                        this.episodeGroups.episodes_archived.episodes[e].total_playlists = stats.total_playlists;
                        this.episodeGroups.episodes_archived.episodes[e].total_recommendations = stats.total_recommendations;
                        $('[data-slug=' + slug + '] .btn-archive-episode .icon-container').attr('data-original-title', stats.this_user_archived ? 'Unarchive' : 'Archive');
                        $('[data-slug=' + slug + '] .btn-episode-like .icon-container').attr('data-original-title', stats.this_user_likes ? 'Unlike' : 'Like');
                    }
                }
            }
            if (!found){
                this.getArchivedEpisodes();
            }
        }
    }
});