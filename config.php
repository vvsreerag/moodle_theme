<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mariadb';
$CFG->dblibrary = 'native';
$CFG->dbhost    = '127.0.0.1';
$CFG->dbname    = 'moodle';
$CFG->dbuser    = 'root';
$CFG->dbpass    = 'root_password';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => 3307,
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_unicode_ci',
);

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$path = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 9292) ? '' : '/moodle';
$CFG->wwwroot   = $protocol . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $path;
$CFG->dataroot  = '/home/sreerag/Documents/Projects/Other/moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;
$CFG->themedesignermode = true;
$CFG->cachejs = false;

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
