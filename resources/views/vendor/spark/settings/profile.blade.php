<spark-profile :user="user" inline-template>
    <div>
        <!-- Update Profile Photo -->
        @include('spark::settings.profile.update-profile-photo')
        
        @include('spark::settings.profile.update-social-accounts')
        
        <!-- Update Contact Information -->
        @include('spark::settings.profile.update-contact-information')
    </div>
</spark-profile>
