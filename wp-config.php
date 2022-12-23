<?php
define( 'WP_CACHE', true );
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
define( 'DB_NAME', 'u794924610_gKWL8' );

/** Database username */
define( 'DB_USER', 'u794924610_XP10v' );

/** Database password */
define( 'DB_PASSWORD', 'H49CBt7cdS' );

/** Database hostname */
define( 'DB_HOST', 'mysql' );

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
define( 'AUTH_KEY',          ',GiYqd5FN5;G{g=div]F%$f`Y[G}*PsCii$,t7mzANTENIR~o9SO3w`<]vd_JoK,' );
define( 'SECURE_AUTH_KEY',   '0gC`c!$jjnHCJ,7JxaHY7r?en7lO!9_0^?9jLTi,;gn*//TRG`*Q6p7~v>@eyBw@' );
define( 'LOGGED_IN_KEY',     'pWG24_?fFcF*5?e3Uu{gra}b7AS8D`7u>%{7[r!`ZVm_k{[^A#I>8n.Y^Ipnr(F>' );
define( 'NONCE_KEY',         '{h@Na.Zqb+s@Q|pVMOGpxJ`<8b@HuX%li;We152iHG1Q$xn:l`/jw)p:RP2spr/]' );
define( 'AUTH_SALT',         '*lC^<}{[l<[01oEiu(&;d=N15YyA@|[9ua7d4Al5k/#cfOC-bUk_${LsQGao34ek' );
define( 'SECURE_AUTH_SALT',  'YUB[ZBEyMpS tzd|%*k*ky~w|LgE,<*mX}0b~xP.~UXAC8@:(r isQmZ0J6F>JKn' );
define( 'LOGGED_IN_SALT',    'p6-y<`9li/#C6K822 kiZk/k 0e3*nj~QHdw^`GKZ9e6|z%<n]=sIZ^tJ|I}x&Fr' );
define( 'NONCE_SALT',        '>#bdBN9DHdhGQd5*cLk&yY!P8+nZbLM*%3uCFY{pzn[,SZ2S,lbKI9cOlim^cknm' );
define( 'WP_CACHE_KEY_SALT', 'w.Ja_ds>e-fkMbyBmLnbYs,iqlRpu(VNV-h~@YL}v8f`rW<$rfHX{)g0oKI;7q|I' );


/**#@-*/

/**
 * WordPress database table prefix.
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
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );


/* Add any custom values between this line and the "stop editing" line. */



define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
