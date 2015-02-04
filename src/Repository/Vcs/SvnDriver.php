<?php
/**
 * This file is part of FancyGuy WordPress Composer Plugin.
 *
 * Copyright (c) 2015 Steve Buzonas <steve@fancyguy.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FancyGuy\Composer\WordPress\Repository\Vcs;

use Composer\Downloader\TransportException;
use Composer\Json\JsonFile;
use Composer\Repository\Vcs\SvnDriver as Driver;
use FancyGuy\Composer\WordPress\Metadata\MetadataAware;
use FancyGuy\Composer\WordPress\Metadata\MetadataProviderInterface;

class SvnDriver extends Driver implements MetadataAware, FileProbingVcsDriver
{
    private $metadataProvider;

    public function getMetadataProvider()
    {
        return $this->metadataProvider;
    }

    public function setMetadataProvider(MetadataProviderInterface $metadataProvider)
    {
        $this->metadataProvider = $metadataProvider;
    }

    public function hasFile($file, $identifier)
    {
        list($path, $rev) = $this->parseIdentifier($identifier);
        $resource = $path.$file;
        try {
            $output = $this->execute('svn ls', $this->baseUrl . $resource . $rev);
            if (trim($output)) {
                return true;
            }
        } catch (\RuntimeException $e) {
        }

        return false;
    }

    public function getFile($file, $identifier)
    {
        list($path, $rev) = $this->parseIdentifier($identifier);
        $resource = $path.$file;
        try {
            $output = $this->execute('svn cat', $this->baseUrl . $resource . $rev);
            if (!trim($output)) {
                return;
            }
        } catch (\RuntimeException $e) {
            throw new TransportException($e->getMessage());
        }

        return $output;
    }

    public function getComposerInformation($identifier)
    {
        $identifier = '/' . trim($identifier, '/') . '/';

        if ($res = $this->cache->read($identifier.'.json')) {
            $this->infoCache[$identifier] = JsonFile::parseJson($res);
        }

        if (!isset($this->infoCache[$identifier])) {
            if (!$this->metadataProvider->supports($this, $identifier)) {
                return;
            }
	    $metadata = $this->metadataProvider->getPackageMetadata($this, $identifier);

            if (empty($metadata['time'])) {
                list($path, $rev) = $this->parseIdentifier($identifier);
                $output = $this->execute('svn info', $this->baseUrl . $path . $rev);
                foreach ($this->process->splitLines($output) as $line) {
                    if ($line && preg_match('{^Last Changed Date: ([^(]+)}', $line, $match)) {
                        $date = new \DateTime($match[1], new \DateTimeZone('UTC'));
                        $metadata['time'] = $date->format('Y-m-d H:i:s');
                        break;
                    }
                }
            }

            $this->cache->write($identifier.'.json', json_encode($metadata));
            $this->infoCache[$identifier] = $metadata;
        }

        return $this->infoCache[$identifier];
    }

    protected function parseIdentifier($identifier)
    {
        preg_match('{^(.+?)(@\d+)?/$}', $identifier, $match);
        if (!empty($match[2])) {
            $path = $match[1];
            $rev = $match[2];
        } else {
            $path = $identifier;
            $rev = '';
        }
        return array($path, $rev);
    }
}
