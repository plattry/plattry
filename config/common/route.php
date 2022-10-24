<?php

return [
    "route" => [
        "http" => [
            [
                "methods"     => ["get", "options"],
                "path"        => "/",
                "middlewares" => [
                    \App\Http\Middleware\VerifyCors::class
                ],
                "target"      => "App\Http\Controller\IndexController@index"
            ]
        ]
    ]
];
