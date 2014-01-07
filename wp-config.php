<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'hrdd');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'mogwai');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '$-%BnDiQYq~N^>;@rCFag/!k+JDKPt#A0Wot4$mGQS5IDZUK7OdVh*@8yQt%M4A?');
define('SECURE_AUTH_KEY',  'cZ(uP92ANMXWz@(0[vGx+Zq]Cu5u7~=N?S,D){,Yx8E|`,U,EdHF-AI g(|F}D]r');
define('LOGGED_IN_KEY',    'Ns/gYpdC:f}+-.3~l{4>?^Ih~vVZXb=agGhPa%s_FNj2XxKTcywLVi-zV,H%5<-;');
define('NONCE_KEY',        'n#Bt~(Jjl};+(d-iJV!UPG`A,7$u#/gIP|il4]s^GXRKM.>C%(Z,BtJ7a4Tdw0&a');
define('AUTH_SALT',        'YFn/[v4lxkhb>-`^2j#%{~|7o3Ks^QEFk`iP*fVV#h@8Z@eCkX@+eaXjUm>t]/yZ');
define('SECURE_AUTH_SALT', '52.]*&M+JHzXSD-eOPM6u;9f/(M@KgvxoXHpSD| #i;)JBuozTuGi(H&X2}bd{U9');
define('LOGGED_IN_SALT',   'UT`>Td}>5H)5/&q:.NU^<B,ZWYB]l]];P)9OpWeq<4mXM>;ag:!Q0Kaf*o@y8ga&');
define('NONCE_SALT',       'mqmpm)#Lal|W-,m;_D_B-nPl>M[<j)(~61=.T}(r*/zRS8o}RiJk[>3341wi~6M<');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
