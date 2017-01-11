<!-- Right Side Of Navbar -->
<li v-if="!user.verified && !verification_email_sent" class="verification-alert"><a href="#" @click.prevent="sendVerificationEmail">Your email address needs to be verified<br>Click here to re-send verification.</a></li>
<li v-if="verification_email_sent" class="verification-alert"><a href="#">Check your email for a verification link.</a></li>