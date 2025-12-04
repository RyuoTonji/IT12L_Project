<?php
// ===========================================================================
// app/Http/Middleware/EncryptCookies.php
// ===========================================================================
namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    protected $except = [];
}

// ===========================================================================
// app/Http/Middleware/PreventRequestsDuringMaintenance.php
// ===========================================================================
namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    protected $except = [];
}

// ===========================================================================
// app/Http/Middleware/TrimStrings.php
// ===========================================================================
namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}

// ===========================================================================
// app/Http/Middleware/TrustProxies.php
// ===========================================================================
namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    protected $proxies;
    protected $headers = Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_AWS_ELB;
}

// ===========================================================================
// app/Http/Middleware/VerifyCsrfToken.php
// ===========================================================================
namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [];
}