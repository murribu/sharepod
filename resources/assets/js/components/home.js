Vue.component('home', {
    props: ['user'],
    data(){
        return {
            episodes:[]
        };
    },
    computed: {
        showGetStarted() {
            return !this.user || 
                !this.user.hasLikedSomething || 
                !this.user.hasRecommendedSomething || 
                (this.user.hasReceivedARecommendation && !this.user.hasTakenActionOnARecommendation) || 
                !this.user.hasRegisteredTheirFeed ||
                !this.user.hasCreatedAPlaylist;
        },
    },
    created(){
        this.loadPopularEpisodes();
    },
    methods:{
        loadPopularEpisodes() {
            var self = this;
            this.$http.get('/api/episodes/popular')
                .then(response => {
                    self.episodes = response.data;
                }, response => {
                    // alert('error');
                });
        }
    }
});

