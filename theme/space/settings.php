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
 * @package    theme_space
 * @copyright  2026 RoseaThemes inspired Space theme for Moodle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingspace', get_string('configtitle', 'theme_space'));

    // ----------------------------------------------------------------------
    // Tab 1: General & Layout
    // ----------------------------------------------------------------------
    $page = new admin_settingpage('theme_space_general', get_string('tabgeneral', 'theme_space'));

    // Compact Mode.
    $name = 'theme_space/compactmode';
    $title = get_string('compactmode', 'theme_space');
    $description = get_string('compactmode_desc', 'theme_space');
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Smooth Transitions & Animations.
    $name = 'theme_space/smoothanimations';
    $title = get_string('smoothanimations', 'theme_space');
    $description = get_string('smoothanimations_desc', 'theme_space');
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Sticky Glassmorphism Navbar.
    $name = 'theme_space/stickynavbar';
    $title = get_string('stickynavbar', 'theme_space');
    $description = get_string('stickynavbar_desc', 'theme_space');
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Show search bar in Navbar.
    $name = 'theme_space/showsearch';
    $title = get_string('showsearch', 'theme_space');
    $description = get_string('showsearch_desc', 'theme_space');
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // ----------------------------------------------------------------------
    // Tab 2: Branding & Colors
    // ----------------------------------------------------------------------
    $page = new admin_settingpage('theme_space_branding', get_string('tabbranding', 'theme_space'));

    // Brand Primary (Rosea Space Vivid Blue #3e81eb).
    $name = 'theme_space/brandcolor';
    $title = get_string('brandcolor', 'theme_space');
    $description = get_string('brandcolor_desc', 'theme_space');
    $default = '#3e81eb';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Secondary Brand Color (Slate 900 #0f172a).
    $name = 'theme_space/brandsecondary';
    $title = get_string('brandsecondary', 'theme_space');
    $description = get_string('brandsecondary_desc', 'theme_space');
    $default = '#0f172a';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Body Canvas Background (#f8fafc).
    $name = 'theme_space/bodybg';
    $title = get_string('bodybg', 'theme_space');
    $description = get_string('bodybg_desc', 'theme_space');
    $default = '#f8fafc';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Sidebar Background (#ffffff).
    $name = 'theme_space/sidebarbg';
    $title = get_string('sidebarbg', 'theme_space');
    $description = get_string('sidebarbg_desc', 'theme_space');
    $default = '#ffffff';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // ----------------------------------------------------------------------
    // Tab 3: Hero Banner
    // ----------------------------------------------------------------------
    $page = new admin_settingpage('theme_space_banner', get_string('tabbanner', 'theme_space'));

    $name = 'theme_space/herobanner_enable';
    $title = get_string('herobanner_enable', 'theme_space');
    $description = get_string('herobanner_enable_desc', 'theme_space');
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_space/herobanner_tag';
    $title = get_string('herobanner_tag', 'theme_space');
    $description = get_string('herobanner_tag_desc', 'theme_space');
    $default = get_string('herobanner_tag_default', 'theme_space');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_space/herobanner_title';
    $title = get_string('herobanner_title', 'theme_space');
    $description = get_string('herobanner_title_desc', 'theme_space');
    $default = get_string('herobanner_title_default', 'theme_space');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_space/herobanner_subtitle';
    $title = get_string('herobanner_subtitle', 'theme_space');
    $description = get_string('herobanner_subtitle_desc', 'theme_space');
    $default = get_string('herobanner_subtitle_default', 'theme_space');
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_space/herobanner_buttontext';
    $title = get_string('herobanner_buttontext', 'theme_space');
    $description = '';
    $default = get_string('herobanner_buttontext_default', 'theme_space');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_space/herobanner_buttonurl';
    $title = get_string('herobanner_buttonurl', 'theme_space');
    $description = '';
    $default = get_string('herobanner_buttonurl_default', 'theme_space');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $settings->add($page);

    // ----------------------------------------------------------------------
    // Tab 4: Modern Footer
    // ----------------------------------------------------------------------
    $page = new admin_settingpage('theme_space_footer', get_string('tabfooter', 'theme_space'));

    $name = 'theme_space/footercopyright';
    $title = get_string('footercopyright', 'theme_space');
    $description = get_string('footercopyright_desc', 'theme_space');
    $default = '© ' . date('Y') . ' Space Platform. All rights reserved.';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_space/footerabout';
    $title = get_string('footerabout', 'theme_space');
    $description = get_string('footerabout_desc', 'theme_space');
    $default = 'Empowering learners with modern, sleek, and intuitive digital learning experiences.';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_space/footershowsocial';
    $title = get_string('footershowsocial', 'theme_space');
    $description = get_string('footershowsocial_desc', 'theme_space');
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $settings->add($page);

    // ----------------------------------------------------------------------
    // Tab 5: Custom SCSS / CSS
    // ----------------------------------------------------------------------
    $page = new admin_settingpage('theme_space_customcss', get_string('tabcustomcss', 'theme_space'));

    $name = 'theme_space/scsspre';
    $title = get_string('rawscsspre', 'theme_space');
    $description = get_string('rawscsspre_desc', 'theme_space');
    $setting = new admin_setting_scsscode($name, $title, $description, '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_space/scss';
    $title = get_string('rawscss', 'theme_space');
    $description = get_string('rawscss_desc', 'theme_space');
    $setting = new admin_setting_scsscode($name, $title, $description, '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
