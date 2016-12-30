Vue.component('shows-list', {
    props: ['user'],
    data() {
        return {
            feedback: '',
        };
    },
    methods: {
        addShow() {
            this.feedback = 'testing';
        }
    },
    mounted() {
        //
    }
});
