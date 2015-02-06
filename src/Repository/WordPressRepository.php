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

use Composer\Cache;
use Composer\Config;
use Composer\DependencyResolver\Pool;
use Composer\Event\EventDispatcher;
use Composer\IO\IOInterface;
use Composer\Package\Loader\ArrayLoader;
use Composer\Repository\RepositoryInterface;
use Composer\Repository\Vcs\SvnDriver;
use Composer\Util\Filesystem;
use Composer\Util\ProcessExecutor;
use Composer\Util\Svn as SvnUtil;

abstract class WordPressRepository extends LazyPackageRepository
{

    const THEME_VENDOR = 'wp-theme';

    protected $driver;
    protected $infoCache;
    protected $packageCache;
    protected $util;
    protected $vendor;
    
    public function __construct(IOInterface $io, Config $config, $vendor, EventDispatcher $eventDispatcher = null)
    {
        parent::__construct($io, $config, $eventDispatcher);
        $this->cache = new Cache($this->io, $this->config->get('cache-repo-dir').'/wordpress');
        $this->vendor = $vendor;
        $this->process = new ProcessExecutor($io);
        $this->loader = new ArrayLoader();
    }

    /**
     * @{inheritDoc}
     */
    public function whatProvides(Pool $pool, $name)
    {
        if (isset($this->packageCache[$name])) {
            return $this->packageCache[$name];
        }
        
        if (!$this->providesPackage($name)) {
            return array();
        }

        $this->packageCache[$name] = array();
        
        foreach ($this->loadPackage($name) as $version) {
            $package = $this->loader->load($version);
            $package->setRepository($this);
            $this->packageCache[$name][] = $package;
        }

        return $this->packageCache[$name];
    }

    /**
     * @TODO implement full text search and allow it to be configured
     */
    public function search($query, $mode = 0)
    {
        return array();
    }

    /**
     * @param string $name
     * @return boolean
     */
    abstract protected function providesPackage($name);

    /**
     * @return string
     */
    abstract protected function getBaseUrl();

    protected function getPackageShortName($name)
    {
        return str_replace($this->vendor.'/', '', $name);
    }

    protected function getDriver()
    {
        if (null === $this->driver) {
            $this->driver = new SvnDriver(array(
                'type' => 'svn',
                'url' => $this->getBaseUrl(),
            ), $this->io, $this->config, $this->process);
        }
        return $this->driver;
    }

    /**
     * An absolute path (leading '/') is converted to a file:// url.
     *
     * @param string $url
     *
     * @return string
     */
    protected static function normalizeUrl($url)
    {
        $fs = new Filesystem();
        if ($fs->isAbsolutePath($url)) {
            return 'file://' . strtr($url, '\\', '/');
        }

        return $url;
    }

    /**
     * Execute an SVN command and try to fix up the process with credentials
     * if necessary.
     *
     * @param  string            $command The svn command to run.
     * @param  string            $url     The SVN URL.
     * @throws \RuntimeException
     * @return array
     */
    protected function executeLines($command, $url)
    {
        return $this->process->splitLines($this->execute($command, $url));
    }
    
    /**
     * Execute an SVN command and try to fix up the process with credentials
     * if necessary.
     *
     * @param  string            $command The svn command to run.
     * @param  string            $url     The SVN URL.
     * @throws \RuntimeException
     * @return string
     */
    protected function execute($command, $url)
    {
        if (null === $this->util) {
            $this->util = new SvnUtil($this->baseUrl, $this->io, $this->config, $this->process);
        }
        
        try {
            return $this->util->execute($command, $url);
        } catch (\RuntimeException $e) {
            if (0 !== $this->process->execute('svn --version', $ignoredOutput)) {
                throw new \RuntimeException('Failed to load '.$this->url.', svn was not found, check that it is installed and in your PATH env.' . "\n\n" . $this->process->getErrorOutput());
            }

            throw new \RuntimeException(
                'Repository '.$this->url.' could not be processed, '.$e->getMessage()
            );
        }
    }
}
