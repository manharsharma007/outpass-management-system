<?php
define('ROOT',dirname(dirname(__DIR__)));
define('APP',ROOT.'/framework/app');
define('LIB',ROOT.'/framework/library');
define('CORE',ROOT.'/framework/core');
define('UTILS',ROOT.'/framework/utils');

// dont change below this

		define('ERROR',4);

		define('WARNING',3);

		define('NOTICE',2);

		define('SUCCESS',1);

		define('SESSION_LIMIT', 300);

define('CONTROLLERS',ROOT.DS.'protected/controllers');
define('MODELS',ROOT.DS.'protected/models');
define('VIEWS',ROOT.DS.'protected/views');

defined('ADMIN_EMAIL') or die('Parameters missing in config file');
defined('SITE') or die('Parameters missing in config file');
defined('DB_NAME') or die('Parameters missing in config file');
defined('DB_PASS') or die('Parameters missing in config file');
defined('DB_HOST') or die('Parameters missing in config file');


define('SITE_URL',URL.SITE);
define('AUTH_KEY', 'O4D#$HthjJ#B[n#skG++5t:O8>D1?O#vA->s.ZpF;z;;BLR#`MmLtRgCGs{iG@</M$S,{');

?>