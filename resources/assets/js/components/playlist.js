Vue.component('playlist', {
    props: ['user'],
    data() {
        return {
            playlist: {
                name: '',
                description: '',
                episodes: [],
            },
            copyLinkText: 'Click here to copy RSS Feed URL',
            loaded: false,
        };
    },
    created() {
        this.loadPlaylist();
    },
    computed: {
        slug() {
            return window.location.href.split('/')[4];
        },
    },
    methods: {
        loadPlaylist() {
            var self = this;
            this.loaded = false;
            this.$http.get('/api/playlists/' + this.slug)
                .then(response => {
                    self.playlist = response.data;
                    self.loaded = true;
                }, response => {
                    // alert('error');
                });
        },
        copyFeed(){
            var textArea = document.createElement("input");
            var feedUrl = window.location.href.split('/')[0]
                + '//' + window.location.href.split('/')[2]
                + '/playlists/' + this.slug + '/feed';
            textArea.style.position = 'fixed';
            textArea.style.top = 0;
            textArea.style.left = 0;
            textArea.style.width = '2em';
            textArea.style.height = '2em';
            textArea.style.padding = 0;
            textArea.style.border = 'none';
            textArea.style.outline = 'none';
            textArea.style.boxShadow = 'none';
            textArea.style.background = 'transparent';
            textArea.value = feedUrl;
            
            document.body.appendChild(textArea);
            
            textArea.select();
            
            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
                if (successful){
                    this.copyLinkText = 'Copied!'
                }else{
                    this.copyLinkText = 'There was a problem. The copy didn\'t work.';
                }
            } catch (err) {
                console.log('Oops, unable to copy');
                this.copyLinkText = 'There was a problem. The copy didn\'t work.';
            }
            
            document.body.removeChild(textArea);
            var self = this;
            setTimeout(function(){
                self.copyLinkText = 'Click here to copy RSS Feed URL';
            }, 2500);
        }
    }
});
