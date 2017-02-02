var copyFeed = require('./mixins/copy-feed');
var tabState = require('./../../../../spark/resources/assets/js/mixins/tab-state');

Vue.component('help', {
    props: ['user'],
    mixins: [tabState, copyFeed],
    mounted() {
        this.usePushStateForTabs('.help-tabs');
    },
});