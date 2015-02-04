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

class CompositeMetadataProvider implements MetadataProviderInterface
{
    private $providers;

    public function __construct(array $providers)
    {
        $this->providers = array();
        foreach ($providers as $provider) {
            $this->addMetadataProvider($provider);
        }
    }

    public function getMetadataProviders()
    {
        return $this->providers;
    }

    public function addMetadataProvider(MetadataProviderInterface $provider)
    {
        if ($provider instanceof self) {
            foreach ($provider->getProviders() as $p) {
                $this->addMetadataProvider($p);
            }
        } else {
            $this->providers[] = $provider;
        }
    }

    public function getPackageMetadata(VcsDriverInterface $driver, $identifier)
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($driver, $identifier)) {
                return $provider->getPackageMetadata($driver, $identifier);
            }
        }
    }

    public function supports(VcsDriverInterface $driver, $identifier)
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($driver, $identifier)) {
                return true;
            }
        }
        
        return false;
    }
}
