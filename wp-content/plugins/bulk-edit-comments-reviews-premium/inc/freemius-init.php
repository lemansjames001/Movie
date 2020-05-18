<?php

if ( !function_exists( 'wpsecr_fs' ) ) {
    // Create a helper function for easy SDK access.
    function wpsecr_fs()
    {
        global  $wpsecr_fs ;
        
        if ( !isset( $wpsecr_fs ) ) {
            // Activate multisite network integration.
            if ( !defined( 'WP_FS__PRODUCT_5156_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_5156_MULTISITE', true );
            }
            $wpsecr_fs = fs_dynamic_init( array(
                'id'             => '5156',
                'slug'           => 'bulk-edit-comments-reviews',
                'type'           => 'plugin',
                'public_key'     => 'pk_ee5dacb4c5f0d137695033a5434de',
                'is_premium'     => true,
                'premium_suffix' => 'Pro',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug'       => 'wpsecr_welcome_page',
                'first-path' => 'admin.php?page=wpsecr_welcome_page',
                'support'    => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $wpsecr_fs;
    }
    
    // Init Freemius.
    wpsecr_fs();
    // Signal that SDK was initiated.
    do_action( 'wpsecr_fs_loaded' );
}
