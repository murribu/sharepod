Vue.component('home', {
    props: ['user'],
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
});
