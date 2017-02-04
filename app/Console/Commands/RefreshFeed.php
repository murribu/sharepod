<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Show;
class RefreshFeed extends Command
{
    protected $signature = 'refreshfeed';
    
    public function handle() {
        $ret = Show::updateOneFeed();
        if ($ret != ""){
            $this->info($ret);
        }
    }
}