<?php
/**
 * This file is part of FancyGuy WordPress Composer Plugin.
 *
 * Copyright (c) 2015 Steve Buzonas <steve@fancyguy.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FancyGuy\Composer\WordPress\Util;

use Composer\Cache as BaseCache;

class Cache extends BaseCache
{

    public function read($file, $staleSince = null)
    {
        if ($staleSince && file_exists($this->getRoot() . $file)) {
            try {
                $dt = new \DateTime($staleSince);
                $repomtime = (int) $dt->format('U');
                $filemtime = filemtime($this->getRoot() . $file);
                
                if ($filemtime < $repomtime) {
                    unlink($this->getRoot() . $file);
                }
            } catch (\Exception $e) {
            }
        }
        
        return parent::read($file);
    }

    public function write($file, $contents)
    {
        $absPath = $this->getRoot() . $file;
        if ($this->isEnabled() && !is_dir(dirname($$absPath))) {
            @mkdir(dirname($absPath), 0777, true);
        }
        parent::write($file, $contents);
    }
}
