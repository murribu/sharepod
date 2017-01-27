<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Podcatcher;
use App\PodcatcherPlatform;

class PodcatcherSeeder extends Seeder{
    public function run(){
        $podcatchers = array(
            array(
                'slug' => 'iphone-podcast-app',
                'name' => 'Podcasts App',
                'platforms' => ['iOS'],
                'url' => 'https://itunes.apple.com/us/app/podcasts/id525463029?mt=8',
                'url_register_feed' => 'http://www.apple.com/itunes/podcasts/fanfaq.html',
            ),
            array(
                'slug' => 'itunes',
                'name' => 'iTunes',
                'platforms' => ['macOS', 'Windows'],
                'url' => 'http://www.apple.com/itunes/',
                'url_register_feed' => 'https://blog.libsyn.com/2012/11/29/how-to-manually-subscribe-to-a-podcast-rss-feed-in-itunes-11/',
            ),
            array(
                'slug' => 'downcast',
                'name' => 'Downcast',
                'platforms' => ['macOS', 'iOS', 'watchOS'],
                'url' => 'http://www.downcastapp.com/',
                'url_register_feed' => 'http://support.downcastapp.com/',
            ),
            array(
                'slug' => 'pocketcasts',
                'name' => 'Pocket Casts',
                'platforms' => ['android', 'web'],
                'url' => 'https://play.pocketcasts.com/',
                'url_register_feed' => '',
            ),
            array(
                'slug' => 'stitcher',
                'name' => 'Stitcher',
                'platforms' => ['android', 'iOS', 'web'],
                'url' => 'https://www.stitcher.com/',
                'url_register_feed' => '',
            ),
            array(
                'slug' => 'spotify',
                'name' => 'Spotify',
                'platforms' => ['android', 'iOS', 'web', 'Windows', 'macOS'],
                'url' => 'https://www.spotify.com/',
                'url_register_feed' => 'https://support.spotify.com/us/using_spotify/lifestyle_features/podcasts/',
            ),
            array(
                'slug' => 'overcast',
                'name' => 'Overcast',
                'platforms' => ['iOS'],
                'url' => 'https://overcast.fm/',
                'url_register_feed' => '',
            ),
        );
        
        foreach($podcatchers as $podcatcher){
            $p = Podcatcher::where('slug', $podcatcher['slug'])->first();
            if (!$p){
                $p = new Podcatcher;
            }
            $p->slug = $podcatcher['slug'];
            $p->name = $podcatcher['name'];
            $p->url = $podcatcher['url'];
            $p->url_register_feed = $podcatcher['url_register_feed'];
            $p->save();
            foreach ($podcatcher['platforms'] as $platform){
                $pp = PodcatcherPlatform::firstOrCreate(['podcatcher_id' => $p->id, 'platform' => $platform]);
            }
        }
    }
}