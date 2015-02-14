<?php
/**
 * This file is part of FancyGuy WordPress Composer Plugin.
 *
 * Copyright (c) 2015 Steve Buzonas <steve@fancyguy.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FancyGuy\Composer\WordPress\Installer;

use Composer\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\Filesystem;

class CoreInstaller extends LibraryInstaller
{
    const PACKAGE_TYPE = 'wordpress-core';
    const SALT_API_URL = 'https://api.wordpress.org/secret-key/1.1/salt/';

    private $wordpressPlugin;

    public function __construct(IOInterface $io, Composer $composer, PluginInterface $plugin, Filesystem $filesystem = null)
    {
        $this->wordpressPlugin = $plugin;
        parent::__construct($io, $composer, self::PACKAGE_TYPE, $filesystem);
    }

    /**
     * @{inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        return $this->wordpressPlugin->getConfig()->getWebroot();
    }

    /**
     * @{inheritDoc}
     */
    public function supports($packageType)
    {
        return $packageType === $this->type;
    }
    
    /**
     * {@inheritDoc}
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);
        $this->createSaltSeed();
        $this->createWpConfig();
    }

    /**
     * {@inheritDoc}
     */
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        parent::update($repo, $initial, $target);
        $this->createSaltSeed();
        $this->createWpConfig();
    }

    private function createWpConfig()
    {
        $configPath = realpath(dirname($this->wordpressPlugin->getConfig()->getWebroot())).'/wp-config.php';
        if (!file_exists($configPath)) {
            $db['name']     = $this->ask('The name of the database for WordPress', 'wordpress');
            $db['user']     = $this->ask('Database username', 'wordpress');
            $db['password'] = $this->ask('Database password', 'wordpress');
            $db['host']     = $this->ask('Database Hostname', 'localhost');
            $db['charset']  = $this->ask('Database Charset', 'utf8');
            $db['collate']  = $this->ask("Database Collation (Don't set this if in doubt)");

            foreach($db as $field => $value) {
                $replacements['{{db.'.$field.'}}'] = $value;
            }
            $root = realpath('.').'/';
            $paths = array(
                'webroot' => $root.$this->wordpressPlugin->getConfig()->getWebroot(),
                'salt_path' => $this->getSaltPath(),
                'content_dir' => $root.$this->wordpressPlugin->getConfig()->getContentPath(),
                'plugin_dir' => $root.$this->wordpressPlugin->getConfig()->getPluginPath(),
            );
            foreach ($paths as $name => $path) {
                $replacements['{{'.$name.'}}'] = $this->filesystem->findShortestPath(dirname($configPath), $path);
            }
            $replacements['{{table_prefix}}'] = $this->wordpressPlugin->getConfig()->getTablePrefix();
            
            $config = file_get_contents(__DIR__.'/../../res/wp-config.php.template');
            $config = str_replace(array_keys($replacements), array_values($replacements), $config);
            file_put_contents($configPath, $config);
        }
    }

    private function getSaltPath()
    {
        return ($this->vendorDir ? $this->vendorDir.'/' : '') . 'wordpress-salts.php';
    }

    private function createSaltSeed()
    {
        $saltPath = $this->getSaltPath();
        if (!file_exists($saltPath)) {
            $salts = '<?php' . PHP_EOL . file_get_contents(self::SALT_API_URL);
            file_put_contents($saltPath, $salts);
        }
    }
    
    private function ask($question, $default = null)
    {
        $question = '<info>'.$question.'</info>';
        $question .= $default ? ' [<comment>'.$default.'</comment>]: ' : ': ';

        return $this->io->ask($question, $default);
    }
}
