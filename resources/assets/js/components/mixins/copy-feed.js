module.exports = {
    methods: {
        copyFeed(feedUrl, ele){
            var textArea = document.createElement("input");
            var originalText = $("#" + ele).html();

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
                    $("#" + ele).html('Copied!');
                }else{
                    $("#" + ele).html('There was a problem. The copy didn\'t work.');
                }
            } catch (err) {
                console.log('Oops, unable to copy');
                $("#" + ele).html('There was a problem. The copy didn\'t work.');
            }
            
            document.body.removeChild(textArea);
            var self = this;
            setTimeout(function(){
                $("#" + ele).html(originalText);
            }, 2500);
        }
    }
}