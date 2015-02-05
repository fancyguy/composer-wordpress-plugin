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
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\ComposerRepository;
use Composer\Repository\RepositoryInterface;
use FancyGuy\Composer\WordPress\DriverInterface;

abstract class LazyVcsRepository extends ComposerRepository implements RepositoryInterface
{

    protected $config;
    protected $drivers;
    protected $io;
    protected $url;
    
    public function __construct(IOInterface $io, Config $config, $url, $drivers = null)
    {
        $this->config = $config;
        $this->io = $io;
        $this->url = $url;
        $this->drivers = $drivers ?: array(
            'svn' => 'FancyGuy\Composer\WordPress\Repository\Driver\SvnDriver',
        );
    }

    public function getRepoConfig()
    {
        return array(
            'type' => 'vcs',
            'url'  => $this->url,
        );
    }
    
    /**
     * @{inheritDoc}
     */
    public function hasPackage(PackageInterface $package)
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
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
    public function findPackages($name, $version = null)
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function search($query, $mode = 0)
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * Returns the number of packages in this repository
     *
     * @return int Number of packages
     */
    public function count()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    public function getPackages()
    {
        throw new \LogicException('Lazy loaded VCS repositories do not have the complete list of packages.');
    }

    protected function getDriver()
    {
        foreach ($this->drivers as $driver) {
            if ($driver::supports($this->getRepoConfig(), $this->io, $this->config)) {
                $driver = new $driver($this->getRepoConfig(), $this->io, $this->config);
                return $driver;
            }
        }
    }
}
