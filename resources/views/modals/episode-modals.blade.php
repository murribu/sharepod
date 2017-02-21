
        <div class="modal fade" id="modal-select-playlist" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true" class="btn btn-primary btn-separate-from-other-buttons">&times;</button>
                        <h4 class="modal-title">@{{show ? show.name : ''}}<br>@{{selectedEpisode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            Add this episode to:
                        </div>
                        <button class="btn btn-primary btn-separate-from-other-buttons" v-for="playlist in playlists" @click.prevent="addepisodeToPlaylist(playlist)">@{{playlist.name}}</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-no-playlists" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">@{{show ? show.name : ''}}<br>@{{selectedEpisode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        You have no Playlists. <a href="/playlists/new">Click here</a> to create one.
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-add-to-playlist-success" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">@{{show ? show.name : ''}}<br>@{{selectedEpisode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        Success! You added this episode to the @{{selectedPlaylist.name}} playlist. <a :href="'/playlists/' + selectedPlaylist.slug">Click here</a> to see it
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-recommend-episode-1" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">@{{show ? show.name : ''}}<br>@{{selectedEpisode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <label class="control-label col-xs-12" for="comment">Comment (optional):</label>
                            <div class="col-xs-12">
                                <textarea name="comment" class="form-control" type="textarea" v-model="recommendationComment" placeholder="This episode made me think of you!" ></textarea>
                            </div>                        
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xs-12">
                                Recommend this episode to:
                                <button class="btn btn-primary btn-separate-from-other-buttons" v-for="user in recentRecommendees" @click.prevent="recommendEpisodeToExistingUser(user.slug)">@{{user.name}}</button>
                                <button class="btn btn-primary" @click.prevent="recommendEpisodeToSomeoneElse">Someone else</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-recommend-episode-2" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">@{{show ? show.name : ''}}<br>@{{selectedEpisode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <label class="control-label col-xs-12" for="comment">Comment (optional):</label>
                            <div class="col-xs-12">
                                <textarea name="comment" class="form-control" type="textarea" v-model="recommendationComment" placeholder="This episode made me think of you!" ></textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="row form-group control">
                            <label class="control-label col-xs-12 col-sm-2" for="recommendEmail">Email</label>
                            <div class="col-xs-12 col-sm-10">
                                <input class="form-control" v-validate="'required|email'" :class="{'input': true, 'is-danger': errors.has('recommendEmail') }" name="recommendEmail" type="text" placeholder="Email" v-model="recommendEmail">
                                <div v-show="errors.has('recommendEmail')" class="alert alert-danger">Please enter a valid email address</div>
                            </div>
                        </div>
                        <div class="row" style="margin-top:10px;">
                            <button class="btn btn-primary pull-right" style="margin-right: 15px;" @click="sendRecommendation()" :disabled="recommendEmail == '' || errors.has('recommendEmail')">
                                <span>
                                    <i class="fa fa-btn" :class="{'fa-spinner fa-spin': recommendForm.busy, 'fa-check-circle': !recommendForm.busy}"></i>Send recommendation
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-recommend-success" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">@{{show ? show.name : ''}}<br>@{{selectedEpisode.name}}</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            Success! You have recommended this podcast episode
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-max-recommendations" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Max Recommendations</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            You have reached today's maximum number of Recommendations for your Subscription Plan.<br><a href="/settings#/subscription">Click here</a> to change your Plan.
                        </div>
                    </div>
                </div>
            </div>
        </div>