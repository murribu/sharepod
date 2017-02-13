<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Show;
class RefreshFeed extends Command
{
    protected $signature = 'refreshfeed {--show_id=0}';
    
    public function handle() {
        if ($this->option('show_id')){
            dd($this->option('show_id'));
            $show = Show::find($this->option('show_id'));
            $ret = $show->parseFeed();
        }else{
            $ret = Show::updateOneFeed();
            if ($ret != ""){
                $this->info($ret);
            }
        }
    }
}