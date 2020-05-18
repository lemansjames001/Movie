<?php

// Create a helper function for easy SDK access.

if ( !function_exists( 'bepof_fs' ) ) {
    function bepof_fs()
    {
        global  $bepof_fs ;
        if ( !isset( $bepof_fs ) ) {
            $bepof_fs = fs_dynamic_init( array(
                'id'             => '1021',
                'slug'           => 'bulk-edit-posts-on-frontend',
                'type'           => 'plugin',
                'public_key'     => 'pk_5c389ae3fec7d724350dcbdd315ed',
                'is_premium'     => true,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                'days'               => 7,
                'is_require_payment' => true,
            ),
                'menu'           => array(
                'slug'       => 'vgsefe_welcome_page',
                'first-path' => 'admin.php?page=vgsefe_welcome_page',
                'support'    => false,
            ),
                'is_live'        => true,
            ) );
        }
        return $bepof_fs;
    }
    
    // Init Freemius.
    bepof_fs();
    // Signal that SDK was initiated.
    do_action( 'bepof_fs_loaded' );
}
