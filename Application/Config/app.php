<?php 
/*
 *---------------------------------------------------------------
 * App Config
 *---------------------------------------------------------------
 *
 * This Is The Main Configuration Of Project
 *
 * Do Not Edit Or Remove Any Key, You Can Edit Only The Values
 *
 */

# Website Base Url ex: domain.com
$config['base_url'] = '';

# Developement Mode || Production Mode
$config['devmode'] = true; 

# Show Errors Reporting
$config['errors'] = true;

# Page Not Found Controller
$config['notFound'] = 'notFoundController';



/*
 *---------------------------------------------------------------
 * Security
 *---------------------------------------------------------------
 *
 * Warning: Do Not Edit This Configs If You Are Not Sure
 * What Are You Doing
 * 
 */

# Start Session By Default 
$config['sess-start'] = false;

# Prevent Session Hijacking !!
$config['prev-sess-hi'] = true;

# Make Custome Session Name
$config['sess-name'] = 'mse-sess';

# Make Custom Session Dir-Name
$config['sess-dir'] = 'session';

# Turn on cookie_httponly
$config['http-only'] = true;

