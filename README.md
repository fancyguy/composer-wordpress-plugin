# A [Composer](http://getcomposer.org) Plugin for [WordPress](http://wordpress.org) Development

[![Build Status](http://img.shields.io/travis/fancyguy/composer-wordpress-plugin.svg)](http://travis-ci.org/fancyguy/composer-wordpress-plugin)

This is a Composer plugin to add WordPress packages from the SVN repository.

## Getting Started

This plugin needs to be globally installed in order to initialize the repositories early enough.

```
composer global require fancyguy/composer-wordpress-plugin
```

## Repositories

**WordPressCoreRepository** - This provides the `wordpress/wordpress` package from SVN as a source install and from the API as a dist install.

**WordPressThemeRepository** - This exposes themes on wordpress.org as `wordpress-theme/*` from the SVN for source or the API for dist.

**WordPressPluginRepository** - This exposes plugins on wordpress.org as `wordpress-plugin/*` from the SVN for source or the API for dist.

## Installers

**CoreInstaller** - This installs the `wordpress-core` package, writes `wp-config.php` and generates salts.

**ThemeInstaller** - This installs `wordpress-theme` packages into the configured theme directory.

**PluginInstaller** - This installs the `wordpress-plugin` packages into the configured plugin directory.

## Configuration

The installers use the following default configuration to define the corresponding constants in `wp-config.php`

```json
{
    "extra": {
        "wordpress": {
            "webroot": "wordpress",
            "content-path": "wp-content",
            "themes-path": "${contentPath}/themes",
            "plugins-path": "${contentPath}/plugins",
            "mu-plugins-path": "${contentPath}/mu-plugins"
        }
    }
}
```
