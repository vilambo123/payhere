<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This is the main entry point for the application
 */

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 */
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 */
switch (ENVIRONMENT)
{
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
    break;

    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        if (version_compare(PHP_VERSION, '5.3', '>='))
        {
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        }
        else
        {
            error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
        }
    break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1);
}

/*
 *---------------------------------------------------------------
 * SYSTEM DIRECTORY NAME
 *---------------------------------------------------------------
 */
$system_path = 'system';

/*
 *---------------------------------------------------------------
 * APPLICATION DIRECTORY NAME
 *---------------------------------------------------------------
 */
$application_folder = 'application';

/*
 *---------------------------------------------------------------
 * VIEW DIRECTORY NAME
 *---------------------------------------------------------------
 */
$view_folder = '';

/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 */
define('BASEPATH', str_replace("\\", "/", __DIR__.'/'));

// Path to the system directory
if (realpath($system_path) !== FALSE)
{
    $system_path = realpath($system_path).DIRECTORY_SEPARATOR;
}

$system_path = rtrim($system_path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

// Is the system path correct?
if ( ! is_dir($system_path))
{
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your system folder path does not appear to be set correctly.';
    exit(3);
}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('FCPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('SYSDIR', basename($system_path));

if (is_dir($application_folder))
{
    if (($_temp = realpath($application_folder)) !== FALSE)
    {
        $application_folder = $_temp;
    }
    else
    {
        $application_folder = strtr(
            rtrim($application_folder, '/\\'),
            '/\\',
            DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
        );
    }
}
elseif (is_dir(BASEPATH.$application_folder.DIRECTORY_SEPARATOR))
{
    $application_folder = BASEPATH.strtr(
        trim($application_folder, '/\\'),
        '/\\',
        DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
    );
}
else
{
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your application folder path does not appear to be set correctly.';
    exit(3);
}

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);

if ( ! isset($view_folder[0]) && is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR))
{
    $view_folder = APPPATH.'views';
}
elseif (is_dir($view_folder))
{
    if (($_temp = realpath($view_folder)) !== FALSE)
    {
        $view_folder = $_temp;
    }
    else
    {
        $view_folder = strtr(
            rtrim($view_folder, '/\\'),
            '/\\',
            DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
        );
    }
}
elseif (is_dir(APPPATH.$view_folder.DIRECTORY_SEPARATOR))
{
    $view_folder = APPPATH.strtr(
        trim($view_folder, '/\\'),
        '/\\',
        DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
    );
}
else
{
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your view folder path does not appear to be set correctly.';
    exit(3);
}

define('VIEWPATH', $view_folder.DIRECTORY_SEPARATOR);

// Simple router for development server
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (file_exists(__DIR__ . $path) && $path !== '/') {
        return false;
    }
}

// Load the bootstrap file
require_once BASEPATH.'application/config/bootstrap.php';
