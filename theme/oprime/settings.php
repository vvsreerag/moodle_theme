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
 * Settings for Space theme.
 *
 * @package    theme_oprime
 * @copyright  2026 RoseaThemes inspired Space theme for Moodle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingoprime', get_string('configtitle', 'theme_oprime'));

    // ----------------------------------------------------------------------
    // Tab 1: General & Layout
    // ----------------------------------------------------------------------
    $page = new admin_settingpage('theme_oprime_general', get_string('tabgeneral', 'theme_oprime'));

    // Compact Mode.
    $name = 'theme_oprime/compactmode';
    $title = get_string('compactmode', 'theme_oprime');
    $description = get_string('compactmode_desc', 'theme_oprime');
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Smooth Transitions & Animations.
    $name = 'theme_oprime/smoothanimations';
    $title = get_string('smoothanimations', 'theme_oprime');
    $description = get_string('smoothanimations_desc', 'theme_oprime');
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Sticky Glassmorphism Navbar.
    $name = 'theme_oprime/stickynavbar';
    $title = get_string('stickynavbar', 'theme_oprime');
    $description = get_string('stickynavbar_desc', 'theme_oprime');
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Show search bar in Navbar.
    $name = 'theme_oprime/showsearch';
    $title = get_string('showsearch', 'theme_oprime');
    $description = get_string('showsearch_desc', 'theme_oprime');
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // ----------------------------------------------------------------------
    // Tab 2: Branding & Colors
    // ----------------------------------------------------------------------
    $page = new admin_settingpage('theme_oprime_branding', get_string('tabbranding', 'theme_oprime'));

    // Brand Primary (Rosea Space Vivid Blue #3e81eb).
    $name = 'theme_oprime/brandcolor';
    $title = get_string('brandcolor', 'theme_oprime');
    $description = get_string('brandcolor_desc', 'theme_oprime');
    $default = '#3e81eb';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Secondary Brand Color (Slate 900 #0f172a).
    $name = 'theme_oprime/brandsecondary';
    $title = get_string('brandsecondary', 'theme_oprime');
    $description = get_string('brandsecondary_desc', 'theme_oprime');
    $default = '#0f172a';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Body Canvas Background (#f8fafc).
    $name = 'theme_oprime/bodybg';
    $title = get_string('bodybg', 'theme_oprime');
    $description = get_string('bodybg_desc', 'theme_oprime');
    $default = '#ffffff';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Sidebar Background (#ffffff).
    $name = 'theme_oprime/sidebarbg';
    $title = get_string('sidebarbg', 'theme_oprime');
    $description = get_string('sidebarbg_desc', 'theme_oprime');
    $default = '#ffffff';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // ----------------------------------------------------------------------
    // Tab 3: Hero Banner
    // ----------------------------------------------------------------------
    $page = new admin_settingpage('theme_oprime_banner', get_string('tabbanner', 'theme_oprime'));

    $name = 'theme_oprime/herobanner_enable';
    $title = get_string('herobanner_enable', 'theme_oprime');
    $description = get_string('herobanner_enable_desc', 'theme_oprime');
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_oprime/herobanner_tag';
    $title = get_string('herobanner_tag', 'theme_oprime');
    $description = get_string('herobanner_tag_desc', 'theme_oprime');
    $default = get_string('herobanner_tag_default', 'theme_oprime');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_oprime/herobanner_title';
    $title = get_string('herobanner_title', 'theme_oprime');
    $description = get_string('herobanner_title_desc', 'theme_oprime');
    $default = get_string('herobanner_title_default', 'theme_oprime');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_oprime/herobanner_subtitle';
    $title = get_string('herobanner_subtitle', 'theme_oprime');
    $description = get_string('herobanner_subtitle_desc', 'theme_oprime');
    $default = get_string('herobanner_subtitle_default', 'theme_oprime');
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_oprime/herobanner_buttontext';
    $title = get_string('herobanner_buttontext', 'theme_oprime');
    $description = '';
    $default = get_string('herobanner_buttontext_default', 'theme_oprime');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_oprime/herobanner_buttonurl';
    $title = get_string('herobanner_buttonurl', 'theme_oprime');
    $description = '';
    $default = get_string('herobanner_buttonurl_default', 'theme_oprime');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $settings->add($page);

    // ----------------------------------------------------------------------
    // Tab 4: Modern Footer
    // ----------------------------------------------------------------------
    $page = new admin_settingpage('theme_oprime_footer', get_string('tabfooter', 'theme_oprime'));

    $name = 'theme_oprime/footercopyright';
    $title = get_string('footercopyright', 'theme_oprime');
    $description = get_string('footercopyright_desc', 'theme_oprime');
    $default = '© ' . date('Y') . ' Space Platform. All rights reserved.';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_oprime/footerabout';
    $title = get_string('footerabout', 'theme_oprime');
    $description = get_string('footerabout_desc', 'theme_oprime');
    $default = 'Empowering learners with modern, sleek, and intuitive digital learning experiences.';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_oprime/footershowsocial';
    $title = get_string('footershowsocial', 'theme_oprime');
    $description = get_string('footershowsocial_desc', 'theme_oprime');
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $settings->add($page);

    // ----------------------------------------------------------------------
    // Tab 5: Custom SCSS / CSS
    // ----------------------------------------------------------------------
    $page = new admin_settingpage('theme_oprime_customcss', get_string('tabcustomcss', 'theme_oprime'));

    $name = 'theme_oprime/scsspre';
    $title = get_string('rawscsspre', 'theme_oprime');
    $description = get_string('rawscsspre_desc', 'theme_oprime');
    $setting = new admin_setting_scsscode($name, $title, $description, '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_oprime/scss';
    $title = get_string('rawscss', 'theme_oprime');
    $description = get_string('rawscss_desc', 'theme_oprime');
    $setting = new admin_setting_scsscode($name, $title, $description, '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
