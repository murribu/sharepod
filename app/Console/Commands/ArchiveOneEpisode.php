<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Spark\Contracts\Repositories\NotificationRepository;

use App\ArchivedEpisode;
use App\Show;

class ArchiveOneEpisode extends Command{
    protected $signature = 'archive_one_episode';
    
    public function handle(NotificationRepository $notifications) {
        $ret = ArchivedEpisode::archive_one_episode($notifications);
    }
}