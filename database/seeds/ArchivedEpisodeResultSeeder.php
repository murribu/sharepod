<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\ArchivedEpisodeResult;

class ArchivedEpisodeResultSeeder extends Seeder{
    public function run(){
        $results = array(
            array(
                'slug'      => 'ok',
                'name'      => 'OK',
                'success'   => '1',
            ),
            array(
                'slug'      => 'http-unauthorized',
                'name'      => '401 Unauthorized',
                'success'   => '0',
            ),
            array(
                'slug'      => 'http-payment-required',
                'name'      => '402 Payment Required',
                'success'   => '0',
            ),
            array(
                'slug'      => 'http-forbidden',
                'name'      => '403 Forbidden',
                'success'   => '0',
            ),
            array(
                'slug'      => 'http-not-found',
                'name'      => '404 Not Found',
                'success'   => '0',
            ),
            array(
                'slug'      => 'http-internal-error',
                'name'      => '500 Internal Server Error',
                'success'   => '0',
            ),
            array(
                'slug'      => 'dj-storage-limit-exceeded',
                'name'      => 'Storage Limit Exceeded',
                'success'   => '0',
            ),
            array(
                'slug'      => 'dj-local-file-storage-problem',
                'name'      => 'Internal Error - Could not create a file in the /tmp directory',
                'success'   => '0',
            ),
        );
        
        foreach($results as $result){
            $r = ArchivedEpisodeResult::where('slug', $result['slug'])->first();
            if (!$r){
                $r = new ArchivedEpisodeResult;
            }
            $r->slug = $result['slug'];
            $r->name = $result['name'];
            $r->success = $result['success'];
            $r->save();
        }
    }
}