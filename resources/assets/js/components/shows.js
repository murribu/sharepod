require('./shows/search');
require('./shows/browse');
require('./shows/new');

Vue.component('shows', {
    props: ['user'],
    mixins: [require('./../../../../spark/resources/assets/js/mixins/tab-state')],
    computed: {
        test() { return 'shows'; },
    },
    mounted() {
        this.usePushStateForTabs('.shows-tabs');
    }
});
