<form class="form-horizontal" role="form">
    @if (Spark::usesTeams() && Spark::onlyTeamPlans())
        <!-- Team Name -->
        <div class="form-group" :class="{'has-error': registerForm.errors.has('team')}" v-if=" ! invitation">
            <label class="col-md-4 control-label">{{ ucfirst(Spark::teamString()) }} Name</label>

            <div class="col-md-6">
                <input type="name" class="form-control" name="team" v-model="registerForm.team" autofocus>

                <span class="help-block" v-show="registerForm.errors.has('team')">
                    @{{ registerForm.errors.get('team') }}
                </span>
            </div>
        </div>

        @if (Spark::teamsIdentifiedByPath())
            <!-- Team Slug (Only Shown When Using Paths For Teams) -->
            <div class="form-group" :class="{'has-error': registerForm.errors.has('team_slug')}" v-if=" ! invitation">
                <label class="col-md-4 control-label">{{ ucfirst(Spark::teamString()) }} Slug</label>

                <div class="col-md-6">
                    <input type="name" class="form-control" name="team_slug" v-model="registerForm.team_slug" autofocus>

                    <p class="help-block" v-show=" ! registerForm.errors.has('team_slug')">
                        This slug is used to identify your team in URLs.
                    </p>

                    <span class="help-block" v-show="registerForm.errors.has('team_slug')">
                        @{{ registerForm.errors.get('team_slug') }}
                    </span>
                </div>
            </div>
        @endif
    @endif
    
    <div class="form-group">
        <div class="row" style="margin-bottom:15px;margin-top:30px;">
            <div class="col-xs-6 col-xs-offset-3">
                <a title="Twitter" class="btn btn-block btn-social btn-twitter" href="/auth/twitter" @click.prevent="signInWithTwitter">
                    <span class="fa fa-twitter"></span> Sign in with Twitter
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
                <a title="Facebook" class="btn btn-block btn-social btn-facebook" href="/auth/facebook" @click.prevent="signInWithFacebook">
                    <i class="fa fa-facebook"></i> Sign in with Facebook
                </a>
            </div>
        </div>
    </div>
    
    <div class="form-group" style="position:relative;padding:50px;">
        <div style="border-top: #333 solid;width: 100%;top: 0px;z-index: 1;left: 50%;" ></div>
        <h4 style="z-index: 2;margin: 2px;background-color: #fff;left: calc(50% - 11px);position: absolute;top: calc(50% - 15px);padding:5px;">OR</h4>
    </div>

    <!-- Name -->
    <div class="form-group" :class="{'has-error': registerForm.errors.has('name')}">
        <label class="col-md-4 control-label">Name</label>

        <div class="col-md-6">
            <input type="name" class="form-control" name="name" v-model="registerForm.name" autofocus>

            <span class="help-block" v-show="registerForm.errors.has('name')">
                @{{ registerForm.errors.get('name') }}
            </span>
        </div>
    </div>

    <!-- E-Mail Address -->
    <div class="form-group" :class="{'has-error': registerForm.errors.has('email')}">
        <label class="col-md-4 control-label">E-Mail Address</label>

        <div class="col-md-6">
            <input type="email" class="form-control" name="email" v-model="registerForm.email">

            <span class="help-block" v-show="registerForm.errors.has('email')">
                @{{ registerForm.errors.get('email') }}
            </span>
        </div>
    </div>

    <!-- Password -->
    <div class="form-group" :class="{'has-error': registerForm.errors.has('password')}">
        <label class="col-md-4 control-label">Password</label>

        <div class="col-md-6">
            <input type="password" class="form-control" name="password" v-model="registerForm.password">

            <span class="help-block" v-show="registerForm.errors.has('password')">
                @{{ registerForm.errors.get('password') }}
            </span>
        </div>
    </div>

    <!-- Password Confirmation -->
    <div class="form-group" :class="{'has-error': registerForm.errors.has('password_confirmation')}">
        <label class="col-md-4 control-label">Confirm Password</label>

        <div class="col-md-6">
            <input type="password" class="form-control" name="password_confirmation" v-model="registerForm.password_confirmation">

            <span class="help-block" v-show="registerForm.errors.has('password_confirmation')">
                @{{ registerForm.errors.get('password_confirmation') }}
            </span>
        </div>
    </div>

    <!-- Terms And Conditions -->
    <div v-if=" ! selectedPlan || selectedPlan.price == 0">
        <div class="form-group" :class="{'has-error': registerForm.errors.has('terms')}">
            <div class="col-md-6 col-md-offset-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="terms" v-model="registerForm.terms">
                        I Accept The <a href="/terms" target="_blank">Terms Of Service</a>
                    </label>

                    <span class="help-block" v-show="registerForm.errors.has('terms')">
                        @{{ registerForm.errors.get('terms') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
                <button class="btn btn-primary" @click.prevent="register" :disabled="registerForm.busy">
                    <span v-if="registerForm.busy">
                        <i class="fa fa-btn fa-spinner fa-spin"></i>Registering
                    </span>

                    <span v-else>
                        <i class="fa fa-btn fa-check-circle"></i>Register
                    </span>
                </button>
            </div>
        </div>
    </div>
</form>
