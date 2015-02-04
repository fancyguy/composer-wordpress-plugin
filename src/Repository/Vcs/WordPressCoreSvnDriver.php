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

use Composer\Cache;
use Composer\Config;
use Composer\IO\IOInterface;

class WordPressCoreSvnDriver extends WordPressSvnDriver
{

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
        $this->cacne = new Cache($this->io, $this->config->get('cache-repo-dir').'/wordpress/core');
    }

    public function getDist($identifier)
    {
        if (in_array($identifier, $this->getTags()) && $this->hasComposerFile($identifier)) {
            $id = '/' . trim($identifier, '/') . '/';
            list($path, $rev) = $this->parseIdentifier($id);
            $version = trim(substr(trim($path, '/'), strlen($this->tagsPath)), '/');
            $url = sprintf('http://wordpress.org/wordpress-%s.zip', $version);
            $dist = array('type' => 'zip', 'url' => $url, 'reference' => $identifier);
            if (!$sha = $this->cache->read($url.'.shasum')) {
                $sha = @file_get_contents($url.'.sha1') ?: '';
                $this->cache->write($url.'.shasum', $sha);
            }
            $dist['shasum'] = $sha;
            return $dist;
        }
        return null;
    }

    public static function supports(IOInterface $io, Config $config, $url, $deep = false)
    {
        $urlFragments = parse_url($url);
        if (!$urlFragments['host'] === 'core.svn.wordpress.org') {
            return false;
        }

        // make sure it's an svn repo just to be safe
        return parent::supports($io, $config, $url, $deep);
    }
    
}
