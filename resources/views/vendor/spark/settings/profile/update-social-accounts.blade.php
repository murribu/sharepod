<update-social-accounts :user="user" inline-template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">
                Social
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="row" style="margin-bottom:15px;margin-top:30px;">
                        <div class="col-xs-6 col-xs-offset-3" v-if="!user.twitter_user_id">
                            <a title="Twitter" class="btn btn-block btn-social btn-twitter" href="/auth/twitter" @click.prevent="linkWithTwitter">
                                <span class="fa fa-twitter"></span> Link your Twitter account
                            </a>
                        </div>
                        <div class="col-xs-6 col-xs-offset-3" v-if="user.twitter_user_id">
                            <a title="Twitter" class="btn btn-block btn-social btn-twitter" href="#" @click.prevent="approveUnlinkWithTwitter">
                                <i class="fa fa-twitter"></i> Linked!
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-3" v-if="!user.facebook_user_id">
                            <a title="Facebook" class="btn btn-block btn-social btn-facebook" href="/auth/facebook" @click.prevent="linkWithFacebook">
                                <i class="fa fa-facebook"></i> Link your Facebook account
                            </a>
                        </div>
                        <div class="col-xs-6 col-xs-offset-3" v-if="user.facebook_user_id">
                            <a title="Facebook" class="btn btn-block btn-social btn-facebook" href="#" @click.prevent="approveUnlinkWithFacebook">
                                <i class="fa fa-facebook"></i> Linked!
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="modal-unlink-facebook" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        
                        <h4 class="modal-title">Unlink your Facebook account</h4>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to unlink your Facebook account?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" type="button" data-dismiss="modal">No, Go Back</button>
                        <button class="btn btn-danger" @click="unlinkWithFacebook">Yes, unlink</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="modal-unlink-twitter" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                        
                        <h4 class="modal-title">Unlink your Twitter account</h4>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to unlink your Twitter account?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" type="button" data-dismiss="modal">No, Go Back</button>
                        <button class="btn btn-danger" @click="unlinkWithTwitter">Yes, unlink</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</update-social-accounts>