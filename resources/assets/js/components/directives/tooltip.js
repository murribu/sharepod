Vue.directive('tooltip', {
    bind() {
        Vue.nextTick(_.debounce(function(){
            $('[title]').tooltip();
        }, 200));
    }
});