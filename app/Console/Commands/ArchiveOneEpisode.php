<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Spark\Contracts\Repositories\NotificationRepository;

use App\ArchivedEpisode;
use App\Show;

class ArchiveOneEpisode extends Command{
    protected $signature = 'archive_one_episode';
    
    public function handle(NotificationRepository $notifications) {
        $lockfile = "/tmp/episode_archive.lock";

        if(!file_exists($lockfile))
            $fh = fopen($lockfile, "w");
        else
            $fh = fopen($lockfile, "r");

        if($fh === FALSE)
            $this->info("Unable to open lock file");

        if(!flock($fh, LOCK_EX)) // another process is running
            return;

        $ret = ArchivedEpisode::archive_one_episode($notifications);
        if($ret){
            $this->info($ret);
        }
        
        flock($fh, LOCK_UN);
    }
}