<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u425263752_txx1M' );

/** Database username */
define( 'DB_USER', 'u425263752_SQbpZ' );

/** Database password */
define( 'DB_PASSWORD', 'o!a4&X(ePe' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '_iC|6,R/E`pb@^%-R}m[OQk1dm%i.nF f(-P N>v_AS4:UVDh6cCivpLPi^P{|Yz' );
define( 'SECURE_AUTH_KEY',   '`WkTwvvOBFl>DK7!fbO_@FhDt4 2v4p)8_W*~6BwdXtwl1I6FncHU-eq,82FE/}7' );
define( 'LOGGED_IN_KEY',     '|{Ba2Ct9rr(0&g7070;ZAeZ/TF%GNPL9u&$-oIz5PABpqu./)G[X|ZNgOsW0Y-@#' );
define( 'NONCE_KEY',         'A.$vnO3y*A^he?g;TTsM^P!1mFsJ+A.-X|c~#Y:&tsydxkm w<3ejN|FZ]-f/!hW' );
define( 'AUTH_SALT',         'E*(C(0!??VB.:a3i$=~F4w9QZ2FAC{zZj{t`w!h^I`8V7.[#Lk4qOSzyXe$4@9@+' );
define( 'SECURE_AUTH_SALT',  '^W:Aid4(Mh77m8.p71,FeL=td/k/UkhE/EF4Jgl<{fy_{:OE4QAmDXQcJEX:c.+M' );
define( 'LOGGED_IN_SALT',    '*md:BMp9jpnF-w=s3tW`,Dx.V:Tk0HuY.~aZ$6u=gfAKdG9_qc~vm$0)!!0au+t5' );
define( 'NONCE_SALT',        '/SeU9,RgR V92|mnW#:*h%1q~KNn]FbqkDbtmcg[gHV@DR^JeSBWm^4IPOCQi^[B' );
define( 'WP_CACHE_KEY_SALT', '^vUgmH>ZbNjY0QC(]rycjSy=[`/|mm-Q9wHLAEs?In%SX6)Ng@-V]~#qFkRv*U}j' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', '08d6c00cd32d751f8572f8f0e96590dd' );
define( 'WP_AUTO_UPDATE_CORE', false );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
