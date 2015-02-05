<?php
/**
 * This file is part of FancyGuy WordPress Composer Plugin.
 *
 * Copyright (c) 2015 Steve Buzonas <steve@fancyguy.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FancyGuy\Composer\WordPress\Repository\Driver;

use Composer\Config;
use Composer\IO\IOInterface;
use Composer\Util\Filesystem;
use Composer\Util\ProcessExecutor;

abstract class VcsDriver implements DriverInterface
{

    protected $config;
    protected $io;
    protected $process;
    protected $repoConfig;

    public function __construct($repoConfig, IOInterface $io, Config $config)
    {
        $this->config = $config;
        $this->io = $io;
        $this->repoConfig = $repoConfig;
        $this->process = new ProcessExecutor($io);
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
}
