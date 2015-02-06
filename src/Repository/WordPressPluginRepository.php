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
use FancyGuy\Composer\WordPress\Installer\PluginInstaller;

class WordPressPluginRepository extends WordPressRepository
{
    
    public function __construct(IOInterface $io, Config $config, EventDispatcher $eventDispatcher = null)
    {
        parent::__construct($io, $config, WordPressRepository::PLUGIN_VENDOR, PluginInstaller::PACKAGE_TYPE, $eventDispatcher);
    }

    protected function getBaseUrl()
    {
        return 'http://plugins.svn.wordpress.org';
    }

    protected function providesPackage($name)
    {
        if (0 !== strpos($name, $this->vendor)) {
            return false;
        }
        try {
            return (bool) $this->loadPackage($name);
        } catch (\Exception $e) {
        }
        return false;
    }

    protected function loadPackage($name)
    {
        if (!isset($this->infoCache[$name])) {
            $cacheFile = $name.'.json';
            $packageUrl = $this->getBaseUrl().'/'.$this->getPackageShortName($name);
            if ($res = $this->cache->read($cacheFile, $this->getModifiedTimestamp($packageUrl))) {
                $this->infoCache[$name] = json_decode($res, true);
            } else {
                $this->infoCache[$name] = $this->loadVersions($this->getDriver($packageUrl), $name);
                $this->cache->write($cacheFile, json_encode($this->infoCache[$name]));
            }
        }
        return $this->infoCache[$name];
    }

    /**
     * @TODO scan the files in the root of the directory to get the remaining metadata
     */
    protected function getComposerMetadata(VcsDriverInterface $driver, $data)
    {
        if ('dev-' !== substr($data['version'], 0, 4) && '-dev' !== substr($data['version'], -4)) {
            $data['dist'] = array(
                'type' => 'zip',
                'url'  => sprintf('https://downloads.wordpress.org/plugin/%s.%s.zip',
                                  $this->getPackageShortName($data['name']),
                                  $data['version']),
            );
        }
        
        //        $headers = $this->extractHeaderFields($url.'/style.css');
        //        $metadata = array_merge($metadata, $this->translateStandardHeaders($headers));

        return $data;
    }
}
