<?php
/**
 * This file is part of FancyGuy WordPress Composer Plugin.
 *
 * Copyright (c) 2015 Steve Buzonas <steve@fancyguy.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FancyGuy\Composer\WordPress;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Repository\CompositeRepository;
use Composer\Repository\RepositoryManager;
use FancyGuy\Composer\WordPress\Repository\WordPressCoreRepository;
use FancyGuy\Composer\WordPress\Repository\WordPressPluginRepository;
use FancyGuy\Composer\WordPress\Repository\WordPressThemeRepository;
use FancyGuy\Composer\WordPress\Installer\CoreInstaller;
use FancyGuy\Composer\WordPress\Installer\ThemeInstaller;
use FancyGuy\Composer\WordPress\Installer\PluginInstaller;
use FancyGuy\Composer\WordPress\Installer\MuPluginInstaller;

class WordPressPlugin implements PluginInterface
{
    private $config;
    private $metadataProvider;

    public function getConfig()
    {
        if (!$this->config) {
            $this->config = new Config();
        }
        return $this->config;
    }

    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    public function activate(Composer $composer, IOInterface $io)
    {
        if ($package = $composer->getPackage()) {
            $this->setConfig(Config::createFromPackage($package));
        }
        $repo = new CompositeRepository(array(
            new WordPressCoreRepository($io, $composer->getConfig()),
            new WordPressThemeRepository($io, $composer->getConfig()),
            new WordPressPluginRepository($io, $composer->getConfig()),
        ));
        $composer->getRepositoryManager()->addRepository($repo);

        $im = $composer->getInstallationManager();
        $im->addInstaller(new CoreInstaller($io, $composer, $this));
        $im->addInstaller(new ThemeInstaller($io, $composer, $this));
        $im->addInstaller(new PluginInstaller($io, $composer, $this));
        $im->addInstaller(new MuPluginInstaller($io, $composer, $this));
    }
}
