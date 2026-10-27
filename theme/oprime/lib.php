<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme functions and callbacks for Space theme.
 *
 * @package    theme_space
 * @copyright  2026 RoseaThemes inspired Space theme for Moodle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Post-process the CSS tree.
 *
 * @param string $tree The CSS tree.
 * @param theme_config $theme The theme config object.
 */
function theme_oprime_css_tree_post_processor($tree, $theme) {
    $prefixer = new theme_boost\autoprefixer($tree);
    $prefixer->prefix();
}

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_oprime_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss' || empty($filename)) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/oprime/scss/preset/default.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_oprime', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        $scss .= file_get_contents($CFG->dirroot . '/theme/oprime/scss/preset/default.scss');
    }

    // Append Space 5 design system components and overrides.
    $scss .= "\n" . file_get_contents($CFG->dirroot . '/theme/oprime/scss/space.scss');

    return $scss;
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_oprime_get_pre_scss($theme) {
    global $CFG;

    $scss = '';

    // Brand color setting.
    if (!empty($theme->settings->brandcolor)) {
        $brandcolor = $theme->settings->brandcolor;
        $scss .= "\$primary: {$brandcolor} !default;\n";
        $scss .= "\$blue: {$brandcolor} !default;\n";
        $scss .= ":root { --rui-brand: {$brandcolor}; }\n";
    }

    // Secondary brand color.
    if (!empty($theme->settings->brandsecondary)) {
        $secondary = $theme->settings->brandsecondary;
        $scss .= "\$secondary: {$secondary} !default;\n";
        $scss .= ":root { --rui-secondary: {$secondary}; }\n";
    }

    // Body background color.
    if (!empty($theme->settings->bodybg)) {
        $bodybg = $theme->settings->bodybg;
        $scss .= "\$body-bg: {$bodybg} !default;\n";
        $scss .= ":root { --rui-bg: {$bodybg}; }\n";
    }

    // Prepend user-provided pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre . "\n";
    }

    return $scss;
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_oprime_get_extra_scss($theme) {
    global $CFG;
    $content = '';

    // Handle compact mode if enabled in settings.
    if (!empty($theme->settings->compactmode)) {
        $content .= "\nbody { font-size: 0.875rem; }\n";
        $content .= ".activity-item { padding: 0.5rem 0.75rem !important; }\n";
        $content .= ".card { padding: 0.75rem 1rem !important; }\n";
    }

    // Append raw SCSS if provided by administrator.
    if (!empty($theme->settings->scss)) {
        $content .= "\n" . $theme->settings->scss;
    }

    return $content;
}

/**
 * Serves files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_oprime_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM && in_array($filearea, ['logo', 'favicon', 'backgroundimage', 'loginbackgroundimage'])) {
        $theme = theme_config::load('oprime');
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

/**
 * User preferences for drawers.
 *
 * @return array
 */
function theme_oprime_user_preferences(): array {
    return [
        'drawer-open-block' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'drawer-open-index' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => true,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
    ];
}

/**
 * Page initialization callback to inject Google Fonts and styles.
 *
 * @param moodle_page $page The current page.
 */
function theme_oprime_page_init(moodle_page $page) {
    global $CFG;

    // Load modern Plus Jakarta Sans and Inter Google Fonts.
    $page->requires->css(new moodle_url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap'));

    // Apply compact mode class if configured.
    $theme = theme_config::load('oprime');
    if (!empty($theme->settings->compactmode)) {
        $page->add_body_class('rui-compact-mode');
    }
}
