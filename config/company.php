<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    |
    | This file stores all the company-related information that can be used
    | across the application. This allows easy modification without having
    | to update multiple files when company details change.
    |
    */

    'name' => env('COMPANY_NAME', 'CV. XXXX'),
    'address' => env('COMPANY_ADDRESS', 'JL. Mochammad Toha No. XXX'),
    'phone' => env('COMPANY_PHONE', 'Telp. XXX'),
    'email' => env('COMPANY_EMAIL', 'email@company.com'),
    'npwp' => env('COMPANY_NPWP', 'NPWP: XXX-XXX-XXX.XXX.XXX'),
];