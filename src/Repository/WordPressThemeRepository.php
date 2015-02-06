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
use FancyGuy\Composer\WordPress\Installer\ThemeInstaller;

class WordPressThemeRepository extends WordPressRepository
{
    public function __construct(IOInterface $io, Config $config, EventDispatcher $eventDispatcher = null)
    {
        parent::__construct($io, $config, WordPressRepository::THEME_VENDOR, $eventDispatcher);
    }

    protected function getBaseUrl()
    {
        return 'http://themes.svn.wordpress.org';
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
            $packageUrl = sprintf('%s/%s',
                                  $this->getBaseUrl(),
                                  $this->getPackageShortName($name)
            );
            $cacheFile = $name.'.json';
            if ($res = $this->cache->read($cacheFile, $this->getModifiedTimestamp($packageUrl))) {
                $this->infoCache[$name] = json_decode($res, true);
            } else {
                $versions = array();
                foreach ($this->executeLines('svn ls', $packageUrl) as $version) {
                    if (preg_match('{(.*)/}', $version, $match)) {
                        $versions[] = $match[1];
                    }
                }
                $packages = array();
                foreach ($versions as $version) {
                    $packages[] = $this->getComposerMetadata($name, $version);
                }
                $this->infoCache[$name] = $packages;
                $this->cache->write($cacheFile, json_encode($this->infoCache[$name]));
            }
        }
        return $this->infoCache[$name];
    }

    /**
     * @TODO scan the first few lines of the styles.css to get the remaining metadata
     */
    protected function getComposerMetadata($name, $version)
    {
        $url = sprintf('%s/%s/%s',
                       $this->getBaseUrl(),
                       $this->getPackageShortName($name),
                       $version
        );
        
        $source = array(
            'type'          => 'svn',
            'url'           => $url,
            'reference'     => '/',
            'trunk-path'    => '',
            'branches-path' => false,
            'tags-path'     => false,
        );
        
        $dist = array(
            'type' => 'zip',
            'url'  => sprintf('https://downloads.wordpress.org/theme/%s.%s.zip',
                              $this->getPackageShortName($name),
                              $version),
        );

        foreach ($this->executeLines('svn info', $url) as $line) {
            if ($line && preg_match('{^Last Changed Date: ([^(]+)}', $line, $match)) {
                $date = new \DateTime($match[1], new \DateTimeZone('UTC'));
                $time = $date->format('Y-m-d H:i:s');
                break;
            }
        }

        $metadata = array(
            'name'        => $name,
            'version'     => $version,
            'type'        => ThemeInstaller::PACKAGE_TYPE,
            'source'      => $source,
            'dist'        => $dist,
            'time'        => $time,
        );
        
        $headers = $this->extractHeaderFields($url.'/style.css');
        $metadata = array_merge($metadata, $this->translateStandardHeaders($headers));

        return $metadata;
    }

}
