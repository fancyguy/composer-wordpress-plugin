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
    private $contentPath;
    private $themePath;
    private $pluginPath;
    private $muPluginPath;
    private $tablePrefix;

    public function getWebroot()
    {
        return $this->webroot;
    }
    
    public function setWebroot($webroot)
    {
        $this->webroot = $webroot;
    }

    public function getContentPath()
    {
        return $this->contentPath;
    }

    public function setContentPath($path)
    {
        $this->contentPath = $path;
    }

    public function getThemePath()
    {
        return $this->themePath;
    }

    public function setThemePath($path)
    {
        $this->themePath = $path;
    }

    public function getPluginPath()
    {
        return $this->pluginPath;
    }

    public function setPluginPath($path)
    {
        $this->pluginPath = $path;
    }

    public function getMuPluginPath()
    {
        return $this->muPluginPath;
    }

    public function setMuPluginPath($path)
    {
        $this->muPluginPath = $path;
    }
    
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    public function setTablePrefix($prefix)
    {
        $this->tablePrefix = $prefix;
    }
    
    public static function createFromPackage(PackageInterface $package)
    {
        $config = new static;
        
        $extra = $package->getExtra();

        $config->setWebroot(static::extractConfigSetting('webroot', $extra, 'wordpress'));
        $config->setContentPath(static::extractConfigSetting('content-path', $extra, 'wp-content'));
        $config->setThemePath(static::extractConfigSetting('themes-path', $extra, $config->getContentPath().'/themes'));
        $config->setPluginPath(static::extractConfigSetting('plugins-path', $extra, $config->getContentPath().'/plugins'));
        $config->setMuPluginPath(static::extractConfigSetting('mu-plugins-path', $extra, $config->getContentPath().'/mu-plugins'));
        $config->setTablePrefix(static::extractConfigSetting('table-prefix', $extra, 'wp_'));
        
        return $config;
    }

    protected static function extractConfigSetting($setting, array $extra, $default = null)
    {
        return array_key_exists('wordpress', $extra) && array_key_exists($setting, $extra['wordpress']) ? $extra['wordpress'][$setting] : $default;
    }
}
