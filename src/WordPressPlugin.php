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
use FancyGuy\Composer\WordPress\Repository\WordPressPluginRepository;
use FancyGuy\Composer\WordPress\Repository\WordPressThemeRepository;
use FancyGuy\Composer\WordPress\Installer\CoreInstaller;
use FancyGuy\Composer\WordPress\Installer\ThemeInstaller;
use FancyGuy\Composer\WordPress\Metadata\CompositeMetadataProvider;
use FancyGuy\Composer\WordPress\Metadata\FileExistsMetadataProvider;

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
        // $repo = new CompositeRepository($this->getWordPressRepositories($composer, $io));
        // $composer->getRepositoryManager()->addRepository($repo);
        $repo = new CompositeRepository(array(
            new WordPressThemeRepository($io, $composer->getConfig()),
            new WordPressPluginRepository($io, $composer->getConfig()),
        ));
        $composer->getRepositoryManager()->addRepository($repo);

        $im = $composer->getInstallationManager();
        $im->addInstaller(new CoreInstaller($io, $composer, $this));
        $im->addInstaller(new ThemeInstaller($io, $composer, $this));
    }

    private function getWordPressRepositories(Composer $composer, IOInterface $io)
    {
        $rm = new RepositoryManager($io, $composer->getConfig(), $composer->getEventDispatcher());
        $this->setDefaultRepositoryClasses($rm);
	return $this->getRepositories($rm, $io);
    }

    private function getDefaultMetadataProvider()
    {
	$coreProvider = new FileExistsMetadataProvider(array(
            'name' => 'wordpress/wordpress',
            'description' => 'WordPress is web software you can use to create a beautiful website or blog.',
            'type' => CoreInstaller::PACKAGE_TYPE,
	), 'wp-blog-header.php');

	$provider = new CompositeMetadataProvider(array($coreProvider));
        
        return $provider;
    }

    private function getRepositories(RepositoryManager $rm, IOInterface $io)
    {
        $repos = array(
            array('type' => 'wordpress', 'config' => array('url' => 'http://core.svn.wordpress.org')),
            //            array('type' => 'wp-theme', 'config' => array('url' => 'http://themes.svn.wordpress.org')),

        );
        
        $repositories = array();
        foreach ($repos as $repo) {
            $r = $rm->createRepository($repo['type'], $repo['config']);
            $r->setMetadataProvider($this->metadataProvider);
            $repositories[] = $r;
        }
        
        return $repositories;
    }

    private function setDefaultRepositoryClasses(RepositoryManager $rm)
    {
        $rm->setRepositoryClass('wordpress', 'FancyGuy\Composer\WordPress\Repository\VcsRepository');
        //        $rm->setRepositoryClass('wp-theme', 'FancyGuy\Composer\WordPress\Repository\ThemeRepository');
        $rm->setRepositoryClass('svn', 'FancyGuy\Composer\WordPress\Repository\VcsRepository');
    }
}
