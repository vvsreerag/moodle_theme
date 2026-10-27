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
 * Language strings for Space theme.
 *
 * @package    theme_oprime
 * @copyright  2026 RoseaThemes inspired Space theme for Moodle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Oprime';
$string['choosereadme'] = 'Space is a modern, sleek, and slightly compact premium Moodle theme inspired by RoseaThemes Space 5.0.7, designed with clean SaaS aesthetics, refined typography, and responsive modern layouts.';

// Setting Tabs.
$string['tabgeneral'] = 'General';
$string['tabbranding'] = 'Branding & Colors';
$string['tablayout'] = 'Layout & Typography';
$string['tabheader'] = 'Header & Navigation';
$string['tabbanner'] = 'Hero Banner';
$string['tabcoursecards'] = 'Course Cards';
$string['tabfooter'] = 'Footer';
$string['tabcustomcss'] = 'Custom SCSS / CSS';

// General settings.
$string['preset'] = 'Theme preset';
$string['preset_desc'] = 'Choose a preset to dramatically change the appearance of the theme.';
$string['presetdefault'] = 'Space Sleek (Default)';
$string['presetcompact'] = 'Space Ultra-Compact';
$string['presetindigo'] = 'Space Indigo SaaS';
$string['presetdark'] = 'Space Slate Dark';
$string['compactmode'] = 'Compact Mode';
$string['compactmode_desc'] = 'When enabled, reduces whitespace and padding across the dashboard, course sections, and navigation bars for maximum data density and a modern sleek SaaS look.';
$string['smoothanimations'] = 'Smooth Transitions';
$string['smoothanimations_desc'] = 'Enable modern subtle hover lifts, micro-animations, and fluid transitions.';

// Branding.
$string['brandcolor'] = 'Primary Brand Color';
$string['brandcolor_desc'] = 'The primary accent color for active navigation pills, primary buttons, badges, and links (Default: #3e81eb).';
$string['brandsecondary'] = 'Secondary Brand Color';
$string['brandsecondary_desc'] = 'Dark secondary color used for prominent headings, chips, and hero accents (Default: #0f172a).';
$string['brandaccent'] = 'Highlight Accent Color';
$string['brandaccent_desc'] = 'Vibrant highlight color for badges and alerts (Default: #10b981).';
$string['bodybg'] = 'Body Background Color';
$string['bodybg_desc'] = 'Modern muted background tint for page canvas (Default: #f8fafc).';
$string['sidebarbg'] = 'Sidebar Background Color';
$string['sidebarbg_desc'] = 'Background color of the navigation drawer (Default: #ffffff).';

// Layout & Typography.
$string['fontfamily'] = 'Typography Font Family';
$string['fontfamily_desc'] = 'Choose the primary modern sans-serif font family used throughout the interface.';
$string['font_jakarta'] = 'Plus Jakarta Sans (Modern & Crisp)';
$string['font_inter'] = 'Inter (Clean & Balanced)';
$string['font_outfit'] = 'Outfit (Geometric & Tech)';
$string['font_system'] = 'System Native Sans';
$string['cardradius'] = 'Card & Element Radius';
$string['cardradius_desc'] = 'Define the roundness of cards, badges, and buttons.';
$string['radius_compact'] = 'Compact (6px)';
$string['radius_sleek'] = 'Sleek (10px - Recommended)';
$string['radius_rounded'] = 'Rounded Modern (16px)';

// Header & Navigation.
$string['stickynavbar'] = 'Glassmorphism Sticky Navbar';
$string['stickynavbar_desc'] = 'Keep the top navigation bar fixed at the top with a subtle backdrop blur effect while scrolling.';
$string['showsearch'] = 'Navbar Search Bar';
$string['showsearch_desc'] = 'Display a modern rounded search pill in the header.';
$string['brandlogo'] = 'Custom Brand Logo';
$string['brandlogo_desc'] = 'Upload your custom logo image. If empty, the site short name will be displayed.';
$string['logowidth'] = 'Logo Max Height (px)';
$string['logowidth_desc'] = 'Maximum height for your custom logo in pixels (e.g. 36).';

// Hero Banner.
$string['herobanner_enable'] = 'Enable Modern Hero Banner';
$string['herobanner_enable_desc'] = 'Display an eye-catching, modern hero banner with greeting, stats, and quick CTA on the Dashboard and Frontpage.';
$string['herobanner_tag'] = 'Hero Badge Text';
$string['herobanner_tag_desc'] = 'Small badge text displayed above the main heading (e.g. "✦ Space Learning Experience").';
$string['herobanner_tag_default'] = '✦ Modern Space Experience';
$string['herobanner_title'] = 'Hero Banner Title';
$string['herobanner_title_desc'] = 'Main title for the welcome banner. You can use {fullname} to automatically display the logged-in user\'s full name.';
$string['herobanner_title_default'] = 'Welcome back, {fullname}!';
$string['herobanner_subtitle'] = 'Hero Banner Subtitle';
$string['herobanner_subtitle_desc'] = 'Short descriptive text below the title.';
$string['herobanner_subtitle_default'] = 'Track your learning journey, access upcoming courses, and achieve your goals with our streamlined learning platform.';
$string['herobanner_buttontext'] = 'Hero Action Button Text';
$string['herobanner_buttontext_default'] = 'Explore Courses';
$string['herobanner_buttonurl'] = 'Hero Action Button Link';
$string['herobanner_buttonurl_default'] = '/my/courses.php';

// Course Cards.
$string['coursecardhover'] = 'Card Hover Lift';
$string['coursecardhover_desc'] = 'Enable delicate hover lift and shadow elevation on course cards.';
$string['showcourseprogress'] = 'Show Course Progress Pill';
$string['showcourseprogress_desc'] = 'Display sleek rounded progress bars on dashboard course cards.';

// Footer.
$string['footercopyright'] = 'Copyright Notice';
$string['footercopyright_desc'] = 'Custom copyright notice displayed in the bottom footer.';
$string['footerabout'] = 'About Footer Text';
$string['footerabout_desc'] = 'Short description of your organization shown in the footer.';
$string['footershowsocial'] = 'Show Social Icons';
$string['footershowsocial_desc'] = 'Display social media quick links in the modern footer.';

// SCSS.
$string['rawscss'] = 'Raw SCSS';
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the stylesheet.';
$string['rawscsspre'] = 'Raw Initial SCSS';
$string['rawscsspre_desc'] = 'Use this field to provide initial SCSS code to override variables before anything else is compiled.';

// Space specific UI labels.
$string['searchcourses'] = 'Search courses, activities...';
$string['continuelearning'] = 'Continue';
$string['coursecompleted'] = 'Completed';
$string['courseprogress'] = 'Progress';
$string['quickstats'] = 'Quick Overview';
$string['enrolledcourses'] = 'Enrolled Courses';
$string['inprogress'] = 'In Progress';
$string['completed'] = 'Completed';
$string['privacy:metadata'] = 'The Space theme does not store any personal data about any user.';
