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
use Composer\Package\Version\VersionParser;
use Composer\Repository\Vcs\VcsDriverInterface;
use FancyGuy\Composer\WordPress\Installer\ThemeInstaller;

class WordPressPluginRepository extends WordPressRepository
{
    const VERSION_LIMIT = '9999999-dev';
    
    public function __construct(IOInterface $io, Config $config, EventDispatcher $eventDispatcher = null)
    {
        parent::__construct($io, $config, WordPressRepository::PLUGIN_VENDOR, $eventDispatcher);
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
            $cacheFile = preg_replace('{[^a-z0-9./]}i', '-', $name.'.json');
            if ($res = $this->cache->read($cacheFile)) {
                $this->infoCache[$name] = json_decode($res, true);
            } else {
                $packages = array();
                
                $driver = $this->getDriver($this->getBaseUrl().'/'.$this->getPackageShortName($name));
                $this->versionParser = new VersionParser();
                
                foreach ($driver->getTags() as $tag => $identifier) {
                    $this->overwrite('Loading package metadata of <info>' . $name . '</info> (<comment>' . $tag . '</comment>)');
                    
                    if (!$parsedTag = $this->validateTag($tag)) {
                        if ($this->io->isVerbose()) {
                            $this->io->write('<warning>Skipped branch '.$tag.', invalid name</warning>');
                        }
                        continue;
                    }

                    $data['name'] = $name;
                    
                    $data['version'] = $tag;
                    $data['version_normalized'] = $parsedTag;
                    $data['version'] = preg_replace('{[.-]?dev$}i', '', $data['version']);
                    $data['version_normalized'] = preg_replace('{(^dev-|[.-]?dev$)}i', '', $data['version_normalized']);

                    if ($data['version_normalized'] !== $parsedTag) {
                        if ($this->io->isVerbose()) {
                            $this->io->write('<warning>Skipped tag '.$tag.', tag ('.$parsedTag.') does not match version ('.$data['version_normalized'].')</warning>');
                        }
                        continue;
                    }

                    if ($this->io->isVerbose()) {
                        $this->io->write('Importing tag '.$tag.' ('.$data['version_normalized'].')');
                    }

                    $packages[] = $this->getComposerMetadata($driver, $data, $identifier);
                }
                foreach ($driver->getBranches() as $branch => $identifier) {
                    $this->overwrite('Loading package metadata of <info>' . $name . '</info> (<comment>'. $branch . '</comment>)');

                    if (!$parsedBranch = $this->validateBranch($branch)) {
                        if ($this->io->isVerbose()) {
                            $this->io->write('<warning>Skipped branch '.$branch.', invalid name</warning>');
                        }
                        continue;
                    }

                    $data['name'] = $name;
                    $data['version'] = $branch;
                    $data['version_normalized'] = $parsedBranch;

                    if ('dev-' === substr($parsedBranch, 0, 4) || self::VERSION_LIMIT === $parsedBranch) {
                        $data['version'] = 'dev-' . $data['version'];
                    } else {
                        $data['version'] = preg_replace('{(\.9{7})+}', '.x', $parsedBranch);
                    }

                    if ($this->io->isVerbose()) {
                        $this->io->write('Importing branch ' . $branch . ' (' . $data['version'] . ')');
                    }

                    $packages[] = $this->getComposerMetadata($driver, $data, $identifier);
                }
                $this->infoCache[$name] = $packages;
                $this->cache->write($cacheFile, json_encode($this->infoCache[$name]));
            }
        }
        return $this->infoCache[$name];
    }

    /**
     * @TODO scan the files in the root of the directory to get the remaining metadata
     */
    protected function getComposerMetadata(VcsDriverInterface $driver, $data, $identifier)
    {
        $data['type'] = 'wp-plugin';
        $data['source'] = $driver->getSource($identifier);

        if ('dev-' !== substr($data['version'], 0, 4) && '-dev' !== substr($data['version'], -4)) {
            $data['dist'] = array(
                'type' => 'zip',
                'url'  => sprintf('https://downloads.wordpress.org/theme/%s.%s.zip',
                                  $this->getPackageShortName($data['name']),
                                  $data['version']),
            );
        }

        foreach ($this->executeLines('svn info', $driver->getUrl().'/'.$identifier) as $line) {
            if ($line && preg_match('{^Last Changed Date: ([^(]+)}', $line, $match)) {
                $date = new \DateTime($match[1], new \DateTimeZone('UTC'));
                $data['time'] = $date->format('Y-m-d H:i:s');
                break;
            }
        }

        //        $headers = $this->extractHeaderFields($url.'/style.css');
        //        $metadata = array_merge($metadata, $this->translateStandardHeaders($headers));

        return $data;
    }

    private function overwrite($message)
    {
        if ($this->io->isVerbose()) {
            $this->io->write($message);
        } else {
            $this->io->overwrite($message);
        }
    }

    private function validateBranch($branch)
    {
        try {
            return $this->versionParser->normalizeBranch($branch);
        } catch (\Exception $e) {
        }
        
        return false;
    }

    private function validateTag($version)
    {
        try {
            return $this->versionParser->normalize($version);
        } catch (\Exception $e) {
        }

        return false;
    }
}
