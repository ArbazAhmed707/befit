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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'befitapp_wp955' );

/** Database username */
define( 'DB_USER', 'befitapp_wp955' );

/** Database password */
define( 'DB_PASSWORD', '8US97p-g7[' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         '1z9fqqmvsyl880kxtgp0oytapaxqjwujvp1yol3izbtfeja2wr1x0llzrbwmlkye' );
define( 'SECURE_AUTH_KEY',  'kqg3tzkooxyl471mglmdkc8sjqf27jivb28lq8jsbthvlkb9qeamak5jh7bzeymg' );
define( 'LOGGED_IN_KEY',    'wtdkd0aplu1huhakqkzv7vxahypststwwgbgnvk9ljo7apivfnsbjdottuebdfmn' );
define( 'NONCE_KEY',        '3vvxw26xlgyzqbr4clt9krwoqwpczp7xtvddyht9cf9juissggixltsamyqozmki' );
define( 'AUTH_SALT',        '2ghfyh4rwxlx5pccyeb8kkxfhrxb5nogqqtwuubbsdpmdzm4rvloyw9tlgxjyevi' );
define( 'SECURE_AUTH_SALT', 'xwlsrrydxqip3zr74awerkr68tcrbeg9xvyg2rj8f5revnpagmd7x5gpqtt69ci9' );
define( 'LOGGED_IN_SALT',   'u4uaxlxa52wkxx2vgqc3fgzf9sb3v5kobgunpv14yeewc23v6qdkarfytffqqabb' );
define( 'NONCE_SALT',       'xj0i1nnp4suwfki3lfb7kziypxvzmkhvuqpqi5cgdjinha5q0oshguobkfgk0ttl' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpa2_';

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



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
define('DISABLE_WP_CRON', true);
