<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Business Timezone
    |--------------------------------------------------------------------------
    |
    | Timestamps are persisted in UTC (see config/app.php), but reporting must
    | be bucketed by the store's local calendar day. Without this, a 1:00 AM
    | Manila order is stored as 5:00 PM UTC the previous day and would be
    | counted against the wrong day on the dashboard.
    |
    */

    'timezone' => env('STORE_TIMEZONE', 'Asia/Manila'),

];
