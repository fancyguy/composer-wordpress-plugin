<?php
/**
 * This file is part of FancyGuy WordPress Composer Plugin.
 *
 * Copyright (c) 2015 Steve Buzonas <steve@fancyguy.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FancyGuy\Composer\WordPress\Repository;

use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Composer\IO\IOInterface;
use Composer\Repository\VcsRepository as BaseRepository;
use FancyGuy\Composer\WordPress\Metadata\MetadataAware;
use FancyGuy\Composer\WordPress\Metadata\MetadataProviderInterface;

class VcsRepository extends BaseRepository implements MetadataAware
{
    private $metadataProvider;

    public function __construct(array $repoConfig, IOInterface $io, Config $config, EventDispatcher $dispatcher = null, array $drivers = null)
    {
        $drivers = $drivers ?: array(
            'wordpress-core' => 'FancyGuy\Composer\WordPress\Repository\Vcs\WordPressCoreSvnDriver',
            //            'wordpress-ext'  => 'FancyGuy\Composer\WordPress\Repository\Vcs\WordPressExtensionSvnDriver',
            'svn'            => 'FancyGuy\Composer\WordPress\Repository\Vcs\SvnDriver',
        );

        parent::__construct($repoConfig, $io, $config, $dispatcher, $drivers);
    }

    public function getDriver()
    {
        $driver = parent::getDriver();

        if ($driver instanceof MetadataAware) {
            $driver->setMetadataProvider($this->getMetadataProvider());
        }

        return $driver;
    }

    public function getMetadataProvider()
    {
        return $this->metadataProvider;
    }

    public function setMetadataProvider(MetadataProviderInterface $metadataProvider)
    {
        $this->metadataProvider = $metadataProvider;
    }
}
