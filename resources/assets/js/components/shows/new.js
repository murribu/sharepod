Vue.component('shows-new', {
    props: ['user'],
    data() {
        return {
            processing: false,
            error: false,
            feedback: '',
            addForm: new SparkForm({
                feed: ''
            }),
        };
    },
    methods: {
        addShow() {
            this.processing = true;
            this.error = false;
            this.$http.post('/shows/new', JSON.stringify(this.addForm))
                .then(response => {
                    this.processing = false;
                    this.error = false;
                    this.feedback = response.data;
                }, response => {
                    this.processing = false;
                    this.error = true;
                    this.feedback = 'There was an error';
                });
        }
    },
});
