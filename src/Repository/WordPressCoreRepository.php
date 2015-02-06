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
use Composer\Event\EventDispatcher;
use Composer\IO\IOInterface;
use Composer\Repository\Vcs\VcsDriverInterface;
use FancyGuy\Composer\WordPress\Installer\CoreInstaller;

class WordPressCoreRepository extends WordPressRepository
{
    
    public function __construct(IOInterface $io, Config $config, EventDispatcher $eventDispatcher = null)
    {
        parent::__construct($io, $config, 'wordpress', CoreInstaller::PACKAGE_TYPE, $eventDispatcher);
    }

    protected function getBaseUrl()
    {
        return 'http://core.svn.wordpress.org';
    }

    protected function providesPackage($name)
    {
        return 'wordpress/wordpress' === $name;
    }

    protected function loadPackage($name)
    {
        if (!isset($this->infoCache[$name])) {
            $cacheFile = 'wordpress.json';
            if ($res = $this->cache->read($cacheFile, $this->getModifiedTimestamp($this->getBaseUrl()))) {
                $this->infoCache[$name] = json_decode($res, true);
            } else {
                $this->infoCache[$name] = $this->loadVersions($this->getDriver($this->getBaseUrl()), $name);
                $this->cache->write($cacheFile, json_encode($this->infoCache[$name]));
            }
        }
        return $this->infoCache[$name];
    }

    protected function getComposerMetadata(VcsDriverInterface $driver, $data)
    {
        $data['description'] = 'WordPress is web software you can use to create a beautiful website or blog.';
        if ('dev-' !== substr($data['version'], 0, 4) && '-dev' !== substr($data['version'], -4)) {
            $data['dist'] = array(
                'type' => 'zip',
                'url'  => sprintf('https://downloads.wordpress.org/wordpress-%s.zip', $data['version']),
            );
        }

        return $data;
    }
}
