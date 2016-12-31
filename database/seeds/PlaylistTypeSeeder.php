<?php

use Illuminate\Database\Seeder;
use App\PlaylistType;

class PlaylistTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
			$types = ['Recommendations','MixTape'];
			foreach($types as $type){
                $pt = PlaylistType::where('name', $type)->first();
				if (!$pt){
					$pt = new PlaylistType;
                    $pt->name = $type;
                    $pt->save();
				}
			}
		}
}