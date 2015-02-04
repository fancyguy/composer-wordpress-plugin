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

abstract class WordPressSvnDriver extends SvnDriver
{
    public function getComposerInformation($identifier)
    {
        $identifier = '/' . trim($identifier, '/') . '/';

        if (!$metadata = parent::getComposerInformation($identifier)) {
            return;
        }

        if (!isset($metadata['support']['source'])) {
            list($path, $rev) = $this->parseIdentifier($identifier);
            $urlFragments = parse_url($this->baseUrl);

            $url = sprintf('http://%s/%s', $urlFragments['host'], trim($path, '/'));

            if ($rev) {
                $url .= '?p='.$rev;
            }
            $metadata['support']['source'] = $url;
            $this->cache->write($identifier.'.json', json_encode($metadata));
            $this->infoCache[$identifier] = $metadata;
        }

        return $this->infoCache[$identifier];
    }
}
