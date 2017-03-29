Vue.component('shows-new', {
    props: ['user'],
    data() {
        return {
            processing: false,
            error: false,
            already_exists: false,
            already_exists_show: {
                name: '',
                slug: '',
            },
            errorMessage: '',
            feed: '',
            newShow: null,
        };
    },
    methods: {
        addShow() {
            this.newShow = null;
            var sent = {
                feed: this.feed
            };
            var self = this;
            this.processing = true;
            this.error = false;
            this.already_exists = false;
            this.errorMessage = '';
            axios.post('/shows/new', sent)
                .then(response => {
                    self.processing = false;
                    self.error = false;
                    self.already_exists = false;
                    self.newShow = response.data;
                }, response => {
                    self.processing = false;
                    if (response.error == 'already_exists'){
                        self.already_exists = true;
                        self.already_exists_show.slug = response.data.slug;
                        self.already_exists_show.name = response.data.name;
                    }else{
                        self.error = true;
                        self.errorMessage = response.data.message;
                    }
                });
        }
    },
});
