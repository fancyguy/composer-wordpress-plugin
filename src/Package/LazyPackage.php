<?php
/**
 * This file is part of FancyGuy WordPress Composer Plugin.
 *
 * Copyright (c) 2015 Steve Buzonas <steve@fancyguy.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FancyGuy\Composer\WordPress\Package;

use Composer\Package\BasePackage;
use FancyGuy\Composer\WordPress\Repository\Driver\DriverInterface;

class LazyPackage extends BasePackage
{

    /**
     * @{inheritDoc}
     * @param Driver $driver the repo driver this package belongs to
     */
    public function __construct($name, DriverInterface $driver)
    {
        parent::__construct($name);
    }
    
    /**
     * @{inheritDoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @{inheritDoc}
     */
    public function setInstallationSource($type)
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getInstallationSource()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getSourceType()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getSourceUrl()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getSourceUrls()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getSourceReference()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getSourceMirrors()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getDistType()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getDistUrl()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getDistUrls()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getDistReference()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getDistSha1Checksum()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getDistMirrors()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getVersion()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getPrettyVersion()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getReleaseDate()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getStability()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function getNotificationUrl()
    {
        throw new \Exception('Method: '.__METHOD__.' is not implemented.');
    }

    /**
     * @{inheritDoc}
     */
    public function isDev()
    {
        return $this->getStability() === 'dev';
    }

    /**
     * @{inheritDoc}
     */
    public function getTargetDir()
    {
        return null;
    }

    /**
     * @{inheritDoc}
     */
    public function getExtra()
    {
        return array();
    }

    /**
     * @{inheritDoc}
     */
    public function getRequires()
    {
        return array();
    }

    /**
     * @{inheritDoc}
     */
    public function getConflicts()
    {
        return array();
    }

    /**
     * @{inheritDoc}
     */
    public function getProvides()
    {
        return array();
    }

    /**
     * @{inheritDoc}
     */
    public function getReplaces()
    {
        return array();
    }

    /**
     * @{inheritDoc}
     */
    public function getDevRequires()
    {
        return array();
    }

    /**
     * @{inheritDoc}
     */
    public function getSuggests()
    {
        return array();
    }

    /**
     * @{inheritDoc}
     */
    public function getAutoload()
    {
        return array();
    }

    /**
     * @{inheritDoc}
     */
    public function getDevAutoload()
    {
        return array();
    }

    /**
     * @{inheritDoc}
     */
    public function getIncludePaths()
    {
        return array();
    }

    /**
     * @{inheritDoc}
     */
    public function getBinaries()
    {
        return array();
    }

    /**
     * @{inheritDoc}
     */
    public function getArchiveExcludes()
    {
        return array();
    }
}
