<?php

namespace BULK_IMPORT\Inc;

use BULK_IMPORT\Inc\Traits\Program_Logs;
use BULK_IMPORT\Inc\Traits\Singleton;

class Sign_Generator {

    use Singleton;
    use Program_Logs;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        // setup hooks
    }
}