Vue.component('connections', {
    props: ['user'],
    data() {
        return {
            updateBusy: false,
            connections: {
                given:[],
                received:[],
            },
            outstandingRequests: 0,
            areYouSure: {
                approveMsg: 'Are you sure you want to approve this connection?',
                blockMsg:   'Are you sure you want to block this connection?',
                pendingMsg: 'Are you sure you want to mark this connection as pending?',
                displayMsg: '',
                action: '',
                connection: {},
            },
        };
    },
    created() {
        this.loadConnections();
    },
    computed: {
        approved_connections() {
            return this.connections_of_type('approved');
        },
        blocked_connections() {
            return this.connections_of_type('blocked');
        },
        pending_connections() {
            return this.connections_of_type(null);
        },
    },
    methods: {
        loadConnections() {
            var self = this;
            this.outstandingRequests++;
            this.$http.get('/api/connections')
                .then(response => {
                    if (--self.outstandingRequests == 0){
                        self.connections = response.data
                    }
                    this.updateBusy = false;
                },response => {
                    self.outstandingRequests--;
                    this.updateBusy = false;
                    // alert('error');
                });
        },
        showAreYouSure(message, action, connection){
            this.areYouSure.displayMsg = message;
            this.areYouSure.action = action;
            this.areYouSure.connection = connection;
            $('#modal-are-you-sure').modal('show');
        },
        yesImSure(){
            $('#modal-are-you-sure').modal('hide');
            switch (this.areYouSure.action){
                case 'approve':
                    this.approveConnection(this.areYouSure.connection);
                    break;
                case 'block':
                    this.blockConnection(this.areYouSure.connection);
                    break;
                case 'pending':
                    this.makeConnectionPending(this.areYouSure.connection);
                    break;
            }
        },
        noNeverMind(){
            $('#modal-are-you-sure').modal('hide');
            this.areYouSure.displayMsg = '';
            this.areYouSure.callback = '';
            this.areYouSure.connection = {};
        },
        approveConnection(c){
            var self = this;
            this.updateBusy = true;
            var sent = {
                connection_id: c.connection_id
            }
            this.$http.post('/api/connections/approve', sent)
                .then(response => {
                    self.loadConnections();
                }, response => {
                    // alert('error')
                })
        },
        blockConnection(c){
            var self = this;
            this.updateBusy = true;
            var sent = {
                connection_id: c.connection_id
            }
            this.$http.post('/api/connections/block', sent)
                .then(response => {
                    self.loadConnections();
                }, response => {
                    // alert('error')
                })
        },
        makeConnectionPending(c){
            var self = this;
            this.updateBusy = true;
            var sent = {
                connection_id: c.connection_id
            }
            this.$http.post('/api/connections/make_pending', sent)
                .then(response => {
                    self.loadConnections();
                }, response => {
                    // alert('error')
                })
        },
        connections_of_type(c_status) {
            var self = this;
            var ret = {
                given: [],
                received: [],
            };
            for(c in this.connections.given){
                if (this.connections.given[c].status == c_status){
                    ret.given.push(this.connections.given[c]);
                }
            }
            for(c in this.connections.received){
                if (this.connections.received[c].status == c_status){
                    ret.received.push(this.connections.received[c]);
                }
            }
            ret.given    = ret.given.sort(function(a,b) {return self.sort_connections(a,b);});
            ret.received = ret.received.sort(function(a,b) {return self.sort_connections(a,b);});
            return ret;
        },
        sort_connections(a,b) {
            return a.updated_at < b.updated_at ? -1 : 1;
        }
    }
});