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
define('DB_NAME', 'perform_site');

/** MySQL database username */
define('DB_USER', 'perform');

/** MySQL database password */
define('DB_PASSWORD', 'Pur1fy#');

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
define('AUTH_KEY',         'mL&_LQy. od9k&`mBI7Akjb6$ mPiI`p}v&-gn;3V2kDxZ9STmnz]OF~z3?A5@K;');
define('SECURE_AUTH_KEY',  'yqIYB[mtXr2ja:1R=+=2CqfkPogx3gY$jX$Zqmv=NV*6R5Nv9gmt@5CCR!YziQw^');
define('LOGGED_IN_KEY',    'hmj=9d+ar:)<tw]E*fd<xML|${h3zLkCYYkiSqofOK[%.PM+sH]M48_ygrF7[wq-');
define('NONCE_KEY',        '=p10hkw8p0:B)nUvLz2fkFI9rt2,fRh7-?c.l}Z^P=$]pYj3Kjca_U_SSYFIUOZI');
define('AUTH_SALT',        '4Cl.A)|UMXY-Cr=y7.`|oho|cZ5jW~B#ix+?OF|8.s7X-<14 Ej1R$Kx:KM[E}|U');
define('SECURE_AUTH_SALT', '(R*@WDC)rwvu~29L[@(P|fV-GT-kh_`,*v -/Q)Ub+-lJJH]5!|N-foI >E5J-lV');
define('LOGGED_IN_SALT',   '$%`M4G4znUF|v=V?hrQ!~Ym{zBU0QV1$7|.| !u8kEk:j#+fXXpV*IuEiN{j@[bp');
define('NONCE_SALT',       '5zyCn277H;tkO3bkobaDCIHi:5-S^Ii69ZmDu[J?6qn@T[Nm+^Te9tV3XXffHK->');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_gr_';

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
