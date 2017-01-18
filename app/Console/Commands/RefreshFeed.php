<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Show;
class RefreshFeed extends Command
{
    protected $signature = 'refreshfeed';
    
    public function handle() {
        $this->info(Show::updateOneFeed());
    }
}