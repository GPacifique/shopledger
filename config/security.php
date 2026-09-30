<?php

// File location: config/security.php

return [

    /*
    |--------------------------------------------------------------------------
    | Content-Security-Policy mode
    |--------------------------------------------------------------------------
    |
    | off     - do not send a CSP header
    | report  - send Content-Security-Policy-Report-Only (nothing is blocked;
    |           violations show in the browser console). Use while testing.
    | enforce - send Content-Security-Policy (violations are blocked)
    |
    */

    'csp_mode' => env('CSP_MODE', 'report'),

];