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

interface MetadataAware
{

    /**
     * @return MetdataProvider
     */
    public function getMetadataProvider();

    /**
     * @var MetadataProvider
     */
    public function setMetadataProvider(MetadataProviderInterface $provider);
}
