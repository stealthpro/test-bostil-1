<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Testing\TestResponse;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        TestResponse::macro('assertJsonResource', function (JsonResource $resource) {
            $this->assertJson($resource->response()->getData(true));
        });

        TestResponse::macro('assertJsonResourceFragment', function (JsonResource $resource) {
            $this->assertJsonFragment($resource->jsonSerialize());
        });

        return $app;
    }
}
