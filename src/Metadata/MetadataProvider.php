<?php
/**
 * This file is part of FancyGuy WordPress Composer Plugin.
 *
 * Copyright (c) 2015 Steve Buzonas <steve@fancyguy.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FancyGuy\Composer\WordPress\Metadata;

use Composer\Repository\Vcs\VcsDriverInterface;

class MetadataProvider implements MetadataProviderInterface
{
    protected $metadata;
    
    public function getMetadata()
    {
        return $this->metadata;
    }

    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }

    public function getPackageMetadata(VcsDriverInterface $driver, $identifier)
    {
        return $this->getMetadata();
    }
    
    public function supports(VcsDriverInterface $driver, $identifier)
    {
        return true;
    }
}
