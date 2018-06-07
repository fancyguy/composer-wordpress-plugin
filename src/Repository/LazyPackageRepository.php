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
use Composer\DependencyResolver\Pool;
use Composer\Event\EventDispatcher;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\ComposerRepository;

abstract class LazyPackageRepository extends ComposerRepository
{

    /**
     * @var Composer\Config
     */
    protected $config;
    
    /**
     * @var Composer\IO\IOInterface
     */
    protected $io;

    /**
     * @var Composer\Event\EventDispatcher
     */
    protected $dispatcher;

    public function __construct(IOInterface $io, Config $config, EventDispatcher $eventDispatcher = null)
    {
        $this->config = $config;
        $this->dispatcher = $eventDispatcher;
        $this->io     = $io;
    }

    public function hasProviders()
    {
        return true;
    }

    public function getProviderNames()
    {
        return array();
    }

    /**
     * @{inheritDoc}
     */
    public function findPackage($name, $version)
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }
    
    /**
     * @{inheritDoc}
     */
    public function search($query, $mode = 0)
    {
        return array();
    }
    
    /**
     * @{inheritDoc}
     */
    public function resetPackageIds()
    {
    }
    
    /**
     * @{inheritDoc}
     */
    public function whatProvides(Pool $pool, $name, $bypassFilters = false)
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }
    
    /**
     * @{inheritDoc}
     */
    protected function initialize()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    public function addPackage(PackageInterface $package)
    {
        ArrayRepository::addPackage($package);
    }
}
