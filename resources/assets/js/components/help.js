var copyFeed = require('./mixins/copy-feed');
var tabState = require('./../../../../spark/resources/assets/js/mixins/tab-state');

Vue.component('help', {
    props: ['user'],
    mixins: [tabState, copyFeed],
    mounted() {
        this.usePushStateForTabs('.help-tabs');
    },
    created() {
        var hash = window.location.hash.substring(2);
        if (hash.substring(0,4) == 'what'){
            var self = this;
            Vue.nextTick(function(){
                self.showStory(hash.substring(5));
            });
        }
    },
    methods:{
        showStory(length){
            window.location.hash = '/what-' + length;
            $(".story").hide();
            $("#" + length + "-story").show();
            $("[aria-controls='what']").parent().addClass('active');
        }
    }
});