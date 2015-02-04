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
use FancyGuy\Composer\WordPress\Installer\CoreInstaller;
use FancyGuy\Composer\WordPress\Metadata\MetadataProvider;
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
        $this->metadataProvider = $this->getDefaultMetadataProvider();
        $repo = new CompositeRepository($this->getWordPressRepositories($composer, $io));
        $composer->getRepositoryManager()->addRepository($repo);

        $im = $composer->getInstallationManager();
        $im->addInstaller(new CoreInstaller($io, $composer, $this));
    }

    private function getWordPressRepositories(Composer $composer, IOInterface $io)
    {
        $rm = new RepositoryManager($io, $composer->getConfig(), $composer->getEventDispatcher());
        $this->setDefaultRepositoryClasses($rm);
        return $this->getRepositories($rm);
    }

    private function getDefaultMetadataProvider()
    {
        $coreProvider = new FileExistsMetadataProvider('wp-blog-header.php');
        $coreProvider->setMetadata(array(
            'name' => 'wordpress/wordpress',
            'description' => 'WordPress is web software you can use to create a beautiful website or blog.',
            'type' => CoreInstaller::PACKAGE_TYPE,
        ));

        $provider = $coreProvider;
        
        return $provider;
    }

    private function getRepositories(RepositoryManager $rm)
    {
        $repoUrls = array(
            'http://core.svn.wordpress.org',
        );
        
        $repositories = array();
        foreach ($repoUrls as $repo) {
            $r = $rm->createRepository('wordpress', array('url' => $repo));
            $r->setMetadataProvider($this->metadataProvider);
            $repositories[] = $r;
        }
        
        return $repositories;
    }

    private function setDefaultRepositoryClasses(RepositoryManager $rm)
    {
        $rm->setRepositoryClass('wordpress', 'FancyGuy\Composer\WordPress\Repository\VcsRepository');
        $rm->setRepositoryClass('svn', 'FancyGuy\Composer\WordPress\Repository\VcsRepository');
    }
}
