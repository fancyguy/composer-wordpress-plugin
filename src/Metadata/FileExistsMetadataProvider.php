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
use FancyGuy\Composer\WordPress\Repository\Vcs\FileProbingVcsDriver;

class FileExistsMetadataProvider extends MetadataProvider implements MetadataProviderInterface
{
    protected $filePath;
    
    public function __construct($path)
    {
        $this->filePath = $path;
    }

    public function supports(VcsDriverInterface $driver, $identifier)
    {
        if ($driver instanceof FileProbingVcsDriver) {
            return $driver->hasFile($this->filePath, $identifier);
        }

        return false;
    }
}
