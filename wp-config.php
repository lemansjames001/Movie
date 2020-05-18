<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp-1' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'C^*O4W.npG4+tCA`LA:PIjuONNVrZ)7xwa6m&)&1pDy6}Jw [I=`N~!}{tr&dIh5' );
define( 'SECURE_AUTH_KEY',  '?:>Q{6O(EuL*}9TW$+aP(5psS7_pqq4;ZOuodrc;P*$_HDcJvS8e$Sc)}@4 k#_{' );
define( 'LOGGED_IN_KEY',    'TWj0;:6)Lj(Yj W[=,?`}F}t|xEtM3;^L(2WP)#ZbJ`Jo/@+[ORMa7ts6`7, QU.' );
define( 'NONCE_KEY',        ':o@TL`IMkCP:kzb,Zt`De9UVWJ_WnbcHpv?WtQk5%{Ko6vUgLW?P1 GW,m%HXLQ@' );
define( 'AUTH_SALT',        's W[;Md=KwE(sZe)sxlVXyMK;d>pJ4Npj2,DeQ8fQ-eLA^_Vwfn;Gr.C4TYpIh!=' );
define( 'SECURE_AUTH_SALT', '.4JjmK[P=-XXX&H~V8t4n:k}&66gT71|^&V;!{sU_%g}rkA6@ dAD$tnHCWjy[>v' );
define( 'LOGGED_IN_SALT',   ']|}0*5_Gd2Thdxaa`i4 I}]l.bjaark#mTsXqO1inkyZ:5D%3u=[4`avq7SG`XzW' );
define( 'NONCE_SALT',       'DBjDowsYzq-Yr,/gw?lSv@%.t$[]#+Ll`li8%EzCv=xnwATIK*?Z%:blh]sr@Rx4' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
