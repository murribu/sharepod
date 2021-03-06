
/*
 |--------------------------------------------------------------------------
 | Laravel Spark Components
 |--------------------------------------------------------------------------
 |
 | Here we will load the Spark components which makes up the core client
 | application. This is also a convenient spot for you to load all of
 | your components that you write while building your applications.
 */

require('./../spark-components/bootstrap');

require('./home');
require('./connections');
require('./episode');
require('./help');
require('./playlist');
require('./playlists');
require('./playlist-edit');
require('./recommendation');
require('./recommendations');
require('./show');
require('./shows');
require('./user');

require('./directives/tooltip');

require('./mixins/episode-actions');
require('./mixins/copy-feed');