<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendIndexApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
error_reporting(E_ALL);

/**
 * This autoloading setup is really more complicated than it needs to be for most
 * applications. The added complexity is simply to reduce the time it takes for
 * new developers to be productive with a fresh Index. It allows autoloading
 * to be correctly configured, regardless of the installation method and keeps
 * the use of composer completely optional. This setup should work fine for
 * most users, however, feel free to configure autoloading however you'd like.
 */
// Composer autoloading
if (file_exists('vendor/autoload.php')) {
    $loader = include 'vendor/autoload.php';
}

// Resolve Zend Framework path by host name
$zf2Path = false;

// Support for ZF2_PATH environment variable or git submodule
if (getenv('ZF2_PATH')) {
    $zf2Path = getenv('ZF2_PATH');
    // Support for zf2_path directive value
} else if (get_cfg_var('zf2_path')) {
    $zf2Path = get_cfg_var('zf2_path');
} else if (is_dir('vendor/ZF2/library')) {
    $zf2Path = 'vendor/ZF2/library';
}

// Hasher path
$crackStationPath = implode(DIRECTORY_SEPARATOR, array(
    __DIR__, 'vendor', 'CrackStation', 'src', 'CrackStation'
        ));

// If zf2 path, initiate autoloader
if ($zf2Path) {
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
}

if (!class_exists('Zend\Loader\AutoloaderFactory')) {
    throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
}