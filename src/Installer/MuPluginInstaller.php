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

class MuPluginInstaller extends LibraryInstaller
{
    const PACKAGE_TYPE = 'wordpress-mu-plugin';

    private $wordpressPlugin;
    private $pluginPath;

    public function __construct(IOInterface $io, Composer $composer, PluginInterface $plugin, Filesystem $filesystem = null)
    {
        $this->wordpressPlugin = $plugin;
        $this->pluginPath = $this->wordpressPlugin->getConfig()->getMuPluginPath();
        parent::__construct($io, $composer, self::PACKAGE_TYPE, $filesystem);
    }

    /**
     * {@inheritDoc}
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $this->validatePlugin($package);
        parent::install($repo, $package);
        $this->installPluginFile($package);
    }

    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        $this->validatePlugin($target);
        parent::update($repo, $initial, $target);
        $this->installPluginFile($target);
    }

    /**
     * @{inheritDoc}
     */
    public function supports($packageType)
    {
        return $packageType === $this->type;
    }

    protected function validatePlugin(PackageInterface $package)
    {
        $extra = $package->getExtra();
        if (empty($extra['file'])) {
            throw new \UnexpectedValueException('Error while installing '.$package->getPrettyName().', wordpress-mu-plugin packages should have the plugin file defined in their extra key to be usable.');
        }

        return true;
    }

    protected function installPluginFile(PackageInterface $package)
    {
        $this->initializePluginDir();
        $extra = $package->getExtra();
        $file = $extra['file'];
        if (!$initial = realpath($this->getInstallPath($package).'/'.$file)) {
            throw new \UnexpectedValueException('Error while installing '.$package->getPrettyName().', the specified plugin file '.$file.' does not exist.');
        }
        $target = $this->pluginPath.'/'.$file;
        copy($initial, $target);
    }

    protected function initializePluginDir()
    {
        $this->filesystem->ensureDirectoryExists($this->pluginPath);
        $this->pluginPath = realpath($this->pluginPath);
    }
}
