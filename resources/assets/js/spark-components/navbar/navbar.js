var base = require('navbar/navbar');

Vue.component('spark-navbar', {
    mixins: [base],
    data() {
        return {
            verification_email_sent: false,
        }
    },
    methods: {
        sendVerificationEmail() {
            var vm = this;
            this.$http.get('/send_verification_email').then(response => {
                vm.verification_email_sent = true;
            }, response => {
                // alert('error');
            })
        }
    }
});
