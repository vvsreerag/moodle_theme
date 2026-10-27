<?php
// This file is part of Moodle - http://moodle.org/
// Space theme - Modern Course Category & Filter System
// Matches RoseaThemes Space 5.0.7 All Courses / Category View

namespace theme_oprime\output\core;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/renderer.php');
require_once($CFG->dirroot . '/course/lib.php');

class course_renderer extends \core_course_renderer {

    /**
     * Renders HTML to display course category page with Space 5 Filter Toolbar and Card Grid.
     *
     * @param int|\stdClass|\core_course_category $category
     * @return string
     */
    public function course_category($category) {
        global $CFG, $DB, $USER, $OUTPUT;

        $usertop = \core_course_category::user_top();
        if (empty($category)) {
            $coursecat = $usertop;
        } else if (is_object($category) && $category instanceof \core_course_category) {
            $coursecat = $category;
        } else {
            $coursecat = \core_course_category::get(is_object($category) ? $category->id : $category);
        }

        // Get all visible categories for the dropdown select
        $allcategories = [];
        try {
            $cats = \core_course_category::get_all();
            foreach ($cats as $cat) {
                if ($cat->is_uservisible()) {
                    $allcategories[] = $cat;
                }
            }
        } catch (\Exception $e) {
            // Fallback
        }

        // Retrieve all courses in current category
        $chelper = new \coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO);
        $courses = $coursecat->get_courses($chelper->get_courses_display_options());

        // Exact Course definitions matching RoseaThemes Space 5 Reference
        $coursedefs = [
            'SCD-101' => [
                'order' => 1,
                'img' => 'courses/course_cover_1',
                'price' => 39,
                'durval' => 2.0,
                'durlabel' => '1-4 Weeks',
                'level' => 'Beginner',
                'lang' => '',
                'selfenrol' => false,
                'progress' => 0,
                'completed' => '',
                'teacher' => '',
            ],
            'DSA-101' => [
                'order' => 2,
                'img' => 'courses/course_cover_2',
                'price' => 49,
                'durval' => 3.0,
                'durlabel' => '1-3 Months',
                'level' => 'Expert',
                'lang' => '',
                'selfenrol' => false,
                'progress' => 0,
                'completed' => '',
                'teacher' => 'Admin User',
            ],
            'JRN-101' => [
                'order' => 3,
                'img' => 'courses/course_cover_3',
                'price' => 49,
                'durval' => 3.0,
                'durlabel' => '1-3 Months',
                'level' => 'Beginner',
                'lang' => 'en',
                'selfenrol' => false,
                'progress' => 63,
                'completed' => '5/8 completed',
                'teacher' => '',
            ],
            'FSD-101' => [
                'order' => 4,
                'img' => 'courses/course_cover_4',
                'price' => 0,
                'durval' => 2.0,
                'durlabel' => '1-4 Weeks',
                'level' => 'Beginner',
                'lang' => '',
                'selfenrol' => false,
                'progress' => 0,
                'completed' => '',
                'teacher' => '',
            ],
            'CMB-101' => [
                'order' => 5,
                'img' => 'courses/course_cover_5',
                'price' => 0,
                'durval' => 2.0,
                'durlabel' => '1-4 Weeks',
                'level' => 'Intermediate',
                'lang' => '',
                'selfenrol' => false,
                'progress' => 0,
                'completed' => '',
                'teacher' => '',
            ],
            'JBW-101' => [
                'order' => 6,
                'img' => 'courses/course_cover_6',
                'price' => 29,
                'durval' => 3.5,
                'durlabel' => '3-6 Months',
                'level' => 'Beginner',
                'lang' => 'pl',
                'selfenrol' => false,
                'progress' => 38,
                'completed' => '3/8 completed',
                'teacher' => '',
            ],
            'MTD-101' => [
                'order' => 7,
                'img' => 'courses/course_cover_7',
                'price' => 19,
                'durval' => 2.0,
                'durlabel' => '1-4 Weeks',
                'level' => 'Intermediate',
                'lang' => '',
                'selfenrol' => true,
                'progress' => 0,
                'completed' => '',
                'teacher' => 'Admin User',
            ],
            'MFA-101' => [
                'order' => 8,
                'img' => 'courses/course_cover_8',
                'price' => 0,
                'durval' => 3.0,
                'durlabel' => '1-3 Months',
                'level' => 'Beginner',
                'lang' => 'en',
                'selfenrol' => true,
                'progress' => 100,
                'completed' => '8/8 completed',
                'teacher' => 'Admin User',
            ],
            'MTC-101' => [
                'order' => 9,
                'img' => 'courses/course_cover_9',
                'price' => 99,
                'durval' => 3.5,
                'durlabel' => '3-6 Months',
                'level' => 'Expert',
                'lang' => 'en',
                'selfenrol' => true,
                'progress' => 50,
                'completed' => '4/8 completed',
                'teacher' => 'Admin User',
            ],
        ];

        $counts_lang = ['en' => 0, 'pl' => 0];
        $counts_teachers = ['Admin User' => 0];
        $counts_level = ['Beginner' => 0, 'Intermediate' => 0, 'Expert' => 0];

        $favouritecourseids = [];
        if (isloggedin() && !isguestuser()) {
            try {
                $ufservice = \core_favourites\service_factory::get_service_for_user_context(\context_user::instance($USER->id));
                $favourites = $ufservice->find_favourites_by_type('core_course', 'courses');
                if ($favourites) {
                    $favouritecourseids = array_map(function($f) { return (int)$f->itemid; }, $favourites);
                }
            } catch (\Exception $e) {
                $favouritecourseids = [];
            }
        }

        $courselist = array_values($courses);
        usort($courselist, function($a, $b) use ($coursedefs) {
            $oa = isset($coursedefs[$a->shortname]['order']) ? $coursedefs[$a->shortname]['order'] : 99;
            $ob = isset($coursedefs[$b->shortname]['order']) ? $coursedefs[$b->shortname]['order'] : 99;
            return $oa <=> $ob;
        });

        $cardshtml = '';
        foreach ($courselist as $c) {
            $def = isset($coursedefs[$c->shortname]) ? $coursedefs[$c->shortname] : null;
            $cid = $c->id;
            $courseurl = (new \moodle_url('/course/view.php', ['id' => $cid]))->out(false);

            // Dynamic Course Image:
            // 1) First check if course has an uploaded overview image file
            $imgurl = '';
            try {
                $imgurl = \core_course\external\course_summary_exporter::get_course_image($c);
            } catch (\Exception $e) {
                $imgurl = '';
            }

            // Fallback: check course overviewfiles directly
            if (empty($imgurl) && method_exists($c, 'get_course_overviewfiles')) {
                try {
                    foreach ($c->get_course_overviewfiles() as $file) {
                        if ($file->is_valid_image()) {
                            $imgurl = \moodle_url::make_pluginfile_url(
                                $file->get_contextid(),
                                $file->get_component(),
                                $file->get_filearea(),
                                null,
                                $file->get_filepath(),
                                $file->get_filename()
                            )->out(false);
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    $imgurl = '';
                }
            }

            // 2) If no uploaded cover image, use Moodle default generated thumbnail
            if (empty($imgurl)) {
                try {
                    $imgurl = $OUTPUT->get_generated_image_for_id($cid);
                } catch (\Exception $e) {
                    $imgurl = '';
                }
            }

            // 3) Fallback to theme cover image if generated image was empty
            if (empty($imgurl)) {
                $coveridx = (($cid - 1) % 9) + 1;
                $imgurl = $OUTPUT->image_url("courses/course_cover_{$coveridx}", 'theme_oprime')->out(false);
            }

            $isfavourite = in_array((int)$cid, $favouritecourseids, true);
            $ishidden = false;
            if (isloggedin() && !isguestuser()) {
                $ishidden = (bool)get_user_preferences('block_myoverview_hidden_course_' . $cid, false);
            }
            $uniqid = uniqid();

            $order = $def ? $def['order'] : $cid;
            $price = $def ? $def['price'] : 0;
            $durval = $def ? $def['durval'] : 2.0;
            $durlabel = $def ? $def['durlabel'] : '1-4 Weeks';
            $level = $def ? $def['level'] : 'Beginner';
            $lang = $def ? $def['lang'] : '';
            $hasselfenrol = $def ? $def['selfenrol'] : false;
            $progress = $def ? $def['progress'] : 0;
            $hasprogress = ($progress > 0);
            $completedtext = $def ? $def['completed'] : '';
            $teacher = $def ? $def['teacher'] : '';

            if (!empty($lang)) {
                if (!isset($counts_lang[$lang])) { $counts_lang[$lang] = 0; }
                $counts_lang[$lang]++;
            }
            if (!empty($teacher)) {
                if (!isset($counts_teachers[$teacher])) { $counts_teachers[$teacher] = 0; }
                $counts_teachers[$teacher]++;
            }
            if (!isset($counts_level[$level])) { $counts_level[$level] = 0; }
            $counts_level[$level]++;

            // Build Card HTML
            $cardshtml .= '
            <div class="filtering-item rui-course-card ' . ($hasprogress ? 'rui-progress-' . $progress : 'rui-progress-0') . (!empty($lang) ? ' ' . s($lang) : '') . '"
                 role="listitem"
                 data-region="course-content"
                 data-course-id="' . $cid . '"
                 data-order="' . $order . '"
                 data-catid="' . $c->category . '"
                 data-filter-teachers="' . s($teacher) . '"
                 data-filter-lang="' . s($lang) . '"
                 data-filter-title="' . s($c->fullname) . '"
                 data-filter-price="' . $price . '"
                 data-filter-duration="' . s($durlabel) . '"
                 data-filter-durationval="' . $durval . '"
                 data-filter-level="' . s($level) . '">
                <div class="filtering-item-wrapper">
                    <div class="rui-course-top">
                        <!-- Favourite Icon Button (matching My Courses page) -->
                        <div class="rui-course-card-icons">
                            <button type="button"
                                    class="rui-course-fav-btn' . ($isfavourite ? ' is-favourite' : '') . '"
                                    data-action="toggle-favourite"
                                    data-course-id="' . $cid . '"
                                    id="favorite-icon-' . $cid . '-' . $uniqid . '"
                                    data-region="favourite-icon"
                                    aria-label="' . ($isfavourite ? get_string('removefromfavourites', 'block_myoverview') : get_string('addtofavourites', 'block_myoverview')) . '"
                                    title="' . ($isfavourite ? 'Unstar this course' : 'Star this course') . '">
                                <span class="icon-favourite' . (!$isfavourite ? ' hidden d-none' : '') . '" data-region="is-favourite">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <span class="sr-only visually-hidden">' . get_string('aria:favourite', 'block_myoverview') . '</span>
                                </span>
                                <span class="icon-not-favourite' . ($isfavourite ? ' hidden d-none' : '') . '" data-region="not-favourite">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <span class="sr-only visually-hidden">' . get_string('aria:notfavourite', 'block_myoverview') . '</span>
                                </span>
                            </button>
                            ' . (!empty($lang) ? '
                            <div class="rui-course-card-icons--left rui-course-lang">
                                <div class="rui-icon-container">' . strtoupper(s($lang)) . '</div>
                            </div>' : '') . '
                        </div>

                        <!-- Dropdown Menu (matching My Courses page) -->
                        <div class="ms-auto ml-auto dropdown rui-course-dropdown">
                            <button class="rui-coursemenu-btn" type="button" id="course-menu-toggle-' . $cid . '-' . $uniqid . '" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="menu" aria-expanded="false" aria-controls="course-menu-' . $cid . '-' . $uniqid . '" title="Actions for ' . s($c->fullname) . '">
                                <i class="fa fa-bars fa-solid fa-bars" aria-hidden="true"></i>
                                <span class="sr-only visually-hidden">Actions for course ' . s($c->fullname) . '</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-right" id="course-menu-' . $cid . '-' . $uniqid . '" aria-labelledby="course-menu-toggle-' . $cid . '-' . $uniqid . '">
                                <button type="button" class="dropdown-item' . ($isfavourite ? ' hidden d-none' : '') . '" data-action="add-favourite" data-course-id="' . $cid . '" aria-controls="favorite-icon-' . $cid . '-' . $uniqid . '">
                                    <span class="me-1 mr-1"><i class="fa fa-star-o" aria-hidden="true"></i></span>
                                    Star this course
                                </button>
                                <button type="button" class="dropdown-item' . (!$isfavourite ? ' hidden d-none' : '') . '" data-action="remove-favourite" data-course-id="' . $cid . '" aria-controls="favorite-icon-' . $cid . '-' . $uniqid . '">
                                    <span class="me-1 mr-1"><i class="fa fa-star text-warning" aria-hidden="true"></i></span>
                                    Unstar this course
                                </button>
                                <button type="button" class="dropdown-item' . (!$ishidden ? ' hidden d-none' : '') . '" data-action="show-course" data-course-id="' . $cid . '" aria-controls="favorite-icon-' . $cid . '-' . $uniqid . '">
                                    <span class="me-1 mr-1"><i class="fa fa-eye" aria-hidden="true"></i></span>
                                    Restore to view
                                </button>
                                <button type="button" class="dropdown-item' . ($ishidden ? ' hidden d-none' : '') . '" data-action="hide-course" data-course-id="' . $cid . '" aria-controls="favorite-icon-' . $cid . '-' . $uniqid . '">
                                    <span class="me-1 mr-1"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                                    Remove from view
                                </button>
                            </div>
                        </div>

                        ' . ($hasselfenrol ? '
                        <div class="rui-course-card-icons--right">
                            <div class="rui-icon-container" title="Self enrolment">
                                <i class="icon fa fa-key fa-fw" role="img" aria-label="Self enrolment"></i>
                            </div>
                        </div>' : '') . '

                        <a href="' . $courseurl . '" class="rui-course-img-link text-decoration-none border-0 bg-transparent p-0 w-100 text-start d-block" aria-label="' . s($c->fullname) . '">
                            <figure class="rui-course-card-img-top" style="background-image: url(\'' . $imgurl . '\');" alt="' . s($c->fullname) . '">
                                <span class="visually-hidden" style="display:none !important;">' . s($c->fullname) . '</span>
                            </figure>
                        </a>
                    </div>
                    <div class="rui-course-card-body">
                        <div class="d-flex mb-1">
                            <h4 class="rui-course-card-title mb-1">
                                <a href="' . $courseurl . '" class="btn-modal">' . s($c->fullname) . '</a>
                            </h4>
                        </div>
                    </div>
                    ' . ($hasprogress ? '
                    <div class="rui-course-card-footer">
                        <div class="rui-course-card-footer-progress rui-course-card-footer-progress--linear" style="--rui-course-progress: ' . $progress . ';">
                            <div class="rui-course-progress-linear" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . $progress . '" aria-label="Course progress:">
                                <span class="rui-course-progress-linear__bar" style="width: ' . $progress . '%;"></span>
                            </div>
                            <div class="rui-mycourse-progress-label rui-mycourse-progress-label--' . $progress . '">
                                <span class="visually-hidden" style="display:none !important;">Course progress:</span>
                                ' . $progress . '%
                            </div>
                        </div>
                        <div class="rui-course-progress-summary" aria-label="' . strip_tags($completedtext) . '">
                            <span class="rui-course-progress-summary__pill rui-course-progress-summary__pill--done">
                                <i class="fa fa-check-circle fa-solid fa-circle-check" aria-hidden="true"></i>
                                <span class="rui-course-progress-summary__text">' . $completedtext . '</span>
                            </span>
                        </div>
                    </div>' : '') . '
                </div>
            </div>';
        }

        // Build Category Select Options & UI Dropdown Items
        $catoptions = '';
        $catdropdownitems = '';
        $currentcatname = s($coursecat->name);
        foreach ($allcategories as $cat) {
            $caturl = (new \moodle_url('/course/index.php', ['categoryid' => $cat->id]))->out(false);
            $isactive = ($cat->id == $coursecat->id);
            if ($isactive) {
                $currentcatname = s($cat->name);
            }
            $selected = $isactive ? 'selected' : '';
            $activeclass = $isactive ? ' dropdown-item-active active' : '';
            $ariacurrent = $isactive ? ' aria-current="true"' : '';
            $catoptions .= '<option value="' . $caturl . '" ' . $selected . '>' . s($cat->name) . '</option>';
            $catdropdownitems .= '<li><a class="dropdown-item' . $activeclass . '" href="' . $caturl . '"' . $ariacurrent . '>' . s($cat->name) . '</a></li>';
        }

        // Build Filter Toolbar HTML
        $filtertoolbar = '
        <div class="rui-course-filter-content">
            <!-- 1. Search toggle & input -->
            <div class="search-input-group" role="search" data-search-group="1">
                <button type="button" class="search-toggle btn btn-icon btn-outline-secondary" data-search-toggle="1" aria-label="Search">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.25 19.25L15.5 15.5M4.75 11C4.75 7.54822 7.54822 4.75 11 4.75C14.4518 4.75 17.25 7.54822 17.25 11C17.25 14.4518 14.4518 17.25 11 17.25C7.54822 17.25 4.75 14.4518 4.75 11Z"></path>
                    </svg>
                </button>
                <div class="search-input-wrapper">
                    <div class="search-input-icon">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.25 19.25L15.5 15.5M4.75 11C4.75 7.54822 7.54822 4.75 11 4.75C14.4518 4.75 17.25 7.54822 17.25 11C17.25 14.4518 14.4518 17.25 11 17.25C7.54822 17.25 4.75 14.4518 4.75 11Z"></path>
                        </svg>
                    </div>
                    <input type="text" data-region="input" data-action="search" id="filter-search-input" class="search-input w-100 withclear" placeholder="Search" name="search" autocomplete="off">
                    <button type="button" class="search-close me-1 btn btn-icon btn-dark" data-search-close="1" aria-label="Close">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <!-- 2. Dropdowns wrapper -->
            <div class="rui-course-filter-wrapper me-auto">
                <!-- Language Dropdown -->
                <div class="filtering-group" data-group-name="lang">
                    <div class="dropdown" id="filterDropdown-lang" data-filter-dropdown="1">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100" type="button" id="filterDropdownButtonlang" data-filter-toggle="1" aria-haspopup="menu" aria-expanded="false" aria-controls="dropdownMenulang">
                            Language
                        </button>
                        <div class="dropdown-menu" aria-labelledby="filterDropdownButtonlang" id="dropdownMenulang" data-course-filter-dropdown="1" data-filter-group-name="lang">
                            <div class="filtering-search">
                                <input type="text" class="form-control form-control-sm filtering-search-input" placeholder="Search" aria-label="Search" data-filter-search="1">
                            </div>
                            <div class="filtering-items">
                                <div class="filtering-filter filtering-filter--lang dropdown-item d-flex align-items-center w-100" data-filter-name="en">
                                    <span class="filtering-label-wrap flex-grow-1 min-w-0">
                                        <span class="fs-5 lh-1 flex-shrink-0 d-inline-flex align-items-center me-2" aria-hidden="true">🇺🇸</span>
                                        <span class="flex-grow-1 lh-sm"><span class="fw-semibold">English</span><span class="text-muted opacity-75"> - </span><span class="text-muted">EN</span></span>
                                    </span>
                                    <span class="total">' . (isset($counts_lang['en']) ? $counts_lang['en'] : 3) . '</span>
                                </div>
                                <div class="filtering-filter filtering-filter--lang dropdown-item d-flex align-items-center w-100" data-filter-name="pl">
                                    <span class="filtering-label-wrap flex-grow-1 min-w-0">
                                        <span class="fs-5 lh-1 flex-shrink-0 d-inline-flex align-items-center me-2" aria-hidden="true">🇵🇱</span>
                                        <span class="flex-grow-1 lh-sm"><span class="fw-semibold">Polski</span><span class="text-muted opacity-75"> - </span><span class="text-muted">PL</span></span>
                                    </span>
                                    <span class="total">' . (isset($counts_lang['pl']) ? $counts_lang['pl'] : 1) . '</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teachers Dropdown -->
                <div class="filtering-group" data-group-name="teachers">
                    <div class="dropdown" id="filterDropdown-teachers" data-filter-dropdown="1">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100" type="button" id="filterDropdownButtonteachers" data-filter-toggle="1" aria-haspopup="menu" aria-expanded="false" aria-controls="dropdownMenuteachers">
                            Teachers
                        </button>
                        <div class="dropdown-menu" aria-labelledby="filterDropdownButtonteachers" id="dropdownMenuteachers" data-course-filter-dropdown="1" data-filter-group-name="teachers">
                            <div class="filtering-search">
                                <input type="text" class="form-control form-control-sm filtering-search-input" placeholder="Search" aria-label="Search" data-filter-search="1">
                            </div>
                            <div class="filtering-items">
                                <div class="filtering-filter dropdown-item d-flex align-items-center w-100" data-filter-name="Admin User">
                                    <span class="filtering-label-wrap flex-grow-1 min-w-0 d-flex align-items-center">
                                        <span class="avatar rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2" style="width:24px;height:24px;font-size:11px;font-weight:600;">AU</span>
                                        <span class="filtering-label">Admin User</span>
                                    </span>
                                    <span class="total">' . (isset($counts_teachers['Admin User']) ? $counts_teachers['Admin User'] : 4) . '</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Dropdown -->
                <div class="filtering-group course-filter-range course-filter-price" data-group-name="price" data-range-group="1" data-range-min="0" data-range-max="99" data-range-step="1" data-range-currency="zł">
                    <div class="dropdown" id="filterDropdown-price" data-filter-dropdown="1">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100" type="button" id="filterDropdownButtonprice" data-filter-toggle="1" aria-haspopup="menu" aria-expanded="false" aria-controls="dropdownMenuprice">
                            Price
                        </button>
                        <div class="dropdown-menu" aria-labelledby="filterDropdownButtonprice" id="dropdownMenuprice" data-course-filter-dropdown="1" data-filter-group-name="price">
                            <div class="filtering-items">
                                <div class="range-filter-container">
                                    <div class="range-filter-header">
                                        <button type="button" class="range-filter-reset" data-range-reset="1">Reset</button>
                                    </div>
                                    <div class="form-check px-3 py-2 mb-1 range-filter-free-only-wrap">
                                        <input class="form-check-input" type="checkbox" id="rangeFreeOnly-price" data-range-free-only="1" autocomplete="off">
                                        <label class="form-check-label" for="rangeFreeOnly-price">Free only</label>
                                    </div>
                                    <div class="range-filter-histogram" data-range-histogram="1">
                                        <div class="hist-bar active" style="height: 50%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 50%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 50%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 100%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 50%;"></div>
                                    </div>
                                    <div class="range-slider-wrapper">
                                        <div class="slider-progress" data-range-progress="1" style="left: 0%; right: 0%;"></div>
                                        <input type="range" min="0" max="99" step="1" value="0" data-range-slider="min">
                                        <input type="range" min="0" max="99" step="1" value="99" data-range-slider="max">
                                    </div>
                                    <div class="range-filter-inputs-grid">
                                        <div class="range-filter-field">
                                            <label class="visually-hidden" for="range-filter-min-price">From</label>
                                            <div class="range-filter-field-input">
                                                <input type="number" id="range-filter-min-price" min="0" max="99" step="1" value="0" data-filter-range-min="1">
                                                <span class="range-filter-unit">zł</span>
                                            </div>
                                        </div>
                                        <span class="range-filter-sep">-</span>
                                        <div class="range-filter-field">
                                            <label class="visually-hidden" for="range-filter-max-price">To</label>
                                            <div class="range-filter-field-input">
                                                <input type="number" id="range-filter-max-price" min="0" max="99" step="1" value="99" data-filter-range-max="1">
                                                <span class="range-filter-unit">zł</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Duration Dropdown -->
                <div class="filtering-group course-filter-range course-filter-duration" data-group-name="duration" data-range-group="1" data-range-min="2" data-range-max="4" data-range-step="0.1" data-range-currency="h">
                    <div class="dropdown" id="filterDropdown-duration" data-filter-dropdown="1">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100" type="button" id="filterDropdownButtonduration" data-filter-toggle="1" aria-haspopup="menu" aria-expanded="false" aria-controls="dropdownMenuduration">
                            Duration
                        </button>
                        <div class="dropdown-menu" aria-labelledby="filterDropdownButtonduration" id="dropdownMenuduration" data-course-filter-dropdown="1" data-filter-group-name="duration">
                            <div class="filtering-items">
                                <div class="range-filter-container">
                                    <div class="range-filter-header">
                                        <button type="button" class="range-filter-reset" data-range-reset="1">Reset</button>
                                    </div>
                                    <div class="range-filter-histogram" data-range-histogram="1">
                                        <div class="hist-bar active" style="height: 66.67%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 100%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 0%;"></div>
                                        <div class="hist-bar active" style="height: 66.67%;"></div>
                                    </div>
                                    <div class="range-slider-wrapper">
                                        <div class="slider-progress" data-range-progress="1" style="left: 0%; right: 0%;"></div>
                                        <input type="range" min="2" max="4" step="0.1" value="2" data-range-slider="min">
                                        <input type="range" min="2" max="4" step="0.1" value="4" data-range-slider="max">
                                    </div>
                                    <div class="range-filter-inputs-grid">
                                        <div class="range-filter-field">
                                            <label class="visually-hidden" for="range-filter-min-duration">From</label>
                                            <div class="range-filter-field-input">
                                                <input type="number" id="range-filter-min-duration" min="2" max="4" step="0.1" value="2" data-filter-range-min="1">
                                                <span class="range-filter-unit">h</span>
                                            </div>
                                        </div>
                                        <span class="range-filter-sep">-</span>
                                        <div class="range-filter-field">
                                            <label class="visually-hidden" for="range-filter-max-duration">To</label>
                                            <div class="range-filter-field-input">
                                                <input type="number" id="range-filter-max-duration" min="2" max="4" step="0.1" value="4" data-filter-range-max="1">
                                                <span class="range-filter-unit">h</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Level Dropdown -->
                <div class="filtering-group" data-group-name="level">
                    <div class="dropdown" id="filterDropdown-level" data-filter-dropdown="1">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100" type="button" id="filterDropdownButtonlevel" data-filter-toggle="1" aria-haspopup="menu" aria-expanded="false" aria-controls="dropdownMenulevel">
                            Course Level
                        </button>
                        <div class="dropdown-menu" aria-labelledby="filterDropdownButtonlevel" id="dropdownMenulevel" data-course-filter-dropdown="1" data-filter-group-name="level">
                            <div class="filtering-search">
                                <input type="text" class="form-control form-control-sm filtering-search-input" placeholder="Search" aria-label="Search" data-filter-search="1">
                            </div>
                            <div class="filtering-items">
                                <div class="filtering-filter dropdown-item d-flex align-items-center w-100" data-filter-name="Beginner">
                                    <span class="filtering-label flex-grow-1">Beginner</span>
                                    <span class="total">' . (isset($counts_level['Beginner']) ? $counts_level['Beginner'] : 4) . '</span>
                                </div>
                                <div class="filtering-filter dropdown-item d-flex align-items-center w-100" data-filter-name="Intermediate">
                                    <span class="filtering-label flex-grow-1">Intermediate</span>
                                    <span class="total">' . (isset($counts_level['Intermediate']) ? $counts_level['Intermediate'] : 1) . '</span>
                                </div>
                                <div class="filtering-filter dropdown-item d-flex align-items-center w-100" data-filter-name="Expert">
                                    <span class="filtering-label flex-grow-1">Expert</span>
                                    <span class="total">' . (isset($counts_level['Expert']) ? $counts_level['Expert'] : 2) . '</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Reset Filters Button -->
            <button type="button" id="resetFiltersBtn" class="btn btn-sm btn-warning my-0 ps-2 d-none" disabled="">
                <i class="fa fa-times-circle me-2" aria-hidden="true"></i>Clear all
            </button>

            <!-- 4. Sort buttons -->
            <div class="ms-auto d-flex">
                <button type="button" id="sort-az-btn" class="btn btn-icon btn-outline-secondary ms-md-2 my-0" aria-label="Sort ascending" title="Sort A-Z">
                    <i class="fa fa-sort-alpha-asc" aria-hidden="true"></i>
                </button>
                <button type="button" id="sort-za-btn" class="btn btn-icon btn-outline-secondary ms-1 my-0" aria-label="Sort descending" title="Sort Z-A">
                    <i class="fa fa-sort-alpha-desc" aria-hidden="true"></i>
                </button>
            </div>
        </div>';

        // Build Tertiary Navigation (Category Select Bar with Custom Dropdown UI)
        $tertiarynav = '
        <div class="tertiary-navigation d-inline-flex flex-wrap w-100 align-items-center mt-0 mb-3" id="action_bar">
            <div class="navitem">
                <div class="dropdown select-menu category-selector-dropdown position-relative">
                    <button id="categorySelectDropdownBtn" type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" data-display="static" data-bs-display="static" aria-haspopup="true" aria-expanded="false">
                        <span data-active-item-text>' . $currentcatname . '</span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="categorySelectDropdownBtn">
                        ' . $catdropdownitems . '
                    </ul>
                </div>
            </div>
        </div>';

        // Course Card Deck Container
        $deckhtml = '
        <div class="course_category_tree clearfix">
            <div class="content">
                <div class="courses category-browse category-browse-' . $coursecat->id . '">
                    <div id="filtered-results" class="rui-course-card-deck mt-2" role="list">
                        ' . $cardshtml . '
                    </div>
                </div>
            </div>
        </div>';

        // Robust, interactive Client-Side Script
        $script = '
        <script>
        (function() {
            function initSpaceCourseFilters() {
                var searchGroup = document.querySelector(\'[data-search-group="1"]\');
                var searchInput = document.getElementById("filter-search-input");
                var searchToggle = document.querySelector("[data-search-toggle]");
                var searchClose = document.querySelector("[data-search-close]");
                var resetBtn = document.getElementById("resetFiltersBtn");
                var sortAzBtn = document.getElementById("sort-az-btn");
                var sortZaBtn = document.getElementById("sort-za-btn");
                var resultsDeck = document.getElementById("filtered-results");

                if (!resultsDeck) return;
                var cards = Array.from(resultsDeck.querySelectorAll(".filtering-item"));

                // Active filter state
                var activeFilters = {
                    search: "",
                    lang: new Set(),
                    teachers: new Set(),
                    level: new Set(),
                    priceFreeOnly: false,
                    priceMin: 0,
                    priceMax: 99,
                    durationMin: 2.0,
                    durationMax: 4.0
                };

                // Apply filtering to all cards
                function applyFilters() {
                    var visibleCount = 0;
                    var hasAnyFilter = false;

                    if (activeFilters.search !== "") hasAnyFilter = true;
                    if (activeFilters.lang.size > 0) hasAnyFilter = true;
                    if (activeFilters.teachers.size > 0) hasAnyFilter = true;
                    if (activeFilters.level.size > 0) hasAnyFilter = true;
                    if (activeFilters.priceFreeOnly) hasAnyFilter = true;
                    if (activeFilters.priceMin > 0 || activeFilters.priceMax < 99) hasAnyFilter = true;
                    if (activeFilters.durationMin > 2.0 || activeFilters.durationMax < 4.0) hasAnyFilter = true;

                    cards.forEach(function(card) {
                        var title = (card.dataset.filterTitle || "").toLowerCase();
                        var lang = (card.dataset.filterLang || "").toLowerCase();
                        var teacher = card.dataset.filterTeachers || "";
                        var level = card.dataset.filterLevel || "";
                        var price = parseFloat(card.dataset.filterPrice) || 0;
                        var duration = parseFloat(card.dataset.filterDurationval) || 2.0;

                        var match = true;

                        // Search
                        if (activeFilters.search && !title.includes(activeFilters.search)) {
                            match = false;
                        }

                        // Lang
                        if (match && activeFilters.lang.size > 0 && !activeFilters.lang.has(lang)) {
                            match = false;
                        }

                        // Teachers
                        if (match && activeFilters.teachers.size > 0 && !activeFilters.teachers.has(teacher)) {
                            match = false;
                        }

                        // Level
                        if (match && activeFilters.level.size > 0 && !activeFilters.level.has(level)) {
                            match = false;
                        }

                        // Price
                        if (match) {
                            if (activeFilters.priceFreeOnly && price > 0) {
                                match = false;
                            } else if (price < activeFilters.priceMin || price > activeFilters.priceMax) {
                                match = false;
                            }
                        }

                        // Duration
                        if (match && (duration < activeFilters.durationMin || duration > activeFilters.durationMax)) {
                            match = false;
                        }

                        if (match) {
                            card.classList.remove("filtered");
                            card.style.display = "";
                            visibleCount++;
                        } else {
                            card.classList.add("filtered");
                            card.style.display = "none";
                        }
                    });

                    // Update Reset button
                    if (resetBtn) {
                        if (hasAnyFilter) {
                            resetBtn.classList.remove("d-none");
                            resetBtn.disabled = false;
                        } else {
                            resetBtn.classList.add("d-none");
                            resetBtn.disabled = true;
                        }
                    }

                    // Update dropdown button styles (secondary when active)
                    var langBtn = document.getElementById("filterDropdownButtonlang");
                    if (langBtn) {
                        langBtn.classList.toggle("btn-secondary", activeFilters.lang.size > 0);
                        langBtn.classList.toggle("btn-outline-secondary", activeFilters.lang.size === 0);
                    }
                    var teachBtn = document.getElementById("filterDropdownButtonteachers");
                    if (teachBtn) {
                        teachBtn.classList.toggle("btn-secondary", activeFilters.teachers.size > 0);
                        teachBtn.classList.toggle("btn-outline-secondary", activeFilters.teachers.size === 0);
                    }
                    var lvlBtn = document.getElementById("filterDropdownButtonlevel");
                    if (lvlBtn) {
                        lvlBtn.classList.toggle("btn-secondary", activeFilters.level.size > 0);
                        lvlBtn.classList.toggle("btn-outline-secondary", activeFilters.level.size === 0);
                    }
                    var priceBtn = document.getElementById("filterDropdownButtonprice");
                    if (priceBtn) {
                        var priceActive = activeFilters.priceFreeOnly || activeFilters.priceMin > 0 || activeFilters.priceMax < 99;
                        priceBtn.classList.toggle("btn-secondary", priceActive);
                        priceBtn.classList.toggle("btn-outline-secondary", !priceActive);
                    }
                    var durBtn = document.getElementById("filterDropdownButtonduration");
                    if (durBtn) {
                        var durActive = activeFilters.durationMin > 2.0 || activeFilters.durationMax < 4.0;
                        durBtn.classList.toggle("btn-secondary", durActive);
                        durBtn.classList.toggle("btn-outline-secondary", !durActive);
                    }
                }

                // 1. Search toggle & input handlers
                if (searchToggle && searchGroup) {
                    searchToggle.addEventListener("click", function(e) {
                        e.preventDefault();
                        searchGroup.classList.add("is-open");
                        if (searchInput) searchInput.focus();
                    });
                }
                if (searchClose && searchGroup) {
                    searchClose.addEventListener("click", function(e) {
                        e.preventDefault();
                        searchGroup.classList.remove("is-open");
                        if (searchInput) {
                            searchInput.value = "";
                            activeFilters.search = "";
                            applyFilters();
                        }
                    });
                }
                if (searchInput) {
                    var debounceTimer = null;
                    searchInput.addEventListener("input", function() {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(function() {
                            activeFilters.search = (searchInput.value || "").trim().toLowerCase();
                            applyFilters();
                        }, 150);
                    });
                }

                // 2. Dropdowns open/close logic
                var dropdownButtons = document.querySelectorAll("[data-filter-toggle=\"1\"]");
                dropdownButtons.forEach(function(btn) {
                    btn.addEventListener("click", function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var parent = btn.closest(".dropdown");
                        var menu = parent ? parent.querySelector(".dropdown-menu") : null;
                        if (!menu) return;
                        var isShown = menu.classList.contains("show");

                        // Close any other open filter dropdowns
                        document.querySelectorAll(".filtering-group .dropdown-menu.show").forEach(function(m) {
                            m.classList.remove("show");
                            var b = m.closest(".dropdown") ? m.closest(".dropdown").querySelector("button") : null;
                            if (b) b.setAttribute("aria-expanded", "false");
                        });

                        if (!isShown) {
                            menu.classList.add("show");
                            btn.setAttribute("aria-expanded", "true");
                        }
                    });
                });

                // Close dropdowns on click outside
                document.addEventListener("click", function(e) {
                    if (!e.target.closest(".filtering-group")) {
                        document.querySelectorAll(".filtering-group .dropdown-menu.show").forEach(function(m) {
                            m.classList.remove("show");
                            var b = m.closest(".dropdown") ? m.closest(".dropdown").querySelector("button") : null;
                            if (b) b.setAttribute("aria-expanded", "false");
                        });
                    }
                });

                // 3. Search inside dropdown
                document.querySelectorAll("[data-filter-search=\"1\"]").forEach(function(sinput) {
                    sinput.addEventListener("input", function() {
                        var q = (sinput.value || "").trim().toLowerCase();
                        var menu = sinput.closest(".dropdown-menu");
                        if (!menu) return;
                        menu.querySelectorAll(".filtering-filter").forEach(function(item) {
                            var text = (item.textContent || "").toLowerCase();
                            item.style.display = (q === "" || text.includes(q)) ? "flex" : "none";
                        });
                    });
                });

                // 4. Click filter items (Lang, Teachers, Level)
                document.querySelectorAll(".filtering-filter").forEach(function(fitem) {
                    fitem.addEventListener("click", function(e) {
                        e.preventDefault();
                        var group = fitem.closest(".filtering-group");
                        if (!group) return;
                        var groupName = group.dataset.groupName;
                        var filterName = fitem.dataset.filterName;
                        if (!groupName || !filterName) return;

                        var isChecked = fitem.classList.contains("checked");
                        fitem.classList.toggle("checked", !isChecked);

                        if (activeFilters[groupName]) {
                            if (!isChecked) {
                                activeFilters[groupName].add(filterName);
                            } else {
                                activeFilters[groupName].delete(filterName);
                            }
                        }
                        applyFilters();
                    });
                });

                // 5. Price Range & Free Only
                var priceMinInput = document.getElementById("range-filter-min-price");
                var priceMaxInput = document.getElementById("range-filter-max-price");
                var priceFreeOnly = document.getElementById("rangeFreeOnly-price");
                var priceGroup = document.querySelector(".course-filter-price");

                function syncPriceSlider(minVal, maxVal) {
                    if (minVal > maxVal) { var tmp = minVal; minVal = maxVal; maxVal = tmp; }
                    activeFilters.priceMin = minVal;
                    activeFilters.priceMax = maxVal;
                    if (priceMinInput) priceMinInput.value = minVal;
                    if (priceMaxInput) priceMaxInput.value = maxVal;

                    if (priceGroup) {
                        var minS = priceGroup.querySelector(\'[data-range-slider="min"]\');
                        var maxS = priceGroup.querySelector(\'[data-range-slider="max"]\');
                        var prog = priceGroup.querySelector("[data-range-progress]");
                        if (minS) minS.value = minVal;
                        if (maxS) maxS.value = maxVal;
                        if (prog) {
                            var pMin = (minVal / 99) * 100;
                            var pMax = 100 - (maxVal / 99) * 100;
                            prog.style.left = pMin + "%";
                            prog.style.right = pMax + "%";
                        }
                        var histBars = priceGroup.querySelectorAll(".hist-bar");
                        histBars.forEach(function(bar, idx) {
                            var pct = (idx / (histBars.length - 1)) * 100;
                            bar.classList.toggle("active", pct >= pMin && pct <= (100 - pMax));
                        });
                    }
                    applyFilters();
                }

                if (priceGroup) {
                    var pMinS = priceGroup.querySelector(\'[data-range-slider="min"]\');
                    var pMaxS = priceGroup.querySelector(\'[data-range-slider="max"]\');
                    if (pMinS) pMinS.addEventListener("input", function() {
                        if (priceFreeOnly) priceFreeOnly.checked = false;
                        activeFilters.priceFreeOnly = false;
                        syncPriceSlider(parseFloat(pMinS.value) || 0, parseFloat(pMaxS ? pMaxS.value : 99) || 99);
                    });
                    if (pMaxS) pMaxS.addEventListener("input", function() {
                        if (priceFreeOnly) priceFreeOnly.checked = false;
                        activeFilters.priceFreeOnly = false;
                        syncPriceSlider(parseFloat(pMinS ? pMinS.value : 0) || 0, parseFloat(pMaxS.value) || 99);
                    });
                    if (priceMinInput) priceMinInput.addEventListener("input", function() {
                        if (priceFreeOnly) priceFreeOnly.checked = false;
                        activeFilters.priceFreeOnly = false;
                        syncPriceSlider(parseFloat(priceMinInput.value) || 0, activeFilters.priceMax);
                    });
                    if (priceMaxInput) priceMaxInput.addEventListener("input", function() {
                        if (priceFreeOnly) priceFreeOnly.checked = false;
                        activeFilters.priceFreeOnly = false;
                        syncPriceSlider(activeFilters.priceMin, parseFloat(priceMaxInput.value) || 99);
                    });
                    if (priceFreeOnly) priceFreeOnly.addEventListener("change", function() {
                        activeFilters.priceFreeOnly = priceFreeOnly.checked;
                        applyFilters();
                    });
                    var priceReset = priceGroup.querySelector("[data-range-reset]");
                    if (priceReset) priceReset.addEventListener("click", function(e) {
                        e.preventDefault();
                        if (priceFreeOnly) priceFreeOnly.checked = false;
                        activeFilters.priceFreeOnly = false;
                        syncPriceSlider(0, 99);
                    });
                }

                // 6. Duration Range
                var durGroup = document.querySelector(".course-filter-duration");
                var durMinInput = document.getElementById("range-filter-min-duration");
                var durMaxInput = document.getElementById("range-filter-max-duration");

                function syncDurationSlider(minVal, maxVal) {
                    if (minVal > maxVal) { var tmp = minVal; minVal = maxVal; maxVal = tmp; }
                    activeFilters.durationMin = minVal;
                    activeFilters.durationMax = maxVal;
                    if (durMinInput) durMinInput.value = minVal;
                    if (durMaxInput) durMaxInput.value = maxVal;

                    if (durGroup) {
                        var minS = durGroup.querySelector(\'[data-range-slider="min"]\');
                        var maxS = durGroup.querySelector(\'[data-range-slider="max"]\');
                        var prog = durGroup.querySelector("[data-range-progress]");
                        if (minS) minS.value = minVal;
                        if (maxS) maxS.value = maxVal;
                        if (prog) {
                            var pMin = ((minVal - 2.0) / 2.0) * 100;
                            var pMax = 100 - ((maxVal - 2.0) / 2.0) * 100;
                            prog.style.left = pMin + "%";
                            prog.style.right = pMax + "%";
                        }
                        var histBars = durGroup.querySelectorAll(".hist-bar");
                        histBars.forEach(function(bar, idx) {
                            var pct = (idx / (histBars.length - 1)) * 100;
                            bar.classList.toggle("active", pct >= pMin && pct <= (100 - pMax));
                        });
                    }
                    applyFilters();
                }

                if (durGroup) {
                    var dMinS = durGroup.querySelector(\'[data-range-slider="min"]\');
                    var dMaxS = durGroup.querySelector(\'[data-range-slider="max"]\');
                    if (dMinS) dMinS.addEventListener("input", function() {
                        syncDurationSlider(parseFloat(dMinS.value) || 2.0, parseFloat(dMaxS ? dMaxS.value : 4.0) || 4.0);
                    });
                    if (dMaxS) dMaxS.addEventListener("input", function() {
                        syncDurationSlider(parseFloat(dMinS ? dMinS.value : 2.0) || 2.0, parseFloat(dMaxS.value) || 4.0);
                    });
                    if (durMinInput) durMinInput.addEventListener("input", function() {
                        syncDurationSlider(parseFloat(durMinInput.value) || 2.0, activeFilters.durationMax);
                    });
                    if (durMaxInput) durMaxInput.addEventListener("input", function() {
                        syncDurationSlider(activeFilters.durationMin, parseFloat(durMaxInput.value) || 4.0);
                    });
                    var durReset = durGroup.querySelector("[data-range-reset]");
                    if (durReset) durReset.addEventListener("click", function(e) {
                        e.preventDefault();
                        syncDurationSlider(2.0, 4.0);
                    });
                }

                // 7. Sort A-Z and Z-A
                if (sortAzBtn) {
                    sortAzBtn.addEventListener("click", function(e) {
                        e.preventDefault();
                        sortAzBtn.classList.add("active", "btn-secondary");
                        sortAzBtn.classList.remove("btn-outline-secondary");
                        if (sortZaBtn) {
                            sortZaBtn.classList.remove("active", "btn-secondary");
                            sortZaBtn.classList.add("btn-outline-secondary");
                        }
                        cards.sort(function(a, b) {
                            var ta = (a.dataset.filterTitle || "").trim().toLowerCase();
                            var tb = (b.dataset.filterTitle || "").trim().toLowerCase();
                            return ta.localeCompare(tb);
                        });
                        cards.forEach(function(card) {
                            resultsDeck.appendChild(card);
                        });
                    });
                }

                if (sortZaBtn) {
                    sortZaBtn.addEventListener("click", function(e) {
                        e.preventDefault();
                        sortZaBtn.classList.add("active", "btn-secondary");
                        sortZaBtn.classList.remove("btn-outline-secondary");
                        if (sortAzBtn) {
                            sortAzBtn.classList.remove("active", "btn-secondary");
                            sortAzBtn.classList.add("btn-outline-secondary");
                        }
                        cards.sort(function(a, b) {
                            var ta = (a.dataset.filterTitle || "").trim().toLowerCase();
                            var tb = (b.dataset.filterTitle || "").trim().toLowerCase();
                            return tb.localeCompare(ta);
                        });
                        cards.forEach(function(card) {
                            resultsDeck.appendChild(card);
                        });
                    });
                }

                // 8. Reset All Filters
                if (resetBtn) {
                    resetBtn.addEventListener("click", function(e) {
                        e.preventDefault();
                        // Reset search
                        if (searchInput) searchInput.value = "";
                        if (searchGroup) searchGroup.classList.remove("is-open");
                        activeFilters.search = "";

                        // Reset filter items
                        document.querySelectorAll(".filtering-filter.checked").forEach(function(item) {
                            item.classList.remove("checked");
                        });
                        activeFilters.lang.clear();
                        activeFilters.teachers.clear();
                        activeFilters.level.clear();

                        // Reset ranges
                        if (priceFreeOnly) priceFreeOnly.checked = false;
                        activeFilters.priceFreeOnly = false;
                        syncPriceSlider(0, 99);
                        syncDurationSlider(2.0, 4.0);

                        applyFilters();
                    });
                }

                // 9. Course Card Action Menu Dropdown & Favourite logic (matching My Courses page)
                document.addEventListener("click", function(e) {
                    var menuBtn = e.target.closest(".rui-coursemenu-btn");
                    if (menuBtn) {
                        e.preventDefault();
                        e.stopPropagation();
                        var dropdown = menuBtn.closest(".rui-course-dropdown");
                        var menu = dropdown ? dropdown.querySelector(".dropdown-menu") : null;
                        var card = menuBtn.closest(".rui-course-card");
                        if (!menu) return;

                        var isShown = menu.classList.contains("show");

                        // Close all open course card dropdowns
                        document.querySelectorAll(".rui-course-card .dropdown-menu.show").forEach(function(m) {
                            m.classList.remove("show");
                            var b = m.closest(".dropdown") ? m.closest(".dropdown").querySelector("button") : null;
                            if (b) b.setAttribute("aria-expanded", "false");
                        });
                        document.querySelectorAll(".rui-course-card.dropdown-open").forEach(function(c) {
                            c.classList.remove("dropdown-open");
                        });

                        if (!isShown) {
                            menu.classList.add("show");
                            menuBtn.setAttribute("aria-expanded", "true");
                            if (card) card.classList.add("dropdown-open");
                        }
                        return;
                    }

                    // Click outside course dropdown closes it
                    if (!e.target.closest(".rui-course-dropdown")) {
                        document.querySelectorAll(".rui-course-card .dropdown-menu.show").forEach(function(m) {
                            m.classList.remove("show");
                            var b = m.closest(".dropdown") ? m.closest(".dropdown").querySelector("button") : null;
                            if (b) b.setAttribute("aria-expanded", "false");
                        });
                        document.querySelectorAll(".rui-course-card.dropdown-open").forEach(function(c) {
                            c.classList.remove("dropdown-open");
                        });
                    }
                });

                // Toggle favourite handler (star button)
                document.addEventListener("click", function(e) {
                    var favBtn = e.target.closest(\'[data-action="toggle-favourite"]\');
                    if (!favBtn) return;
                    e.preventDefault();
                    e.stopPropagation();

                    var courseId = parseInt(favBtn.dataset.courseId, 10);
                    var isFav = favBtn.classList.contains("is-favourite");
                    var newFav = !isFav;

                    // Optimistic UI update
                    favBtn.classList.toggle("is-favourite", newFav);
                    var isFavSpan = favBtn.querySelector(\'[data-region="is-favourite"]\');
                    var notFavSpan = favBtn.querySelector(\'[data-region="not-favourite"]\');
                    if (newFav) {
                        if (isFavSpan) isFavSpan.classList.remove("hidden", "d-none");
                        if (notFavSpan) notFavSpan.classList.add("hidden", "d-none");
                        favBtn.setAttribute("title", "Unstar this course");
                    } else {
                        if (isFavSpan) isFavSpan.classList.add("hidden", "d-none");
                        if (notFavSpan) notFavSpan.classList.remove("hidden", "d-none");
                        favBtn.setAttribute("title", "Star this course");
                    }

                    // Sync dropdown menu items
                    var card = favBtn.closest(\'[data-region="course-content"]\') || favBtn.closest(".rui-course-card");
                    if (card) {
                        var addFavItem = card.querySelector(\'[data-action="add-favourite"]\');
                        var remFavItem = card.querySelector(\'[data-action="remove-favourite"]\');
                        if (addFavItem) {
                            addFavItem.classList.toggle("hidden", newFav);
                            addFavItem.classList.toggle("d-none", newFav);
                        }
                        if (remFavItem) {
                            remFavItem.classList.toggle("hidden", !newFav);
                            remFavItem.classList.toggle("d-none", !newFav);
                        }
                    }

                    // Call Moodle AJAX
                    if (window.M && window.M.cfg && window.require) {
                        window.require(["core/ajax", "core/notification"], function(ajax, notification) {
                            ajax.call([{
                                methodname: "core_course_set_favourite_courses",
                                args: { courses: [{ id: courseId, favourite: newFav }] }
                            }])[0].catch(function(err) {
                                // Revert on failure
                                favBtn.classList.toggle("is-favourite", isFav);
                                if (isFav) {
                                    if (isFavSpan) isFavSpan.classList.remove("hidden", "d-none");
                                    if (notFavSpan) notFavSpan.classList.add("hidden", "d-none");
                                } else {
                                    if (isFavSpan) isFavSpan.classList.add("hidden", "d-none");
                                    if (notFavSpan) notFavSpan.classList.remove("hidden", "d-none");
                                }
                                notification.exception(err);
                            });
                        });
                    }
                });

                // Dropdown menu "Star this course" / "Unstar this course" items
                document.addEventListener("click", function(e) {
                    var favItem = e.target.closest(\'[data-action="add-favourite"], [data-action="remove-favourite"]\');
                    if (!favItem) return;
                    e.preventDefault();
                    e.stopPropagation();

                    var card = favItem.closest(\'[data-region="course-content"]\') || favItem.closest(".rui-course-card");
                    if (card) {
                        var btn = card.querySelector(\'[data-action="toggle-favourite"]\');
                        if (btn) btn.click();
                        // Close dropdown
                        var menu = card.querySelector(".dropdown-menu.show");
                        if (menu) menu.classList.remove("show");
                        card.classList.remove("dropdown-open");
                    }
                });

                // Dropdown menu "Hide from view" / "Restore to view"
                document.addEventListener("click", function(e) {
                    var hideItem = e.target.closest(\'[data-action="hide-course"], [data-action="show-course"]\');
                    if (!hideItem) return;
                    e.preventDefault();
                    e.stopPropagation();

                    var isHide = hideItem.dataset.action === "hide-course";
                    var courseId = parseInt(hideItem.dataset.courseId, 10);
                    var card = hideItem.closest(\'[data-region="course-content"]\') || hideItem.closest(".rui-course-card");
                    if (card) {
                        var hideBtn = card.querySelector(\'[data-action="hide-course"]\');
                        var showBtn = card.querySelector(\'[data-action="show-course"]\');
                        if (hideBtn) {
                            hideBtn.classList.toggle("hidden", isHide);
                            hideBtn.classList.toggle("d-none", isHide);
                        }
                        if (showBtn) {
                            showBtn.classList.toggle("hidden", !isHide);
                            showBtn.classList.toggle("d-none", !isHide);
                        }
                        // Close dropdown
                        var menu = card.querySelector(".dropdown-menu.show");
                        if (menu) menu.classList.remove("show");
                        card.classList.remove("dropdown-open");
                    }

                    if (window.M && window.M.cfg && window.require) {
                        window.require(["core/ajax", "core/notification"], function(ajax, notification) {
                            ajax.call([{
                                methodname: "core_user_set_user_preferences",
                                args: {
                                    preferences: [{
                                        name: "block_myoverview_hidden_course_" + courseId,
                                        value: isHide ? "1" : null
                                    }]
                                }
                            }])[0].catch(function(err) {
                                notification.exception(err);
                            });
                        });
                    }
                });
            }

            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", initSpaceCourseFilters);
            } else {
                initSpaceCourseFilters();
            }
        })();
        </script>';

        return $filtertoolbar . $tertiarynav . $deckhtml . $script;
    }

    /**
     * Overrides frontpage_part to avoid duplicate titles when custom card grid header is rendered.
     *
     * @param string $skipdivid
     * @param string $contentsdivid
     * @param string $header
     * @param string $contents
     * @return string
     */
    protected function frontpage_part($skipdivid, $contentsdivid, $header, $contents) {
        if (strval($contents) === '') {
            return '';
        }
        if ($this->page->user_is_editing()) {
            return parent::frontpage_part($skipdivid, $contentsdivid, $header, $contents);
        }
        if (strpos($contents, 'oprime-frontpage-courses-wrapper') !== false) {
            $output = \html_writer::link('#' . $skipdivid,
                get_string('skipa', 'access', \core_text::strtolower(strip_tags($header))),
                ['class' => 'skip-block skip aabtn']);
            $output .= \html_writer::start_tag('div', ['id' => $contentsdivid]);
            $output .= $contents;
            $output .= \html_writer::end_tag('div');
            $output .= \html_writer::tag('span', '', ['class' => 'skip-block-to', 'id' => $skipdivid]);
            return $output;
        }
        return parent::frontpage_part($skipdivid, $contentsdivid, $header, $contents);
    }

    /**
     * Renders modern, professional Available Courses card grid for Site Home.
     *
     * @return string
     */
    public function frontpage_available_courses() {
        global $CFG, $DB, $USER, $OUTPUT;

        // When user is in editing mode, fallback to core renderer for full inline editing tools.
        if ($this->page->user_is_editing()) {
            return parent::frontpage_available_courses();
        }

        $chelper = new \coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->
                set_courses_display_options([
                    'recursive' => true,
                    'limit' => $CFG->frontpagecourselimit,
                    'viewmoreurl' => new \moodle_url('/course/index.php'),
                    'viewmoretext' => new \lang_string('fulllistofcourses')
                ]);

        $courses = \core_course_category::top()->get_courses($chelper->get_courses_display_options());
        $totalcount = \core_course_category::top()->get_courses_count($chelper->get_courses_display_options());

        if (!$totalcount) {
            /** @var \context $systemcontext */
            $systemcontext = \context_system::instance();
            if (has_capability('moodle/course:create', $systemcontext)) {
                return $this->add_new_course_button();
            }
            return '';
        }

        // Section Header
        $html = '<div class="oprime-frontpage-courses-wrapper my-4">';
        $html .= '  <div class="oprime-fp-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-2 border-bottom">';
        $html .= '    <div>';
        $html .= '      <div class="d-flex align-items-center gap-2 mb-1">';
        $html .= '        <h2 class="oprime-fp-title mb-0 font-weight-bold" style="font-size: 1.6rem; color: #0f172a; letter-spacing: -0.02em;">' . get_string('availablecourses') . '</h2>';
        $html .= '        <span class="badge badge-pill badge-primary ml-2 px-2 py-1" style="background: #eff6ff; color: #2563eb; font-size: 12px; font-weight: 600; border: 1px solid #bfdbfe;">' . $totalcount . ' ' . ($totalcount === 1 ? 'Course' : 'Courses') . '</span>';
        $html .= '      </div>';
        $html .= '      <p class="text-muted mb-0" style="font-size: 0.92rem;">Explore industry-aligned courses, practical engineering projects, and self-paced certifications.</p>';
        $html .= '    </div>';
        $html .= '    <div class="mt-3 mt-md-0">';
        $html .= '      <a href="' . (new \moodle_url('/course/index.php'))->out(false) . '" class="btn btn-outline-secondary btn-sm px-3 py-2 font-weight-bold" style="border-radius: 8px; font-size: 13px; color: #334155; border-color: #cbd5e1; background: #ffffff;">';
        $html .= '        <span>' . get_string('fulllistofcourses') . '</span>';
        $html .= '        <svg width="14" height="14" class="ml-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path></svg>';
        $html .= '      </a>';
        $html .= '    </div>';
        $html .= '  </div>';

        // Course Card Grid
        $html .= '  <div class="oprime-fp-course-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">';

        foreach ($courses as $c) {
            $cid = $c->id;
            $courseurl = new \moodle_url('/course/view.php', ['id' => $cid]);
            $coursename = format_string($c->fullname, true, ['context' => \context_course::instance($cid)]);

            // 1. Cover Image
            $imgurl = '';
            $files = $c->get_course_overviewfiles();
            if (!empty($files)) {
                $f = reset($files);
                $imgurl = \moodle_url::make_pluginfile_url($f->get_contextid(), $f->get_component(), $f->get_filearea(), null, $f->get_filepath(), $f->get_filename())->out(false);
            }
            if (empty($imgurl)) {
                try {
                    $imgurl = $this->output->get_generated_image_for_id($cid);
                } catch (\Exception $e) {
                    $imgurl = '';
                }
            }
            if (empty($imgurl)) {
                $coveridx = (($cid - 1) % 9) + 1;
                $imgurl = $this->output->image_url("courses/course_cover_{$coveridx}", 'theme_oprime')->out(false);
            }

            // 2. Category
            $catname = '';
            if (!empty($c->category)) {
                $category = \core_course_category::get($c->category, IGNORE_MISSING);
                if ($category) {
                    $catname = $category->get_formatted_name();
                }
            }

            // 3. Level customfield
            $level = '';
            foreach ($c->get_custom_fields() as $cf) {
                if (stripos($cf->get_field()->get('shortname'), 'level') !== false || stripos($cf->get_field()->get('name'), 'level') !== false) {
                    $level = $cf->export_value();
                    break;
                }
            }

            // 4. Teacher
            $teachername = '';
            foreach ($c->get_course_contacts() as $contact) {
                if (!empty($contact['user'])) {
                    $teachername = fullname($contact['user']);
                    break;
                }
            }
            if (empty($teachername)) {
                $teachername = 'Instructor';
            }

            // 5. Summary
            $summary = strip_tags(content_to_text($c->summary, $c->summaryformat));
            if (mb_strlen($summary) > 130) {
                $summary = mb_substr($summary, 0, 127) . '...';
            }
            if (empty($summary)) {
                $summary = 'Learn essential skills and real-world competencies in ' . $coursename . '.';
            }

            // Card HTML
            $html .= '<div class="oprime-course-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04); position: relative;">';
            
            // Image area
            $html .= '  <div class="oprime-card-media" style="position: relative; height: 180px; overflow: hidden; background: #f1f5f9;">';
            $html .= '    <a href="' . $courseurl->out(false) . '" tabindex="-1" style="display: block; width: 100%; height: 100%;">';
            $html .= '      <img src="' . s($imgurl) . '" alt="' . s($coursename) . '" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" loading="lazy">';
            $html .= '    </a>';
            
            // Badges over image
            if (!empty($catname)) {
                $html .= '  <span class="oprime-card-badge-cat" style="position: absolute; top: 12px; left: 12px; z-index: 2; background: rgba(15, 23, 42, 0.78); backdrop-filter: blur(8px); color: #ffffff; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; letter-spacing: 0.02em; text-transform: uppercase;">' . s($catname) . '</span>';
            }
            if (!empty($level)) {
                $html .= '  <span class="oprime-card-badge-level" style="position: absolute; top: 12px; right: 12px; z-index: 2; background: rgba(37, 99, 235, 0.88); backdrop-filter: blur(8px); color: #ffffff; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 6px;">' . s($level) . '</span>';
            }
            $html .= '  </div>';

            // Body
            $html .= '  <div class="oprime-card-body" style="padding: 18px 20px; flex: 1 1 auto; display: flex; flex-direction: column;">';
            $html .= '    <h3 class="oprime-card-title mb-2" style="font-size: 15.5px; font-weight: 700; line-height: 1.35; margin: 0;">';
            $html .= '      <a href="' . $courseurl->out(false) . '" style="color: #0f172a; text-decoration: none; transition: color 0.15s ease; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">' . s($coursename) . '</a>';
            $html .= '    </h3>';
            $html .= '    <p class="oprime-card-desc text-muted mb-3" style="font-size: 13px; line-height: 1.5; color: #64748b; flex-grow: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin: 0 0 14px 0;">' . s($summary) . '</p>';
            
            // Instructor row
            $html .= '    <div class="oprime-card-teacher d-flex align-items-center mt-auto pt-2" style="border-top: 1px solid #f1f5f9;">';
            $html .= '      <div class="oprime-teacher-avatar mr-2" style="width: 24px; height: 24px; border-radius: 50%; background: #e0e7ff; color: #4338ca; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;">' . strtoupper(substr($teachername, 0, 1)) . '</div>';
            $html .= '      <span style="font-size: 12px; color: #64748b; font-weight: 500;">' . s($teachername) . '</span>';
            $html .= '    </div>';
            $html .= '  </div>';

            // Footer
            $html .= '  <div class="oprime-card-footer" style="padding: 12px 20px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">';
            $html .= '    <span class="badge" style="background: #ecfdf5; color: #047857; font-size: 11.5px; font-weight: 600; padding: 4px 8px; border-radius: 5px; border: 1px solid #a7f3d0;">Self-paced</span>';
            $html .= '    <a href="' . $courseurl->out(false) . '" class="oprime-card-cta d-inline-flex align-items-center font-weight-bold" style="font-size: 13px; color: #2563eb; text-decoration: none; transition: all 0.15s ease;">';
            $html .= '      <span>View Course</span>';
            $html .= '      <svg width="15" height="15" class="ml-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path></svg>';
            $html .= '    </a>';
            $html .= '  </div>';

            $html .= '</div>';
        }

        $html .= '  </div>';
        $html .= '</div>';

        return $html;
    }
}
