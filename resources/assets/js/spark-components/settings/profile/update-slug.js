Vue.component('spark-update-slug', {
    props: ['user'],
    data() {
        return {
            slug: '',
            busy: false,
            error: '',
            successful: false,
            dirty: false,
            firstRun: true,
            success: '',
        };
    },
    mounted() {
        this.slug = this.user.slug;
    },
    watch: {
        slug: function(s){
            if (!this.firstRun){
                this.dirty = true;
                this.error = '';
                this.success = '';
            }
            this.firstRun = false;
            this.busy = true;
            this.searchForSlug();
        }
    },
    methods: {
        updateSlug() {
            var self = this;
            this.busy = true;
            this.error = '';
            this.success = '';
            var sent = {
                slug: this.slug
            };
            axios.post('/api/userslug/update', sent)
                .then(response => {
                    self.busy = false;
                    if (response.data.success == '1'){
                        self.success = '1';
                        self.dirty = false;
                        setTimeout(function(){
                            self.success = '';
                        }, 8000);
                    }else{
                        self.success = '';
                        self.error = 'There was an error. Please refresh the page and try again.';
                    }
                }, response => {
                    $("#modal-error").modal('show');
                    setTimeout(function(){
                        $("#modal-error").modal('hide');
                    }, 8000);
                })
        },
        searchForSlug: _.debounce(function(){
            if (this.dirty){
                var self = this;
                var sent = {
                    slug: this.slug
                };
                axios.post('/api/userslug/search', sent)
                    .then(response => {
                        self.busy = false;
                        if (response.data.slug){
                            if (response.data.slug == self.user.slug){
                                self.error = 'That\'s you!';
                            }else{
                                self.error = 'This handle is already taken';
                            }
                        }else{
                            self.error = '';
                        }
                    }, response => {
                        $("#modal-error").modal('show');
                        setTimeout(function(){
                            $("#modal-error").modal('hide');
                        }, 8000);
                    })
            }
        }, 500)
    },
});
