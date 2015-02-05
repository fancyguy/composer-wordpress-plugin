<?php
/**
 * This file is part of FancyGuy WordPress Composer Plugin.
 *
 * Copyright (c) 2015 Steve Buzonas <steve@fancyguy.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FancyGuy\Composer\WordPress\Repository\Driver;

use Composer\Cache;
use Composer\Config;
use Composer\IO\IOInterface;
use Composer\Util\Svn as SvnUtil;

class SvnDriver extends VcsDriver
{

    protected $packages;
    protected $baseUrl;
    protected $url;
    protected $util;

    public function __construct($repoConfig, IOInterface $io, Config $config)
    {
        parent::__construct($repoConfig, $io, $config);
        $this->url = $this->baseUrl = $this->normalizeUrl($repoConfig['url']);
        $this->cache = new Cache($this->io, $this->config->get('cache-repo-dir').'/'.preg_replace('{[^a-z0-9.]}i', '-', $this->baseUrl));
    }

    /**
     * {@inheritDoc}
     */
    public function getPackages()
    {
        if (!$this->packages) {
            $this->packages = array();
            foreach ($this->process->splitLines($this->execute('svn ls', $this->baseUrl)) as $package) {
                if (preg_match('{(.*)/$}', $package, $match)) {
                    $this->packages[] = $match[1];
                }
            }
        }
        return $this->packages;
    }

    /**
     * @{inheritDoc}
     */
    public static function supports($repoConfig, IOInterface $io, Config $config)
    {
        if (!isset($repoConfig['url'])) {
            return false;
        }
        $url = static::normalizeUrl($repoConfig['url']);
        if (preg_match('#(^svn://|^svn\+ssh://|svn\.)#i', $url)) {
            return true;
        }

        // proceed with deep check for local urls since they are fast to process
        if (!$deep && !Filesystem::isLocalPath($url)) {
            return false;
        }
        $processExecutor = new ProcessExecutor();

        $exit = $processExecutor->execute(
            "svn info --non-interactive {$url}",
            $ignoredOutput
        );

        if ($exit === 0) {
            // This is definitely a Subversion repository.
            return true;
        }

        if (false !== stripos($processExecutor->getErrorOutput(), 'authorization failed:')) {
            // This is likely a remote Subversion repository that requires
            // authentication. We will handle actual authentication later.
            return true;
        }

        return false;
    }

    /**
     * Execute an SVN command and try to fix up the process with credentials
     * if necessary.
     *
     * @param  string            $command The svn command to run.
     * @param  string            $url     The SVN URL.
     * @throws \RuntimeException
     * @return string
     */
    protected function execute($command, $url)
    {
        if (null === $this->util) {
            $this->util = new SvnUtil($this->baseUrl, $this->io, $this->config, $this->process);
        }

        try {
            return $this->util->execute($command, $url);
        } catch (\RuntimeException $e) {
            if (0 !== $this->process->execute('svn --version', $ignoredOutput)) {
                throw new \RuntimeException('Failed to load '.$this->url.', svn was not found, check that it is installed and in your PATH env.' . "\n\n" . $this->process->getErrorOutput());
            }

            throw new \RuntimeException(
                'Repository '.$this->url.' could not be processed, '.$e->getMessage()
            );
        }
    }
}
