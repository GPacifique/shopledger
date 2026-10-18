<?php

// File location: config/security.php

return [

    // off | report | enforce
    'csp_mode' => env('CSP_MODE', 'report'),

    // Adds "preload" to the HSTS header. The header alone does nothing until
    // you submit the domain at hstspreload.org.
    'hsts_preload' => env('HSTS_PRELOAD', true),

    // Optional URL that receives CSP violation reports.
    'csp_report_uri' => env('CSP_REPORT_URI'),

];