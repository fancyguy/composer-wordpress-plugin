<?php
/**
 * This file is part of FancyGuy WordPress Composer Plugin.
 *
 * Copyright (c) 2015 Steve Buzonas <steve@fancyguy.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FancyGuy\Composer\WordPress;

use Composer\Package\PackageInterface;

class Config
{

    private $webroot;

    public function getWebroot()
    {
        return $this->webroot;
    }

    public function setWebroot($webroot)
    {
        $this->webroot = $webroot;
    }
    
    public static function createFromPackage(PackageInterface $package)
    {
        $config = new static;
        
        $extra = $package->getExtra();

        $config->setWebroot(static::extractConfigSetting('webroot', $extra, 'wordpress'));
        
        return $config;
    }

    protected static function extractConfigSetting($setting, array $extra, $default = null)
    {
        return array_key_exists('wordpress', $extra) && array_key_exists($setting, $extra['wordpress']) ? $extra['wordpress'][$setting] : $default;
    }
}
