<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        "logout",
        "getunitid",
        "getlastpositionsbyassets",
        "gettripbyasset",
        "getviagensbydatarange",
        "getlinhasitinerarios",
        "getsystemconfigs",
        "playeraovivo",
        "api/*",
        "getassetpositionsinotico",
        "getassetpositionsinotico_new",
        'reports_generate_sinotico',
        'get_real_time_positions',
    ];
}
