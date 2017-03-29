Vue.component('update-social-accounts', {
    props: ['user'],
    methods: {
        linkWithTwitter() {
            window.open('/auth/twitter','auth','width=500,height=450');
        },
        linkWithFacebook() {
            window.open('/auth/facebook','auth','width=500,height=450');
        },
        approveUnlinkWithFacebook() {
            $('#modal-unlink-facebook').modal('show');
        },
        unlinkWithFacebook() {
            var vm = this;
            axios.get('/auth/facebook/unlink').then(response => {
                Bus.$emit('updateUser');
                $('#modal-unlink-facebook').modal('hide');
            });
        },
        approveUnlinkWithTwitter() {
            $('#modal-unlink-twitter').modal('show');
        },
        unlinkWithTwitter() {
            var vm = this;
            axios.get('/auth/twitter/unlink').then(response => {
                Bus.$emit('updateUser');
                $('#modal-unlink-twitter').modal('hide');
            });
        },
    },
});
