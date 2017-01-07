var base = require('auth/register-stripe');

Vue.component('spark-register-stripe', {
    mixins: [base],
    methods: {
        signInWithTwitter() {
            window.open('/auth/twitter','auth','width=500,height=450');
        },
        signInWithFacebook() {
            window.open('/auth/facebook','auth','width=500,height=450');
        },
    },
});
