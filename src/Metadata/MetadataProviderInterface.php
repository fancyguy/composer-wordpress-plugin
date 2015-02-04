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

interface MetadataProviderInterface
{

    /**
     * @return array the metadata configuration
     */
    public function getMetadata();

    /**
     * @param array $metadata the medatada configuration
     */
    public function setMetadata($metadata);

    /**
     * @param Composer\Repository\Vcs\VcsDriverInterface $driver
     * @param string $identifier
     * @return array the metadata for the package
     */
    public function getPackageMetadata(VcsDriverInterface $driver, $identifier);

    /**
     * @param Composer\Repository\Vcs\VcsDriverInterface $driver
     * @param string $identifier
     * @return bool
     */
    public function supports(VcsDriverInterface $driver, $identifier);
}
