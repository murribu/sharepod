require('./shows/search');
require('./shows/browse');
require('./shows/new');

Vue.component('shows', {
    props: ['user'],
    computed: {
        test() { return 'shows'; },
    },
    mounted() {
        //
    }
});
