<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Spark\Contracts\Repositories\NotificationRepository;

use App\ArchivedEpisode;
use App\Show;

class CleanOutTmp extends Command{
    protected $signature = 'clean_out_tmp';
    
    public function handle(NotificationRepository $notifications) {
        $ret = [];
        $lockfile = "/tmp/episode_archive.lock";

        if(!file_exists($lockfile))
            $fh = fopen($lockfile, "w");
        else
            $fh = fopen($lockfile, "r");

        if($fh === FALSE) return ['error' => "Unable to open lock file"];

        if(!flock($fh, LOCK_EX)) // another process is running
            return [];

        $files = glob('/tmp/*.mp3'); // get all mp3 files
        foreach($files as $file){ // iterate files
            if(is_file($file))
                unlink($file); // delete file
        }
        
        flock($fh, LOCK_UN);
    }
}