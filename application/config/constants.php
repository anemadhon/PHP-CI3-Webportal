<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/* DEFAULT PWD */
defined('PWD') OR define('PWD', 'P@ssw0rd123');

/* reCaptcha */
defined('KEY_RECAPTCHA') OR define('KEY_RECAPTCHA', '6Lc0QJYaAAAAABMLkOKQMOyz49z9tSTF_Xn6EHGe');
defined('SITEKEY_RECAPTCHA') OR define('SITEKEY_RECAPTCHA', '6Lc0QJYaAAAAALDGbwh6gKKXEV8ZRZEQ39XHkNJ2');
defined('URL_RECAPTCHA') OR define('URL_RECAPTCHA', 'https://www.google.com/recaptcha/api/siteverify');

/* API CREATE POLIS */
defined('USER_CREATE_POLIS') OR define('USER_CREATE_POLIS', 'web-core');
defined('PWD_CREATE_POLIS') OR define('PWD_CREATE_POLIS', 'sigma123');
defined('URL_CREATE_POLIS_TOKEN') OR define('URL_CREATE_POLIS_TOKEN', 'https://acs.askrindo.co.id/telkomsigma-security/oauth/token');
defined('AUTH_CREATE_POLIS_TOKEN') OR define('AUTH_CREATE_POLIS_TOKEN', 'Authorization: Basic d2ViLWNvcmU6c2lnbWExMjM=');
defined('PARAMS_CREATE_POLIS_TOKEN') OR define('PARAMS_CREATE_POLIS_TOKEN', 'grant_type=password&username=teamsupport4&password=Ask#2020');

defined('URL_CREATE_POLIS') OR define('URL_CREATE_POLIS', 'https://acs.askrindo.co.id/askrindo-h2h/api/askred/transaction/proceed-askred-akseptasi/v.1'
);

/* API INQUIRY POLIS */
defined('URL_INQUIRY_POLIS_TOKEN') OR define('URL_INQUIRY_POLIS_TOKEN', 'http://10.20.10.20:8000/api/token/generate');

defined('URL_INQUIRY_POLIS') OR define('URL_INQUIRY_POLIS', 'http://10.20.10.20:8000/api/polis/inquiry');
defined('SIGN_INQUIRY_POLIS') OR define('SIGN_INQUIRY_POLIS', 'askSignature: 2af1502cdb30437e23a677b75829db31ed66c4df7b94468ecba68b4c8ec069ef');
