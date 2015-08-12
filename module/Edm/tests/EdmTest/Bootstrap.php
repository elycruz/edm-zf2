<?php
namespace EdmTest;//Change this namespace for your test

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Db\Adapter\Adapter as DbAdapter;
use RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

ini_set('date.timezone', 'America/New_York');

class Bootstrap
{
    protected static $serviceManager;
    protected static $config;
    protected static $bootstrap;
    protected static $autoload_config;

    public static function init()
    {
        // Load the user-defined test configuration file, if it exists; otherwise, load
        if (is_readable(__DIR__ . '/TestConfig.php')) {
            $testConfig = include __DIR__ . '/../TestConfig.php';
        } else {
            $testConfig = include __DIR__ . '/../TestConfig.php.dist';
        }

        $zf2ModulePaths = array();

        if (isset($testConfig['module_listener_options']['module_paths'])) {
            $modulePaths = $testConfig['module_listener_options']['module_paths'];
            foreach ($modulePaths as $modulePath) {
                if (($path = static::findParentPath($modulePath)) ) {
                    $zf2ModulePaths[] = $path;
                }
            }
        }

        $zf2ModulePaths  = implode(PATH_SEPARATOR, $zf2ModulePaths) . PATH_SEPARATOR;
        $zf2ModulePaths .= getenv('ZF2_MODULES_TEST_PATHS') ?: (defined('ZF2_MODULES_TEST_PATHS') ? ZF2_MODULES_TEST_PATHS : '');

        static::initAutoloader();

        // use ModuleManager to load this module and it's dependencies
        $baseConfig = array(
            'module_listener_options' => array(
                'module_paths' => explode(PATH_SEPARATOR, $zf2ModulePaths),
            ),
        );

        // Global configs
        $global_autoload_options =  include __DIR__ . '/../../../../config/autoload/global.php';
        $local_autoload_options =   include __DIR__ . '/../../../../config/autoload/local.php';
        $autoload_config = static::$autoload_config = ArrayUtils::merge($global_autoload_options, $local_autoload_options);

        // Application configs
        $config = ArrayUtils::merge($baseConfig, $testConfig);
        $serviceManager = new ServiceManager(new ServiceManagerConfig($autoload_config['service_manager']));
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();

        static::$serviceManager = $serviceManager;
        static::$config = $config;
        static::initDbAdapter();

    }

    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    public static function getConfig()
    {
        return static::$config;
    }
    
    public static function initDbAdapter () {
//
//        $dbOptions = array_merge(
//            array_merge(static::$autoload_config['db']['driver_options'],
//            static::$autoload_config['db']['driver_options']);

        GlobalAdapterFeature::setStaticAdapter(new DbAdapter(array(
            'driver' => 'Mysqli',
            'dbname' => 'edm',
            'username' => 'root',
            'password' => '07-bienven',
            'host' => 'localhost',
            'options' => array(
                'buffer_results' => true
            )
        )));
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (is_readable($vendorPath . '/autoload.php')) {
            $loader = include $vendorPath . '/autoload.php';
        } else {
            $zf2Path = getenv('ZF2_PATH') ?: (defined('ZF2_PATH') ? ZF2_PATH : (is_dir($vendorPath . '/ZF2/library') ? $vendorPath . '/ZF2/library' : false));
            if (!$zf2Path) {
                throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
            }
            // Hasher path
            $crackStationPath = implode(DIRECTORY_SEPARATOR, array(
                __DIR__, 'vendor', 'CrackStation', 'src', 'CrackStation'
            ));

            // If zf2 path, initiate autoloader
            if (isset($loader)) {
                $loader->add('Zend', $zf2Path);
                $loader->add('CrackStation', $crackStationPath);
            }
            else {
                include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
                Zend\Loader\AutoloaderFactory::factory(array(
                    'Zend\Loader\StandardAutoloader' => array(
                        'autoregister_zf' => true,
                        'namespaces' => array(
                            'CrackStation' => $crackStationPath
                        ))
                ));
            }

//            include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';

        }

        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true,
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/' . __NAMESPACE__,
                ),
            ),
        ));        
    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) return false;
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }
    
}

Bootstrap::init();