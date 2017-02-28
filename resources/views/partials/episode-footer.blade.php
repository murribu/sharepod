<div class="panel-footer" v-if="user && user.verified">
    <div class="episode-action">
        <button class="btn-recommend" @click.prevent="recommendEpisode(episode)">
            <div class="icon-container" title="Recommend">
                <i class="fa fa-reply"></i>
            </div>
            <div class="icon-text-container">
                <span>@{{episode.total_recommendations}}</span>
            </div>
        </button>
    </div>
    <div class="episode-action">
        <button class="btn-episode-like" :class="{'active': episode.this_user_likes}" @click.prevent="toggleLikeEpisode(episode)">
            <div class="icon-container" title="Unike" v-if="episode.this_user_likes">
                <div class="heart-container">
                    <div class="heart"></div>
                </div>
            </div>
            <div class="icon-container" title="Like" v-if="!episode.this_user_likes">
                <div class="heart-container">
                    <div class="heart"></div>
                </div>
            </div>
            <div class="icon-text-container">
                <span>@{{episode.total_likes}}</span>
            </div>
        </button>
    </div>
    <div class="episode-action">
        <button class="btn-add-to-playlist" @click.prevent="selectEpisodeForAddingToPlaylist(episode)">
            <div class="icon-container" title="Add To Playlist">
                <i class="fa fa-plus"></i>
            </div>
            <div class="icon-text-container">
                <span>@{{episode.total_playlists}}</span>
            </div>
        </button>
    </div>
    <div class="episode-action">
        <button :class="{'active': episode.this_user_archived}" class="btn-archive-episode" @click.prevent="toggleArchiveEpisode(episode)">
            <div class="icon-container" title="Archive" v-if="!episode.this_user_archived">
                <i class="fa fa-floppy-o"></i>
            </div>
            <div class="icon-container" title="Unarchive" v-if="episode.this_user_archived">
                <i class="fa fa-floppy-o"></i>
                <div class="btn-hover-container" v-if="episode.result_slug == 'ok'">
                    Archived
                </div>
                <div class="btn-hover-container" v-if="!episode.result_slug">
                    Archive Requested
                </div>
            </div>
        </button>
    </div>
</div>