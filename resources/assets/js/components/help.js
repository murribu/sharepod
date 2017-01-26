require('./help/find-a-podcatcher');
require('./help/register-my-feed');

Vue.component('help', {
    props: ['user'],
    mixins: [require('./../../../../spark/resources/assets/js/mixins/tab-state')],
    mounted() {
        this.usePushStateForTabs('.help-tabs');
    },
});