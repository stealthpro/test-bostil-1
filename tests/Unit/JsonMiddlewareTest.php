<?php

namespace Tests\Unit;

use App\Http\Middleware\JsonMiddleware;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class JsonMiddlewareTest extends TestCase
{
    public function testCheckAcceptHeader()
    {
        $request = new Request();
        $middleware = new JsonMiddleware();

        $middleware->handle($request, function (Request $request) {
            $this->assertEquals('application/json', $request->header('Accept'));
        });
    }
}
