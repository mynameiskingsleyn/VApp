<?php
if (env('APP_ENV') == 'local' || env('APP_ENV') == 'staging') {
    return array(
        /* dev and test */
        'dc' => array(
            'login' => false,  // false: connect localDB; true: from dealerconnect
        )
    );
} else {

    return array(
        /*production  */
        'dc' => array(
            'login' => false,  // false: connect localDB; true: from dealerconnect
        )
    );
}
