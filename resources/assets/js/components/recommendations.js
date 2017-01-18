Vue.component('recommendations', {
    props: ['user'],
    data() {
        return {
            recommendations_given: [],
            recommendations_received: [],
            recommendations_pending: [],
            recommendations_given_count: 0,
            recommendations_received_count: 0,
            recommendations_given_loaded: false,
            recommendations_received_loaded: false,
            updateBusy: false,
        };
    },
    directives: {
        tooltip: {
            bind() {
                console.log('bind');
                Vue.nextTick(function(){
                    $('[title]').tooltip();
                });
            }
        }
    },
    created() {
        this.loadRecommendationsGiven();
        this.loadRecommendationsGivenCount();
        this.loadRecommendationsReceived();
        this.loadRecommendationsReceivedCount();
        this.loadRecommendationsPending();
    },
    computed: {
        oldest_recommendation_given() {
            var ret = new Date(2199, 11, 31, 23, 59, 59);
            for(r in this.recommendations_given){
                var dateStr = this.recommendations_given[r].created_at;
                var a=dateStr.split(" ");
                var d=a[0].split("-");
                var t=a[1].split(":");
                var d = new Date(d[0],(d[1]-1),d[2],t[0],t[1],t[2]);
                if (d < ret){
                    ret = d;
                }
            }
            
            return ret.getFullYear() + '-' + ("00" + (ret.getMonth() + 1)).slice(-2) + '-' + ("00" + ret.getDate()).slice(-2) + ' ' + ("00" + ret.getHours()).slice(-2) + ':' + ("00" + ret.getMinutes()).slice(-2) + ':' + ("00" + ret.getSeconds()).slice(-2);
        },
        recommendations_given_episode_ids() {
            var ret = [-1];
            for(r in this.recommendations_given){
                ret.push(this.recommendations_given[r].episode_id);
            }
            
            return ret.join();
        },
        oldest_recommendation_received() {
            var ret = new Date(2199, 11, 31, 23, 59, 59);
            for(r in this.recommendations_received){
                var dateStr = this.recommendations_received[r].created_at;
                var a=dateStr.split(" ");
                var d=a[0].split("-");
                var t=a[1].split(":");
                var d = new Date(d[0],(d[1]-1),d[2],t[0],t[1],t[2]);
                if (d < ret){
                    ret = d;
                }
            }
            
            return ret.getFullYear() + '-' + ("00" + (ret.getMonth() + 1)).slice(-2) + '-' + ("00" + ret.getDate()).slice(-2) + ' ' + ("00" + ret.getHours()).slice(-2) + ':' + ("00" + ret.getMinutes()).slice(-2) + ':' + ("00" + ret.getSeconds()).slice(-2);
        },
        recommendations_received_episode_ids() {
            var ret = [-1];
            for(r in this.recommendations_received){
                ret.push(this.recommendations_received[r].episode_id);
            }
            
            return ret.join();
        },
    },
    methods: {
        gotoRecommendation(r) {
            window.location.href = '/recommendations/' + r.recommendation_slug;
        },
        loadRecommendationsPending(){
            var self = this;
            this.$http.get('/api/recommendations_pending')
                .then(response => {
                    self.recommendations_pending = response.data;
                },
                response => {
                    // alert('error');
                });
        },
        loadRecommendationsReceived(){
            var self = this;
            this.$http.get('/api/recommendations_received?date=' + this.oldest_recommendation_given + '&episodes=' + this.recommendations_received_episode_ids)
                .then(response => {
                    self.recommendations_received = self.recommendations_received.concat(response.data);
                    self.recommendations_received_loaded = true;
                },
                response => {
                    // alert('error');
                });
        },
        loadRecommendationsReceivedCount(){
            var self = this;
            this.$http.get('/api/recommendations_received_count')
                .then(response => {
                    self.recommendations_received_count = response.data[0].c;
                },
                response => {
                    // alert('error');
                });
        },
        loadRecommendationsGiven(){
            var self = this;
            this.$http.get('/api/recommendations_given?date=' + this.oldest_recommendation_given + '&episodes=' + this.recommendations_given_episode_ids)
                .then(response => {
                    self.recommendations_given = self.recommendations_given.concat(response.data);
                    self.recommendations_given_loaded = true;
                },
                response => {
                    // alert('error');
                });
        },
        loadRecommendationsGivenCount(){
            var self = this;
            this.$http.get('/api/recommendations_given_count')
                .then(response => {
                    self.recommendations_given_count = response.data[0].c;
                },
                response => {
                    // alert('error');
                });
        },
    }
});