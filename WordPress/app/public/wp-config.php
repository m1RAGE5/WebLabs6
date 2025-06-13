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
define( 'DB_NAME', 'minadesuki6241_sJEvcKAGXBDh7Uiyrm0I' );

/** Database username */
define( 'DB_USER', 'minadesuki6241_rapotazase9110' );

/** Database password */
define( 'DB_PASSWORD', 'cmAkoh4LVXM0fIw6REOJ' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',          'Kg3CI.4*W1wqw>fx:QNJ2(e3OJs=CRXFjiv{hLaMW8O hkd8~{}lYv64y+!2w(<O' );
define( 'SECURE_AUTH_KEY',   '`SW_LvkFKu[fO74xA`9%h]|G308O{L_yV{rC&KJNG=iN<aAR15~M/`r*cR,]N:qp' );
define( 'LOGGED_IN_KEY',     'h}|Jxx.4xWRR&|$saJ4v#BizR5gh,)I#l+YX/Wne@]-vMJj(<RpW[q:vVOc*:*<|' );
define( 'NONCE_KEY',         '!FV@PxLS$L5Nh{JrgtyrF:WgFzY`KV?|%u`(T<Y`b2 +3So{AA.!tLGD2#8A<:ND' );
define( 'AUTH_SALT',         '(B(SW@<}#zmFAgP~>HvJnPuL.g3~@A0r(}ZQPdLe1@-+H{nK.z>bQ!BMsgw|o0{ ' );
define( 'SECURE_AUTH_SALT',  'CZ;uG|/(Fby^UeD_dX}{M/zGHNsMr,q~-pqj9lCy* /[E2m/>*;P2!:>pWQa%3dC' );
define( 'LOGGED_IN_SALT',    'H))#q`n~f/b]XHO~po83-?w2D(V1[9N-h>J-k6W=%h-:c%(Z}cqT{X$&m[Z297+O' );
define( 'NONCE_SALT',        '4MHp]Gzdq_vM*<+>:l_Hid@&qg,SkPG}OElC[Q_lah3CsZj{`C{(s3{cQk>/FlG;' );
define( 'WP_CACHE_KEY_SALT', '0h0j1Jo1.St}342p.M_}Uc,3pohKr&Ask<9}^=/r3_o1l>A*[&H[FiqDf,O83H/Q' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'iwp6ba7_';


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

define( 'WP_AUTO_UPDATE_CORE', false );
define( 'WP_DEBUG_LOG', false );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
