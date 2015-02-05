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
use Composer\IO\IOInterface;
use Composer\Repository\ArrayRepository;
use FancyGuy\Composer\Metadata\MetadataAware;

class ThemeRepository extends WordPressRepository
{

    public function __construct(IOInterface $io, Config $config)
    {
        parent::__construct($io, $config, 'http://themes.svn.wordpress.org', WordPressRepository::THEME_VENDOR);
    }

}
