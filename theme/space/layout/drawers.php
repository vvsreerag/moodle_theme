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
 * Modern drawer layout for Space theme with Docked Sidebar & Dashboard Stats.
 *
 * @package    theme_space
 * @copyright  2026 RoseaThemes inspired Space theme for Moodle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/completionlib.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

// Determine authentication and guest status.
$isloggedin = isloggedin() && !isguestuser();
$isguest = isguestuser();
$loginurl = get_login_url();

$isdashboard = ($PAGE->pagelayout === 'mydashboard');
$ismycourses = ($PAGE->pagelayout === 'mycourses' || strpos($PAGE->url->get_path(), '/my/courses.php') !== false);
$issitehome = ($PAGE->pagelayout === 'frontpage');
$iscalendar = ($PAGE->pagelayout === 'calendar' || strpos($PAGE->url->get_path(), '/calendar/') !== false);
$iscoursecategory = ($PAGE->pagelayout === 'coursecategory' || strpos($PAGE->url->get_path(), '/course/index.php') !== false);
$iscourse = (!$iscoursecategory && ($PAGE->pagelayout === 'course' || ($PAGE->course && $PAGE->course->id > 1)));
$issiteadmin = $isloggedin && (is_siteadmin() || has_capability('moodle/site:config', context_system::instance()));
$isuserfiles = (strpos($PAGE->url->get_path(), '/user/files.php') !== false);

// Capabilities & permissions for navigation items.
$canviewdashboard = $isloggedin && !empty($CFG->enabledashboard);
$canviewmycourses = $isloggedin;
$canviewcalendar = $isloggedin;

$canviewprivatefiles = false;
if ($isloggedin) {
    $usercontext = context_user::instance($USER->id);
    $canviewprivatefiles = has_capability('moodle/user:manageownfiles', $usercontext);
}

$canviewcontentbank = false;
if ($isloggedin) {
    $systemcontext = context_system::instance();
    $canviewcontentbank = has_capability('moodle/contentbank:access', $systemcontext) ||
                          has_capability('moodle/contentbank:viewunlistedcontent', $systemcontext);
}

$canedit = $isloggedin && $PAGE->user_allowed_editing();

if ($isloggedin) {
    $courseindexopen = (get_user_preferences('drawer-open-index', false) == true);
    $blockpref = get_user_preferences('drawer-open-block', null);
    if ($isdashboard || $ismycourses) {
        $blockdraweropen = false;
    } else {
        $blockdraweropen = ($blockpref === null) ? false : ($blockpref == true || $blockpref === '1');
    }
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING') && get_user_preferences('behat_keep_drawer_closed') != 1) {
    $blockdraweropen = true;
}

$coursetitle = ($PAGE->course && $PAGE->course->id > 1) ? format_string($PAGE->course->fullname) : '';
$coursebreadcrumbs = [];
if ($iscourse && $PAGE->course && $PAGE->course->id > 1) {
    if ($canviewdashboard) {
        $coursebreadcrumbs[] = ['name' => 'Dashboard', 'url' => (new moodle_url('/my/'))->out(false)];
    }
    if ($canviewmycourses) {
        $coursebreadcrumbs[] = ['name' => 'My courses', 'url' => (new moodle_url('/my/courses.php'))->out(false)];
    }
    $coursebreadcrumbs[] = ['name' => $coursetitle, 'url' => (new moodle_url('/course/view.php', ['id' => $PAGE->course->id]))->out(false), 'isactive' => true];
}

$calendarbreadcrumbs = [];
$calendartabs = [];
if ($iscalendar) {
    $curview = optional_param('view', 'month', PARAM_ALPHA);
    $calendartabs = [
        [
            'name' => 'Month',
            'url' => (new moodle_url('/calendar/view.php', ['view' => 'month']))->out(false),
            'isactive' => ($curview === 'month'),
        ],
        [
            'name' => 'Day',
            'url' => (new moodle_url('/calendar/view.php', ['view' => 'day']))->out(false),
            'isactive' => ($curview === 'day'),
        ],
        [
            'name' => 'Upcoming events',
            'url' => (new moodle_url('/calendar/view.php', ['view' => 'upcoming']))->out(false),
            'isactive' => ($curview === 'upcoming'),
        ],
    ];
    if ($canviewdashboard) {
        $calendarbreadcrumbs[] = ['name' => 'Dashboard', 'url' => (new moodle_url('/my/'))->out(false)];
    }
    $calendarbreadcrumbs[] = ['name' => 'Calendar', 'url' => (new moodle_url('/calendar/view.php'))->out(false), 'isactive' => true];
}

$userfilestabs = [];
$userfilesbreadcrumbs = [];
if ($isuserfiles && $isloggedin) {
    $userid = $USER->id;
    $userfilestabs = [
        [
            'name' => 'Profile',
            'url' => (new moodle_url('/user/profile.php', ['id' => $userid]))->out(false),
            'isactive' => false,
        ],
        [
            'name' => 'Private files',
            'url' => (new moodle_url('/user/files.php'))->out(false),
            'isactive' => true,
        ],
        [
            'name' => 'Preferences',
            'url' => (new moodle_url('/user/preferences.php'))->out(false),
            'isactive' => false,
        ],
    ];
    if ($canviewdashboard) {
        $userfilesbreadcrumbs[] = ['name' => 'Dashboard', 'url' => (new moodle_url('/my/'))->out(false)];
    }
    $userfilesbreadcrumbs[] = ['name' => 'Private files', 'url' => (new moodle_url('/user/files.php'))->out(false), 'isactive' => true];
}

$categorytitle = 'Courses';
$categorybreadcrumbs = [];
$categorytabs = [];
if ($iscoursecategory) {
    $categoryid = optional_param('categoryid', 0, PARAM_INT);
    $catid = 1;
    if (!empty($PAGE->category)) {
        $catid = $PAGE->category->id;
        $categorytitle = format_string($PAGE->category->name, true, ['context' => context_coursecat::instance($catid)]);
    } else if ($categoryid) {
        $catid = $categoryid;
        try {
            $cat = core_course_category::get($categoryid);
            $categorytitle = format_string($cat->name, true, ['context' => context_coursecat::instance($cat->id)]);
        } catch (\Exception $e) {
            $categorytitle = 'Platform Administration';
        }
    } else {
        $categorytitle = 'Platform Administration';
    }

    if ($canviewdashboard) {
        $categorybreadcrumbs[] = ['name' => 'Dashboard', 'url' => (new moodle_url('/my/'))->out(false)];
    }
    $categorybreadcrumbs[] = ['name' => 'Courses', 'url' => (new moodle_url('/course/index.php'))->out(false)];
    $categorybreadcrumbs[] = [
        'name' => $categorytitle,
        'url' => (new moodle_url('/course/index.php', ['categoryid' => $catid]))->out(false),
        'isactive' => true
    ];

    $categorycontext = context_coursecat::instance($catid);
    $categoryurl = (new moodle_url('/course/index.php', ['categoryid' => $catid]))->out(false);
    $competencyurl = (new moodle_url('/admin/tool/lp/competencyframeworks.php', ['pagecontextid' => $categorycontext->id]))->out(false);
    $canmanagecategory = has_capability('moodle/category:manage', $categorycontext) || $issiteadmin;
    $managecategoryurl = (new moodle_url('/course/management.php', ['categoryid' => $catid]))->out(false);
    $addcourseurl = (new moodle_url('/course/edit.php', ['category' => $catid]))->out(false);

    $categorytabs = [
        [
            'name' => 'Category',
            'url' => $categoryurl,
            'isactive' => true,
        ],
    ];
}

$extraclasses = ['uses-drawers', 'theme-space', 'has-space-sidebar'];
if ($isdashboard) {
    $extraclasses[] = 'page-space-dashboard';
}
if ($ismycourses) {
    $extraclasses[] = 'page-mycourses';
    $extraclasses[] = 'page-space-mycourses';
}
if ($iscourse) {
    $extraclasses[] = 'page-space-course';
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}
$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = method_exists($OUTPUT, 'firstview_fakeblocks') ? $OUTPUT->firstview_fakeblocks() : false;

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

// Fetch categories for Space sidebar.
$sidebarcategories = [];
try {
    $allcategories = core_course_category::get_all();
    $count = 0;
    foreach ($allcategories as $cat) {
        if ($cat->is_uservisible()) {
            $sidebarcategories[] = [
                'name' => format_string($cat->name, true, ['context' => context_system::instance()]),
                'url' => (new moodle_url('/course/index.php', ['categoryid' => $cat->id]))->out(false),
                'coursecount' => $cat->coursecount,
                'isactive' => (isset($PAGE->category) && $PAGE->category->id == $cat->id),
            ];
            $count++;
            if ($count >= 6) {
                break;
            }
        }
    }
} catch (\Exception $e) {
    // Fallback if not available.
}
$hassidebarcategories = !empty($sidebarcategories);

// Fetch user's courses for Space sidebar (strictly enrolled courses for logged-in users only).
$sidebarmycourses = [];
$hasmycourses = false;
if ($isloggedin) {
    try {
        $mycourses = enrol_get_my_courses('id, fullname, shortname, enablecompletion');
        foreach ($mycourses as $mc) {
            $progress = 0;
            if (!empty($mc->enablecompletion)) {
                $progresspercentage = \core_completion\progress::get_course_progress_percentage($mc, $USER->id);
                if ($progresspercentage !== null) {
                    $progress = round($progresspercentage);
                }
            }
            $sidebarmycourses[] = [
                'fullname' => format_string($mc->fullname, true, ['context' => context_course::instance($mc->id)]),
                'url' => (new moodle_url('/course/view.php', ['id' => $mc->id]))->out(false),
                'progress' => $progress,
            ];
            if (count($sidebarmycourses) >= 8) {
                break;
            }
        }
        $hasmycourses = !empty($sidebarmycourses);
    } catch (\Exception $e) {
        // Fallback.
    }
}

// Dashboard statistics (only computed for logged-in user on dashboard).
$enrolledcount = 0;
$completedcount = 0;
if ($isloggedin && $isdashboard) {
    try {
        $mycourses = enrol_get_my_courses('id, enablecompletion');
        $enrolledcount = count($mycourses);
        foreach ($mycourses as $mc) {
            if (!empty($mc->enablecompletion)) {
                $cinfo = new completion_info($mc);
                if ($cinfo->is_course_complete($USER->id)) {
                    $completedcount++;
                }
            }
        }
    } catch (\Exception $e) {
        // Fallback.
    }
}
$dashboardstats = [
    'courses_enrolled' => $enrolledcount,
    'courses_completed' => $completedcount . '/' . $enrolledcount,
    'activities_completed' => '0',
    'activities_due' => '0',
];

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
    'isloggedin' => $isloggedin,
    'isguest' => $isguest,
    'loginurl' => $loginurl,
    'canviewdashboard' => $canviewdashboard,
    'canviewmycourses' => $canviewmycourses,
    'canviewcalendar' => $canviewcalendar,
    'canviewprivatefiles' => $canviewprivatefiles,
    'canviewcontentbank' => $canviewcontentbank,
    'canedit' => $canedit,
    'isdashboard' => $isdashboard,
    'ismycourses' => $ismycourses,
    'issitehome' => $issitehome,
    'iscalendar' => $iscalendar,
    'isuserfiles' => $isuserfiles,
    'calendartabs' => $calendartabs,
    'userfilestabs' => $userfilestabs,
    'calendarbreadcrumbs' => $calendarbreadcrumbs,
    'userfilesbreadcrumbs' => $userfilesbreadcrumbs,
    'iscourse' => $iscourse,
    'issiteadmin' => $issiteadmin,
    'coursetitle' => $coursetitle,
    'coursebreadcrumbs' => $coursebreadcrumbs,
    'hassidebarcategories' => $hassidebarcategories,
    'sidebarcategories' => $sidebarcategories,
    'hasmycourses' => $hasmycourses,
    'sidebarmycourses' => $sidebarmycourses,
    'dashboardstats' => $dashboardstats,
    'iscoursecategory' => $iscoursecategory,
    'categorytitle' => $categorytitle,
    'categorybreadcrumbs' => $categorybreadcrumbs,
    'categorytabs' => $categorytabs,
    'categoryurl' => $categoryurl,
    'competencyurl' => $competencyurl,
    'canmanagecategory' => $canmanagecategory,
    'managecategoryurl' => $managecategoryurl,
    'addcourseurl' => $addcourseurl,
];

echo $OUTPUT->render_from_template('theme_space/drawers', $templatecontext);
