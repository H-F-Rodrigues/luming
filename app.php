<?php

define('SOURCES', 'src');
define('BASE_PATH', realpath(__DIR__));

// Initial config
require_once 'path.php';

// Constants of path
define('COMPONENTS', getComponentsPath());
define('CONTROLLERS', getControllersPath());
define('FUNCTIONS', getFunctionsPath());
define('PAGES', getPagesPath());
define('CONFIGS', getConfigsPath());
define('SERVICES', getServicesPath());

// Requires
require_once FUNCTIONS . 'functions.php';
require_once CONFIGS . 'routes.php';
require_once CONFIGS . 'database.php';
require_once SERVICES . 'route_resolver.php';
require_once SERVICES . 'router.php';
