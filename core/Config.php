<?php

/*** Database settings - You can get this info from your web host ***/

/* The name of the database for G-Frame */
define( 'DB_NAME', 'profit' );

/* Database username */
define( 'DB_USER', 'root' );

/* Database password */
define( 'DB_PASSWORD', '' );

/* Database hostname */
define( 'DB_HOST', 'localhost' );

/* Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/* Site URL. */
define( 'site_url', guess_url() );

/* Debug mode. */
define( 'DebugMode', true );



/* Time Zone */
//date_default_timezone_set('America/Havana');



/*** Mailer settings - You can get this info from your web host ***/

/* Mail smtp Host*/
define( 'M_Host', 'smtp.hostinger.com' );

/* Mail smtp port */
define( 'M_Port', 587 );

/* Mail username */
define( 'M_Username', 'juank@gorvet.com' );

/* Mail Password */
define( 'M_Password', 'Morales23!' );

/* Mail Secure Protocol */
define( 'M_Secure', 'tls' );

/* Mail Sender Address username */
define( 'M_From', 'juank@gorvet.com' );

/* Mail Sender Name */
define( 'M_Name', 'Juank de Gorvet' );

 