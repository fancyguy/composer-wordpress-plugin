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

class SPDXLicense
{
    const REGISTRY = 'http://www.spdx.org/licenses/';
    const PATH_IDENTIFIERS = '//*[@typeof="spdx:License"]/code[@property="spdx:licenseId"]/text()';
    const PATH_NAMES = '//*[@typeof="spdx:License"]/preceding-sibling::*/*[contains(@rel, "rdf")]/text()';

    private static $licenses;

    public static function getLicenses()
    {
        if (!self::$licenses) {
            $identifiers = self::importNodesFromURL(
                self::REGISTRY,
                self::PATH_IDENTIFIERS
            );

            $names = self::importNodesFromURL(
                self::REGISTRY,
                self::PATH_NAMES
            );

            self::$licenses = array_combine($identifiers, $names);
        }

        return self::$licenses;
    }

    public static function getIdentifierFromName($name)
    {
        $identifier = array_search($name, self::getLicenses());
        if (!$identifier && preg_match('{ v\d+ }', $name)) {
            $identifier = array_search(preg_replace('{ (v\d+) }', ' $1.0 ', $name), self::getLicenses());
        }
        if (!$identifier && preg_match('{ v\d+.0 }', $name)) {
            $identifier = array_search(preg_replace('{ (v\d+)\.0 }', ' $1 ', $name), self::getLicenses());
        }
        return $identifier;
    }

    private static function importNodesFromURL($url, $expressionTextNodes)
    {
        $doc = new \DOMDocument();
        $doc->loadHTMLFile($url);
        $xp = new \DOMXPath($doc);
        $codes = $xp->query($expressionTextNodes);
        if (!$codes) {
            throw new \Exception(sprintf('XPath query failed: %s', $expressionTextNodes));
        }
        if ($codes->length < 20) {
            throw new \Exception('Obtaining the license table failed, there can not be less than 20 identifiers.');
        }
        $identifiers = array();
        foreach ($codes as $code) {
            $identifiers[] = $code->nodeValue;
        }
        return $identifiers;
    }
}
