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
define( 'DB_NAME', 'movie' );

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
define( 'AUTH_KEY',         's,&H@N LEi D0H{2)h$s=zjIKQdHm$&(b-HoC?H5gb+UJMc)#%4zTAxS{Qcse}5h' );
define( 'SECURE_AUTH_KEY',  '?EPug|~{EBF?2]xEVVvd|-/o_VP#cF/4G H8rEHW_{U$B^qD+VFc~xbHtth7Ika{' );
define( 'LOGGED_IN_KEY',    'kG-oZad|sRj0b<3wc+[Pt}ou$jeKl7%n9lwI0rHMz2StL&m-4Ze|J5gOz_yKi5Ge' );
define( 'NONCE_KEY',        '^^bK;ngHr}5:WCNw]hx4$S{1XkeK?>[w?*K.5az)EjREy=YMi+,Z)dP0^PGDR}zF' );
define( 'AUTH_SALT',        '<sEL_9:|^FYpp?nzQ@5,l4xvzlD&z9&qmCvE@+PZ%.SiLb.FTqhHc;44V?>h%Eax' );
define( 'SECURE_AUTH_SALT', '`aQRtPja6{?b))b]rw<lR+Q^g=0c>gv/U$C#<#Hl6^W+iM7+f77_QS#(R*1mD)+v' );
define( 'LOGGED_IN_SALT',   'ku]R-$R-eW<G{~_]?k^cqD>M{i^.0c[9:wI6Sj6ewJS-K[rNpB2Uf%Wx=+_S?gjT' );
define( 'NONCE_SALT',       '0:;^)9r*.X8V9JgIrE:D eaq8YUPuQ^$&24;P`3T|N)=`n}dYB[N:X[mL@((V<-@' );

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
