<spark-update-slug :user="user" inline-template>
    <div class="panel panel-default">
        <div class="panel-heading">Handle</div>

        <div class="panel-body">
            <!-- Success Message -->
            <div class="alert alert-success" v-if="successful">
                Your handle has been updated! Don't forget to <a :href="'/users/' + newSlug">re-register your Recommendation Feed</a>
            </div>
            
            <form class="form-horizontal">
                <!-- handle -->
                <div class="form-group" :class="{'has-error': dirty && error != '', 'has-success': success}">
                    <label class="col-md-4 control-label">Handle</label>

                    <div class="col-md-6">
                        <input type="text" class="form-control" name="handle" v-model="slug">

                        <span class="help-block" v-show="dirty && error != ''">
                            @{{ error }}
                        </span>
                        <span class="help-block" v-show="success == '1'">
                            Success! Your handle has been updated.<br><a href="/help#/register-my-feed">You'll need to re-register your Recommendation Feed</a>
                        </span>
                    </div>
                </div>

                <!-- Update Button -->
                <div class="form-group">
                    <div class="col-md-offset-4 col-md-6">
                        <button type="submit" class="btn btn-primary"
                                @click.prevent="updateSlug"
                                :disabled="busy || error != '' || !dirty">

                            Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</spark-update-slug>
