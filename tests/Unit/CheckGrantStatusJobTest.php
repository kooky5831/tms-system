<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Jobs\CheckGrantStatusJob;


class CheckGrantStatusJobTest extends TestCase
{
     /**
     * A basic test example.
     *
     * @return void
     */
    public function testCheckGrantStatusTest()
    {
       $data = ['key' => 'value'];

       CheckGrantStatusJob::dispatch();
    }
}
