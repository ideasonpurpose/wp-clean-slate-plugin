<?php
/**
 * Plugin Name: Clean Slate
 * Plugin URI: https://www.github.com/ideasonpurpose
 * Description: An experimental plugin for removing ALL WordPress styles from the front end
 * Version: 0.0.1
 * Author:            Ideas On Purpose
 * Author URI:        https://www.ideasonpurpose.com
 * License: MIT
 * Requires at least: 6
 * Requires PHP: 8
 *
 */

namespace IdeasOnPurpose;

defined('ABSPATH') or die();

require __DIR__ . '/vendor/autoload.php';

class CleanSlate
{
    public function __construct()
    {
        add_filter('print_styles_array', [$this, 'remove_styles']);

        // add_filter('style_loader_tag', [$this, 'dump_tag'], 10, 4);
        // add_action('wp_print_scripts', [$this, 'scrub_styles']);
    }

    public function remove_styles($styles)
    {
        if (is_admin()) {
            return $styles;
        }
        /**
         * Style handles to preserve
         * TODO: Provide an interface for adding to this?
         */
        $keepStyles = ['admin-bar', 'dashicons'];

        /**
         * Styles to remove
         * All style handles starting with 'wp-block' will also be removed
         * Removal can be overridden by adding a handle to $keepStyles
         */
        $dumpStyles = [
            'classic-theme-styles',
            'core-block-supports',
            'global-styles',
            'wp-webfonts',
        ];

        $newStyles = [];

        foreach ($styles as $handle) {
            /**
             * Default to keeping everything in $keepFiles first
             */
            if (in_array($handle, $keepStyles)) {
                $newStyles[] = $handle;
                continue;
            }

            /**
             * Skip anything listed in $dumpStyles
             */
            if (in_array($handle, $dumpStyles)) {
                continue;
            }

            /**
             * Remove all wp-block* styles
             */
            if (strpos($handle, 'wp-block') === 0) {
                continue;
            }

            /**
             * Add whatever's left
             */
            $newStyles[] = $handle;
        }

        // DEBUG SNIPPET START
        if (class_exists('Kint')) {
            \Kint::$mode_default = \Kint::MODE_CLI;
        }
        if (class_exists('Sage')) {
            \Sage::enabled(\Sage::MODE_CLI);
        }

        error_log(@d($styles, $newStyles, strpos($handle, 'wp-block')));

        if (class_exists('Kint')) {
            \Kint::$mode_default = \Kint::MODE_RICH;
        }
        if (class_exists('Sage')) {
            \Sage::enabled(\Sage::MODE_RICH);
        }
        // DEBUG SNIPPET END

        return $newStyles;
    }
}

new CleanSlate();
