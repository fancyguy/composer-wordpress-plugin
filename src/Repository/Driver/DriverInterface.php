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

interface DriverInterface
{

    /**
     * Returns list of registered packages.
     *
     * @return array
     */
    public function getPackages();
    
    /**
     * @param array       $repoConfig The repository configuration
     * @param IOInterface $io
     * @param Config      $config
     * @return boolean
     */
    public static function supports($repoConfig, IOInterface $io, Config $config);
}
