require('./shows/list');
require('./shows/browse');
require('./shows/search');

Vue.component('shows', {
    props: ['user'],
    computed: {
        test() { return 'shows'; },
    },
    mounted() {
        //
    }
});
