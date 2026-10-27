<?php
define('CLI_SCRIPT', true);

require_once(__DIR__ . '/config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/completion/criteria/completion_criteria_activity.php');
require_once($CFG->dirroot . '/completion/completion_completion.php');
require_once($CFG->dirroot . '/completion/completion_criteria_completion.php');
require_once($CFG->dirroot . '/mod/assign/lib.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->libdir . '/enrollib.php');
require_once($CFG->libdir . '/filelib.php');

echo "=== Moodle Course Seeder Starting ===\n";

$now = time();
$adminUser = $DB->get_record('user', ['id' => 2]);
$users = $DB->get_records_list('user', 'id', [2, 3, 4, 5]);

// Helper to attach cover image
function attach_course_image($courseid, $coursename) {
    global $DB;
    $coursecontext = context_course::instance($courseid);
    $source_file = '/home/sreerag/Documents/Projects/Other/moodledata/filedir/f2/81/f28187fc54e3fcdd35020ee6df00545c93d2f85c';
    if (!file_exists($source_file)) {
        return;
    }
    $fs = get_file_storage();
    // Delete existing overview file if any
    $fs->delete_area_files($coursecontext->id, 'course', 'overviewfiles');
    $filerecord = [
        'contextid' => $coursecontext->id,
        'component' => 'course',
        'filearea' => 'overviewfiles',
        'itemid' => 0,
        'filepath' => '/',
        'filename' => 'course_banner_' . $courseid . '.jpg',
        'userid' => 2,
        'author' => 'System Admin',
        'license' => 'allrightsreserved',
    ];
    $fs->create_file_from_pathname($filerecord, $source_file);
    echo "  [Image] Attached course cover image for course ID {$courseid}\n";
}

// Helper to create or get section
function get_or_create_section($courseid, $sectionnum, $name, $summary = '') {
    global $DB;
    $sec = $DB->get_record('course_sections', ['course' => $courseid, 'section' => $sectionnum]);
    if (!$sec) {
        $sec = new stdClass();
        $sec->course = $courseid;
        $sec->section = $sectionnum;
        $sec->name = $name;
        $sec->summary = $summary;
        $sec->summaryformat = 1;
        $sec->sequence = '';
        $sec->visible = 1;
        $sec->availability = null;
        $sec->timemodified = time();
        $sec->id = $DB->insert_record('course_sections', $sec);
    } else {
        $sec->name = $name;
        $sec->summary = $summary;
        $DB->update_record('course_sections', $sec);
    }
    return $sec;
}

// Helper to create page activity
function create_page_activity($course, $section, $title, $content) {
    global $DB;
    $page = new stdClass();
    $page->course = $course->id;
    $page->name = $title;
    $page->intro = '<p>' . htmlspecialchars($title) . '</p>';
    $page->introformat = 1;
    $page->content = $content;
    $page->contentformat = 1;
    $page->legacyfiles = 0;
    $page->display = 0;
    $page->displayoptions = 'a:2:{s:10:"printintro";i:0;s:17:"printlastmodified";i:1;}';
    $page->revision = 1;
    $page->timemodified = time();
    $page->id = $DB->insert_record('page', $page);

    $mod = $DB->get_record('modules', ['name' => 'page']);
    $cm = new stdClass();
    $cm->course = $course->id;
    $cm->module = $mod->id;
    $cm->instance = $page->id;
    $cm->section = $section->id;
    $cm->added = time();
    $cm->score = 0;
    $cm->indent = 0;
    $cm->visible = 1;
    $cm->visibleoncoursepage = 1;
    $cm->visibleold = 1;
    $cm->completion = 2; // COMPLETION_TRACKING_AUTOMATIC
    $cm->completionview = 1; // Require view
    $cm->completiongradeitemnumber = null;
    $cm->completionpassgrade = 0;
    $cm->completionexpected = 0;
    $cm->showdescription = 0;
    $cm->downloadcontent = 1;
    $cm->id = $DB->insert_record('course_modules', $cm);

    // Context
    context_module::instance($cm->id);

    // Update section sequence
    $seq = array_filter(explode(',', $section->sequence));
    $seq[] = $cm->id;
    $section->sequence = implode(',', $seq);
    $DB->set_field('course_sections', 'sequence', $section->sequence, ['id' => $section->id]);

    return ['page' => $page, 'cm' => $cm];
}

// Helper to create assignment activity
function create_assignment_activity($course, $section, $title, $intro, $gradepass = 75) {
    global $DB;
    $assign = new stdClass();
    $assign->course = $course->id;
    $assign->name = $title;
    $assign->intro = $intro;
    $assign->introformat = 1;
    $assign->alwaysshowdescription = 1;
    $assign->nosubmissions = 0;
    $assign->submissiondrafts = 0;
    $assign->sendnotifications = 0;
    $assign->sendlatenotifications = 0;
    $assign->duedate = 0;
    $assign->allowsubmissionsfromdate = 0;
    $assign->grade = 100;
    $assign->timemodified = time();
    $assign->requiresubmissionstatement = 0;
    $assign->completionsubmit = 1;
    $assign->cutoffdate = 0;
    $assign->gradingduedate = 0;
    $assign->teamsubmission = 0;
    $assign->requireallteammemberssubmit = 0;
    $assign->teamsubmissiongroupingid = 0;
    $assign->blindmarking = 0;
    $assign->hidegrader = 0;
    $assign->revealidentities = 0;
    $assign->attemptreopenmethod = 'none';
    $assign->maxattempts = -1;
    $assign->markingworkflow = 0;
    $assign->markingallocation = 0;
    $assign->sendstudentnotifications = 1;
    $assign->preventsubmissionnotingroup = 0;
    $assign->timelimit = 0;
    $assign->submissionattachments = 0;
    $assign->id = $DB->insert_record('assign', $assign);

    // Course module
    $mod = $DB->get_record('modules', ['name' => 'assign']);
    $cm = new stdClass();
    $cm->course = $course->id;
    $cm->module = $mod->id;
    $cm->instance = $assign->id;
    $cm->section = $section->id;
    $cm->added = time();
    $cm->score = 0;
    $cm->indent = 0;
    $cm->visible = 1;
    $cm->visibleoncoursepage = 1;
    $cm->visibleold = 1;
    $cm->completion = 2; // Automatic
    $cm->completionview = 1;
    $cm->completiongradeitemnumber = 0; // Require grade
    $cm->completionpassgrade = 1; // Require pass grade
    $cm->completionexpected = 0;
    $cm->showdescription = 0;
    $cm->downloadcontent = 1;
    $cm->id = $DB->insert_record('course_modules', $cm);

    context_module::instance($cm->id);

    // Section sequence
    $seq = array_filter(explode(',', $section->sequence));
    $seq[] = $cm->id;
    $section->sequence = implode(',', $seq);
    $DB->set_field('course_sections', 'sequence', $section->sequence, ['id' => $section->id]);
    rebuild_course_cache($course->id);

    // Create Grade Item
    assign_grade_item_update($assign);
    $gi = $DB->get_record('grade_items', ['courseid' => $course->id, 'itemtype' => 'mod', 'itemmodule' => 'assign', 'iteminstance' => $assign->id]);
    if ($gi) {
        $gi->gradepass = $gradepass;
        $gi->grademax = 100;
        $gi->grademin = 0;
        $DB->update_record('grade_items', $gi);
    }

    return ['assign' => $assign, 'cm' => $cm, 'gi' => $gi];
}

// Helper to create quiz activity with questions
function create_quiz_activity($course, $section, $title, $intro, $questions, $gradepass = 75) {
    global $DB;
    $quiz = new stdClass();
    $quiz->course = $course->id;
    $quiz->name = $title;
    $quiz->intro = $intro;
    $quiz->introformat = 1;
    $quiz->timeopen = 0;
    $quiz->timeclose = 0;
    $quiz->timelimit = 1800; // 30 mins
    $quiz->overduehandling = 'autosubmit';
    $quiz->graceperiod = 86400;
    $quiz->preferredbehaviour = 'deferredfeedback';
    $quiz->canredoquestions = 0;
    $quiz->attempts = 3;
    $quiz->attemptonlast = 0;
    $quiz->grademethod = 1; // Highest grade
    $quiz->decimalpoints = 2;
    $quiz->questiondecimalpoints = -1;
    $quiz->reviewattempt = 69904;
    $quiz->reviewcorrectness = 69904;
    $quiz->reviewmaxmarks = 69904;
    $quiz->reviewmarks = 69904;
    $quiz->reviewspecificfeedback = 69904;
    $quiz->reviewgeneralfeedback = 69904;
    $quiz->reviewrightanswer = 69904;
    $quiz->reviewoverallfeedback = 4368;
    $quiz->questionsperpage = 1;
    $quiz->navmethod = 'free';
    $quiz->shuffleanswers = 1;
    $quiz->sumgrades = 100.00000;
    $quiz->grade = 100.00000;
    $quiz->timecreated = time();
    $quiz->timemodified = time();
    $quiz->password = '';
    $quiz->subnet = '';
    $quiz->browsersecurity = '';
    $quiz->delay1 = 0;
    $quiz->delay2 = 0;
    $quiz->showuserpicture = 0;
    $quiz->showblocks = 0;
    $quiz->completionattemptsexhausted = 0;
    $quiz->completionminattempts = 0;
    $quiz->allowofflineattempts = 0;
    $quiz->id = $DB->insert_record('quiz', $quiz);

    // Course module
    $mod = $DB->get_record('modules', ['name' => 'quiz']);
    $cm = new stdClass();
    $cm->course = $course->id;
    $cm->module = $mod->id;
    $cm->instance = $quiz->id;
    $cm->section = $section->id;
    $cm->added = time();
    $cm->score = 0;
    $cm->indent = 0;
    $cm->visible = 1;
    $cm->visibleoncoursepage = 1;
    $cm->visibleold = 1;
    $cm->completion = 2; // Automatic
    $cm->completionview = 1;
    $cm->completiongradeitemnumber = 0; // Require grade
    $cm->completionpassgrade = 1; // Require pass grade
    $cm->completionexpected = 0;
    $cm->showdescription = 0;
    $cm->downloadcontent = 1;
    $cm->id = $DB->insert_record('course_modules', $cm);

    $quizcontext = context_module::instance($cm->id);

    // Section sequence
    $seq = array_filter(explode(',', $section->sequence));
    $seq[] = $cm->id;
    $section->sequence = implode(',', $seq);
    $DB->set_field('course_sections', 'sequence', $section->sequence, ['id' => $section->id]);
    rebuild_course_cache($course->id);

    // Create Grade Item
    quiz_grade_item_update($quiz);
    $gi = $DB->get_record('grade_items', ['courseid' => $course->id, 'itemtype' => 'mod', 'itemmodule' => 'quiz', 'iteminstance' => $quiz->id]);
    if ($gi) {
        $gi->gradepass = $gradepass;
        $gi->grademax = 100;
        $gi->grademin = 0;
        $DB->update_record('grade_items', $gi);
    }

    // Question category for this course
    $coursecontext = context_course::instance($course->id);
    $qcat = $DB->get_record('question_categories', ['contextid' => $coursecontext->id, 'name' => 'Questions: ' . $course->shortname]);
    if (!$qcat) {
        $qcat = new stdClass();
        $qcat->name = 'Questions: ' . $course->shortname;
        $qcat->contextid = $coursecontext->id;
        $qcat->info = 'Question bank for ' . $course->fullname;
        $qcat->infoformat = 1;
        $qcat->stamp = make_unique_id_code();
        $qcat->parent = 0;
        $qcat->sortorder = 999;
        $qcat->idnumber = '';
        $qcat->id = $DB->insert_record('question_categories', $qcat);
    }

    // Add questions
    $slotnum = 1;
    $points_per_q = round(100 / count($questions), 2);
    foreach ($questions as $qdata) {
        // question_bank_entries
        $qbe = new stdClass();
        $qbe->questioncategoryid = $qcat->id;
        $qbe->idnumber = null;
        $qbe->ownerid = 2;
        $qbe->id = $DB->insert_record('question_bank_entries', $qbe);

        // question
        $q = new stdClass();
        $q->category = $qcat->id;
        $q->parent = 0;
        $q->name = $qdata['name'];
        $q->questiontext = '<p>' . $qdata['text'] . '</p>';
        $q->questiontextformat = 1;
        $q->generalfeedback = '<p>' . ($qdata['feedback'] ?? 'Review documentation.') . '</p>';
        $q->generalfeedbackformat = 1;
        $q->defaultmark = $points_per_q;
        $q->penalty = 0.3333333;
        $q->qtype = 'multichoice';
        $q->length = 1;
        $q->stamp = make_unique_id_code();
        $q->timecreated = time();
        $q->timemodified = time();
        $q->createdby = 2;
        $q->modifiedby = 2;
        $q->id = $DB->insert_record('question', $q);

        // question_versions
        $qv = new stdClass();
        $qv->questionbankentryid = $qbe->id;
        $qv->version = 1;
        $qv->questionid = $q->id;
        $qv->status = 'ready';
        $qv->id = $DB->insert_record('question_versions', $qv);

        // question_answers
        foreach ($qdata['answers'] as $ans) {
            $qa = new stdClass();
            $qa->question = $q->id;
            $qa->answer = $ans['text'];
            $qa->answerformat = 1;
            $qa->fraction = $ans['fraction'];
            $qa->feedback = '<p>' . $ans['feedback'] . '</p>';
            $qa->feedbackformat = 1;
            $DB->insert_record('question_answers', $qa);
        }

        // qtype_multichoice_options
        $mco = new stdClass();
        $mco->questionid = $q->id;
        $mco->layout = 0;
        $mco->single = 1;
        $mco->shuffleanswers = 1;
        $mco->correctfeedback = 'Your answer is correct.';
        $mco->correctfeedbackformat = 1;
        $mco->partiallycorrectfeedback = 'Your answer is partially correct.';
        $mco->partiallycorrectfeedbackformat = 1;
        $mco->incorrectfeedback = 'Your answer is incorrect.';
        $mco->incorrectfeedbackformat = 1;
        $mco->answernumbering = 'abc';
        $mco->shownumcorrect = 1;
        $mco->showstandardinstruction = 1;
        $DB->insert_record('qtype_multichoice_options', $mco);

        // quiz_slots
        $slot = new stdClass();
        $slot->slot = $slotnum;
        $slot->quizid = $quiz->id;
        $slot->page = $slotnum;
        $slot->displaynumber = null;
        $slot->requireprevious = 0;
        $slot->maxmark = $points_per_q;
        $slot->id = $DB->insert_record('quiz_slots', $slot);

        // question_references
        $qr = new stdClass();
        $qr->usingcontextid = $quizcontext->id;
        $qr->component = 'mod_quiz';
        $qr->questionarea = 'slot';
        $qr->itemid = $slot->id;
        $qr->questionbankentryid = $qbe->id;
        $qr->version = null;
        $DB->insert_record('question_references', $qr);

        $slotnum++;
    }

    // Update quiz sumgrades
    $quiz->sumgrades = 100.00000;
    $quiz->grade = 100.00000;
    $DB->update_record('quiz', $quiz);
    rebuild_course_cache($course->id);
    quiz_update_sumgrades($quiz);

    return ['quiz' => $quiz, 'cm' => $cm, 'gi' => $gi];
}

// Function to configure Course Completion criteria
function setup_course_completion_criteria($course, $cms_required) {
    global $DB;
    // Delete existing criteria for this course
    $DB->delete_records('course_completion_criteria', ['course' => $course->id]);
    $DB->delete_records('course_completion_aggr_methd', ['course' => $course->id]);

    // Insert aggregation method = 1 (ALL criteria required)
    $agg = new stdClass();
    $agg->course = $course->id;
    $agg->criteriatype = null;
    $agg->method = 1; // All
    $agg->value = 1.0;
    $DB->insert_record('course_completion_aggr_methd', $agg);

    // Insert criteria for each required module
    foreach ($cms_required as $cm_item) {
        $crit = new stdClass();
        $crit->course = $course->id;
        $crit->criteriatype = COMPLETION_CRITERIA_TYPE_ACTIVITY;
        $crit->module = $cm_item['modname'];
        $crit->moduleinstance = $cm_item['cmid'];
        $crit->courseinstance = null;
        $crit->enrolperiod = null;
        $crit->timeend = null;
        $crit->gradepass = null;
        $crit->role = null;
        $DB->insert_record('course_completion_criteria', $crit);
    }
}

// Function to enrol users into course
function enrol_course_users($course) {
    global $DB;
    $enrol_plugin = enrol_get_plugin('manual');
    $instances = $DB->get_records('enrol', ['courseid' => $course->id]);
    $manual_instance = null;
    foreach ($instances as $instance) {
        if ($instance->enrol === 'manual') {
            $manual_instance = $instance;
            break;
        }
    }
    if (!$manual_instance) {
        $manual_instance = new stdClass();
        $manual_instance->enrol = 'manual';
        $manual_instance->status = ENROL_INSTANCE_ENABLED;
        $manual_instance->courseid = $course->id;
        $manual_instance->enrolstartdate = 0;
        $manual_instance->enrolenddate = 0;
        $manual_instance->timecreated = time();
        $manual_instance->timemodified = time();
        $manual_instance->id = $DB->insert_record('enrol', $manual_instance);
    }

    $users_to_enrol = [
        2 => 3, // Admin -> Editing Teacher
        3 => 5, // Awadhesh -> Student
        4 => 5, // Sharvan -> Student
        5 => 5, // Gautam -> Student
    ];

    foreach ($users_to_enrol as $userid => $roleid) {
        $exists = $DB->get_record('user_enrolments', ['enrolid' => $manual_instance->id, 'userid' => $userid]);
        if (!$exists) {
            $enrol_plugin->enrol_user($manual_instance, $userid, $roleid);
        }
    }
}

// Function to record student completion and grading directly into DB and Moodle gradebook
function record_student_grade_and_completion($course, $student_userid, $assign_res, $quiz_res, $all_cms, $assign_grade_val, $quiz_grade_val) {
    global $DB;
    $now = time();

    // 1. Mark Page completions
    $completion = new completion_info($course);
    foreach ($all_cms as $cm_item) {
        $cm_record = get_coursemodule_from_id('', $cm_item->id);
        if ($cm_record) {
            $completion->update_state($cm_record, COMPLETION_COMPLETE, $student_userid);
        }
    }

    // 2. Grade Assignment
    $assign = $assign_res['assign'];
    $assign_cm = $assign_res['cm'];
    $assign_gi = $assign_res['gi'];

    // assign_submission
    $sub = $DB->get_record('assign_submission', ['assignment' => $assign->id, 'userid' => $student_userid]);
    if (!$sub) {
        $sub = new stdClass();
        $sub->assignment = $assign->id;
        $sub->userid = $student_userid;
        $sub->timecreated = $now - 7200;
        $sub->timemodified = $now - 3600;
        $sub->status = 'submitted';
        $sub->attemptnumber = 0;
        $sub->latest = 1;
        $sub->id = $DB->insert_record('assign_submission', $sub);
    }

    // assign_grades
    $ag = $DB->get_record('assign_grades', ['assignment' => $assign->id, 'userid' => $student_userid]);
    if (!$ag) {
        $ag = new stdClass();
        $ag->assignment = $assign->id;
        $ag->userid = $student_userid;
        $ag->timecreated = $now - 3600;
        $ag->timemodified = $now - 1800;
        $ag->grader = 2; // Admin
        $ag->grade = (float)$assign_grade_val;
        $ag->attemptnumber = 0;
        $ag->id = $DB->insert_record('assign_grades', $ag);
    } else {
        $ag->grade = (float)$assign_grade_val;
        $ag->timemodified = $now - 1800;
        $DB->update_record('assign_grades', $ag);
    }

    // Update assignment grade in gradebook
    assign_update_grades($assign, $student_userid);

    // Ensure grade_grades record is accurate
    $assign_gg = $DB->get_record('grade_grades', ['itemid' => $assign_gi->id, 'userid' => $student_userid]);
    if ($assign_gg) {
        $assign_gg->finalgrade = (float)$assign_grade_val;
        $assign_gg->rawgrade = (float)$assign_grade_val;
        $assign_gg->usermodified = 2;
        $assign_gg->timemodified = $now - 1800;
        $DB->update_record('grade_grades', $assign_gg);
    }

    // Mark assignment module completion (pass)
    $completion->update_state(get_coursemodule_from_id('', $assign_cm->id), COMPLETION_COMPLETE_PASS, $student_userid);

    // 3. Grade Quiz
    $quiz = $quiz_res['quiz'];
    $quiz_cm = $quiz_res['cm'];
    $quiz_gi = $quiz_res['gi'];

    // quiz_grades
    $qg = $DB->get_record('quiz_grades', ['quiz' => $quiz->id, 'userid' => $student_userid]);
    if (!$qg) {
        $qg = new stdClass();
        $qg->quiz = $quiz->id;
        $qg->userid = $student_userid;
        $qg->grade = (float)$quiz_grade_val;
        $qg->timemodified = $now - 900;
        $qg->id = $DB->insert_record('quiz_grades', $qg);
    } else {
        $qg->grade = (float)$quiz_grade_val;
        $qg->timemodified = $now - 900;
        $DB->update_record('quiz_grades', $qg);
    }

    // quiz_attempts
    $attempt = $DB->get_record('quiz_attempts', ['quiz' => $quiz->id, 'userid' => $student_userid]);
    if (!$attempt) {
        $attempt = new stdClass();
        $attempt->quiz = $quiz->id;
        $attempt->userid = $student_userid;
        $attempt->attempt = 1;
        $quizcontext = context_module::instance($quiz_cm->id);
        $qu = new stdClass();
        $qu->contextid = $quizcontext->id;
        $qu->component = 'mod_quiz';
        $qu->preferredbehaviour = 'deferredfeedback';
        $attempt->uniqueid = $DB->insert_record('question_usages', $qu);
        $attempt->layout = '1,2,3,4,5,0';
        $attempt->currentpage = 5;
        $attempt->preview = 0;
        $attempt->state = 'finished';
        $attempt->timestart = $now - 2700;
        $attempt->timefinish = $now - 900;
        $attempt->timemodified = $now - 900;
        $attempt->timemodifiedoffline = 0;
        $attempt->timecheckstate = null;
        $attempt->sumgrades = (float)$quiz_grade_val;
        $attempt->id = $DB->insert_record('quiz_attempts', $attempt);
    }

    // Update quiz grade in gradebook
    quiz_update_grades($quiz, $student_userid);

    $quiz_gg = $DB->get_record('grade_grades', ['itemid' => $quiz_gi->id, 'userid' => $student_userid]);
    if ($quiz_gg) {
        $quiz_gg->finalgrade = (float)$quiz_grade_val;
        $quiz_gg->rawgrade = (float)$quiz_grade_val;
        $quiz_gg->usermodified = 2;
        $quiz_gg->timemodified = $now - 900;
        $DB->update_record('grade_grades', $quiz_gg);
    }

    // Mark quiz module completion (pass)
    $completion->update_state(get_coursemodule_from_id('', $quiz_cm->id), COMPLETION_COMPLETE_PASS, $student_userid);

    // 4. Update Course Grade total
    grade_regrade_final_grades($course->id);

    // 5. Mark Course Completion in database
    $cc = $DB->get_record('course_completions', ['course' => $course->id, 'userid' => $student_userid]);
    if (!$cc) {
        $cc = new stdClass();
        $cc->userid = $student_userid;
        $cc->course = $course->id;
        $cc->timeenrolled = $now - 86400;
        $cc->timestarted = $now - 72000;
        $cc->timecompleted = $now - 900;
        $cc->reaggregate = 0;
        $cc->id = $DB->insert_record('course_completions', $cc);
    } else {
        $cc->timecompleted = $now - 900;
        $cc->reaggregate = 0;
        $DB->update_record('course_completions', $cc);
    }

    // Mark criteria completion records
    $crits = $DB->get_records('course_completion_criteria', ['course' => $course->id]);
    foreach ($crits as $c) {
        $crit_compl = $DB->get_record('course_completion_crit_compl', ['userid' => $student_userid, 'course' => $course->id, 'criteriaid' => $c->id]);
        if (!$crit_compl) {
            $crit_compl = new stdClass();
            $crit_compl->userid = $student_userid;
            $crit_compl->course = $course->id;
            $crit_compl->criteriaid = $c->id;
            $crit_compl->gradefinal = ($c->module === 'assign') ? (float)$assign_grade_val : (float)$quiz_grade_val;
            $crit_compl->raggregate = 0;
            $crit_compl->timecompleted = $now - 900;
            $DB->insert_record('course_completion_crit_compl', $crit_compl);
        }
    }
}

// ==========================================
// COURSE 1: DevOps & Kubernetes Engineering
// ==========================================
$short1 = 'K8S-201';
$course1 = $DB->get_record('course', ['shortname' => $short1]);
if (!$course1) {
    echo "Creating Course 1: DevOps, Kubernetes & Cloud-Native Infrastructure ($short1)...\n";
    $cdata = new stdClass();
    $cdata->category = 1;
    $cdata->fullname = 'DevOps, Kubernetes & Cloud-Native Infrastructure';
    $cdata->shortname = $short1;
    $cdata->idnumber = 'COURSE-DEVOPS-201';
    $cdata->summary = '<p>Master enterprise container orchestration, production Kubernetes clusters, Docker multi-stage build optimization, Helm 3 packaging, automated CI/CD pipelines, Prometheus & Grafana telemetry, and cloud resilience architectures.</p>';
    $cdata->summaryformat = 1;
    $cdata->format = 'topics';
    $cdata->showgrades = 1;
    $cdata->enablecompletion = 1;
    $cdata->showcompletionconditions = 1;
    $cdata->visible = 1;
    $cdata->startdate = $now - 86400 * 7;
    $cdata->enddate = 0;
    $cdata->timecreated = $now;
    $cdata->timemodified = $now;
    $course1 = create_course($cdata);
} else {
    echo "Course 1 already exists with ID {$course1->id}. Updating settings...\n";
    $course1->enablecompletion = 1;
    $course1->showcompletionconditions = 1;
    $course1->showgrades = 1;
    $DB->update_record('course', $course1);
}

attach_course_image($course1->id, $course1->fullname);

// Sections for Course 1
$sec0 = get_or_create_section($course1->id, 0, 'General', '<p>Welcome to DevOps, Kubernetes & Cloud-Native Infrastructure.</p>');
$sec1 = get_or_create_section($course1->id, 1, 'Module 1: Container Architecture & Linux Internals', '<p>Understand Linux namespaces, cgroups, container runtimes (containerd/CRI-O), and image optimization.</p>');
$sec2 = get_or_create_section($course1->id, 2, 'Module 2: Kubernetes Workloads & Cloud Orchestration', '<p>Deploy and scale production workloads using Pods, Deployments, StatefulSets, Services, and Ingress controllers.</p>');
$sec3 = get_or_create_section($course1->id, 3, 'Module 3: Production Hands-on Lab Assessment', '<p>Complete the containerized microservices deployment project and submit your architecture manifest.</p>');
$sec4 = get_or_create_section($course1->id, 4, 'Module 4: Professional Certification Assessment', '<p>Pass the comprehensive DevOps & Kubernetes Professional Certification Exam (75% passing grade required).</p>');

// Page 1.1
$p1_content = '<h3>Linux Kernel Primitives: Namespaces and Cgroups</h3>' .
    '<p>Containers are not lightweight virtual machines; they are regular Linux processes running with isolated views of system resources provided by kernel namespaces and bounded by control groups (cgroups).</p>' .
    '<ul>' .
    '<li><strong>PID Namespace:</strong> Isolates the process ID space, allowing the container entrypoint to run as PID 1 inside the container while having a distinct host PID.</li>' .
    '<li><strong>NET Namespace:</strong> Provides virtual network interfaces, independent routing tables, and IP firewall rules.</li>' .
    '<li><strong>MNT Namespace:</strong> Provides an isolated file system hierarchy using pivot_root.</li>' .
    '<li><strong>Control Groups v2 (cgroups):</strong> Enforce memory limits (OOM prevention), CPU quota quotas (CFS scheduler), and I/O bandwidth throttling.</li>' .
    '</ul>';
$p1 = create_page_activity($course1, $sec1, 'Lesson 1.1: Deep Dive into Linux Namespaces, Cgroups & Container Runtimes', $p1_content);

// Page 1.2
$p2_content = '<h3>Production Dockerfile Optimization & Multi-Stage Builds</h3>' .
    '<p>Minimizing container attack surfaces and decreasing image pull times are crucial for high-velocity CI/CD deployments.</p>' .
    '<pre><code># Stage 1: Build stage with build dependencies
FROM golang:1.22-alpine AS builder
WORKDIR /app
COPY go.mod go.sum ./
RUN go mod download
COPY . .
RUN CGO_ENABLED=0 GOOS=linux go build -ldflags="-w -s" -o api-service ./cmd/api

# Stage 2: Distroless minimal scratch runtime
FROM gcr.io/distroless/static-debian12:nonroot
WORKDIR /
COPY --from=builder /app/api-service /api-service
USER nonroot:nonroot
EXPOSE 8080
ENTRYPOINT ["/api-service"]</code></pre>' .
    '<p>By adopting multi-stage builds and distroless base images, final image footprints decrease from 800MB to under 25MB, removing package managers, compilers, and shell access.</p>';
$p2 = create_page_activity($course1, $sec1, 'Lesson 1.2: Production Dockerfile Optimization & Minimal Distroless Builds', $p2_content);

// Page 2.1
$p3_content = '<h3>Kubernetes Core Primitives: Pods, ReplicaSets, and Deployments</h3>' .
    '<p>A Pod represents the smallest deployable computing unit in Kubernetes. Deployments manage declarative updates for Pods and ReplicaSets.</p>' .
    '<ul>' .
    '<li><strong>Rolling Updates:</strong> MaxSurge and MaxUnavailable parameters guarantee zero-downtime upgrades.</li>' .
    '<li><strong>Liveness & Readiness Probes:</strong> Prevent routing traffic to uninitialized pods and restart failing containers.</li>' .
    '<li><strong>StatefulSets:</strong> Guarantee stable, unique network identifiers and persistent storage bindings across reschedules.</li>' .
    '</ul>';
$p3 = create_page_activity($course1, $sec2, 'Lesson 2.1: Kubernetes Workloads — Deployments, StatefulSets & Probes', $p3_content);

// Assignment in Section 3
$assign1_intro = '<p><strong>Project Scenario:</strong> Design, containerize, and deploy a three-tier microservice architecture (Frontend, Backend API, Redis caching layer) on a Kubernetes cluster.</p>' .
    '<p><strong>Submission Requirements:</strong> Submit your multi-stage Dockerfiles, Kubernetes manifests (Deployment, ClusterIP Service, Ingress NGINX with TLS termination, Resource Limits, and ConfigMaps), and a brief verification log.</p>' .
    '<p><strong>Passing Criteria:</strong> Minimum grade of 75/100 required for course completion.</p>';
$assign1 = create_assignment_activity($course1, $sec3, 'DevOps Hands-on Project: Multi-Tier Microservices Deployment on Kubernetes', $assign1_intro, 75);

// Quiz in Section 4
$quiz1_intro = '<p>This exam assesses your real-world comprehension of Kubernetes orchestration, container security, rolling updates, and cluster troubleshooting.</p><p><strong>Requirements:</strong> 5 scenario questions | 30 minutes | 75% passing score required for completion.</p>';
$quiz1_questions = [
    [
        'name' => 'K8s Readiness vs Liveness Probes',
        'text' => 'What is the primary operational consequence when a container fails its Kubernetes <strong>Readiness Probe</strong>, while its Liveness Probe continues to pass?',
        'feedback' => 'Readiness probe failures remove the pod IP from all associated Service endpoints to stop traffic delivery without terminating the process.',
        'answers' => [
            ['text' => 'The Pod IP is immediately removed from the Service Endpoints so no client traffic is routed to it, but the container is NOT restarted.', 'fraction' => 1.0, 'feedback' => 'Correct! This allows the application time to warm up or process queues without restarts.'],
            ['text' => 'Kubelet immediately sends a SIGTERM and kills the container.', 'fraction' => 0.0, 'feedback' => 'Incorrect. That is the behavior of a failed Liveness probe.'],
            ['text' => 'The Node is cordoned and marked NotReady by the Controller Manager.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'All ReplicaSet replicas are scaled down to zero.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
        ]
    ],
    [
        'name' => 'Docker Multi-Stage Build Advantages',
        'text' => 'Why are multi-stage Docker builds recommended for production enterprise container images?',
        'feedback' => 'Multi-stage builds separate compilation dependencies from the lean runtime artifact.',
        'answers' => [
            ['text' => 'They eliminate build-time tools, package managers, and source code from the final runtime image, significantly reducing attack surface and image size.', 'fraction' => 1.0, 'feedback' => 'Correct! Only production binaries and runtime necessities are shipped.'],
            ['text' => 'They automatically compress all images into ZIP archives before pushing to registry.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'They bypass the Linux kernel cgroups memory restriction.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'They enable live code modification on running Kubernetes pods without redeployment.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
        ]
    ],
    [
        'name' => 'Zero Downtime Deployment Strategies',
        'text' => 'In a standard Kubernetes Deployment object, which parameters control how many additional pods can be scheduled and how many can be unavailable during a rolling update?',
        'feedback' => 'maxSurge and maxUnavailable configure rolling update thresholds.',
        'answers' => [
            ['text' => 'maxSurge and maxUnavailable within the rollingUpdate deployment strategy.', 'fraction' => 1.0, 'feedback' => 'Correct! These ensure capacity stays above minimum thresholds during rollouts.'],
            ['text' => 'minReadySeconds and terminationGracePeriodSeconds.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'restartPolicy and backoffLimit.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'nodeSelector and tolerations.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
        ]
    ],
    [
        'name' => 'Kubernetes Ingress Controllers',
        'text' => 'How does an Ingress Controller (such as Ingress-NGINX) differ from a standard NodePort service in managing inbound HTTP/S cluster traffic?',
        'feedback' => 'Ingress operates at Layer 7 providing path-based and host-based routing with centralized SSL termination.',
        'answers' => [
            ['text' => 'Ingress provides Layer 7 application routing, SSL/TLS termination, and path-based routing through a single reverse proxy entrypoint, avoiding port proliferation.', 'fraction' => 1.0, 'feedback' => 'Correct! Ingress manages HTTP/HTTPS traffic at Layer 7.'],
            ['text' => 'Ingress only handles raw Layer 4 TCP byte streams and cannot inspect HTTP headers.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'NodePort encrypts traffic automatically while Ingress is plain HTTP only.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'Ingress requires each Pod to hold a public IPv4 address.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
        ]
    ],
    [
        'name' => 'StatefulSet Persistent Volume Claims',
        'text' => 'When a Kubernetes StatefulSet with a <code>volumeClaimTemplates</code> specification scales down and up, how are Persistent Volumes managed?',
        'feedback' => 'PVCs created by StatefulSets are persistent and reattached by ordinal index.',
        'answers' => [
            ['text' => 'Each pod ordinal (e.g. data-store-0, data-store-1) binds to its dedicated PVC, which is preserved and reattached when pods restart or reschedule.', 'fraction' => 1.0, 'feedback' => 'Correct! StatefulSets guarantee persistent identity and storage pairing across the pod lifecycle.'],
            ['text' => 'All pods share a single temporary in-memory tmpfs mount that is wiped on pod termination.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'Persistent Volume Claims are deleted immediately upon scaling down to free up cloud disk space.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'StatefulSets do not support storage templates; storage must be hardcoded.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
        ]
    ]
];
$quiz1 = create_quiz_activity($course1, $sec4, 'Kubernetes & Cloud Infrastructure Certification Exam', $quiz1_intro, $quiz1_questions, 75);

// Configure course completion criteria for Course 1
setup_course_completion_criteria($course1, [
    ['modname' => 'assign', 'cmid' => $assign1['cm']->id],
    ['modname' => 'quiz', 'cmid' => $quiz1['cm']->id],
]);

// Enrol users
enrol_course_users($course1);

// Rebuild course cache
rebuild_course_cache($course1->id);

// Record student grades and completions
$all_cms_c1 = [$p1['cm'], $p2['cm'], $p3['cm'], $assign1['cm'], $quiz1['cm']];
echo "  [Grades & Completion] Recording Awadhesh Verma (user 3) completion on Course 1...\n";
record_student_grade_and_completion($course1, 3, $assign1, $quiz1, $all_cms_c1, 95.0, 92.0);

echo "  [Grades & Completion] Recording Sharvan Tripathi (user 4) completion on Course 1...\n";
record_student_grade_and_completion($course1, 4, $assign1, $quiz1, $all_cms_c1, 88.0, 84.0);

echo "  Course 1 setup complete! Awadhesh progress: " . \core_completion\progress::get_course_progress_percentage($course1, 3) . "%\n\n";

// ==========================================
// COURSE 2: AI & LLM Engineering
// ==========================================
$short2 = 'AIML-301';
$course2 = $DB->get_record('course', ['shortname' => $short2]);
if (!$course2) {
    echo "Creating Course 2: Artificial Intelligence & Large Language Model (LLM) Engineering ($short2)...\n";
    $cdata2 = new stdClass();
    $cdata2->category = 1;
    $cdata2->fullname = 'Artificial Intelligence & Large Language Model (LLM) Engineering';
    $cdata2->shortname = $short2;
    $cdata2->idnumber = 'COURSE-AIML-301';
    $cdata2->summary = '<p>Comprehensive engineering course covering Transformer architectures, Multi-Head Self-Attention mechanisms, fine-tuning LLMs with LoRA/QLoRA, Retrieval-Augmented Generation (RAG) pipelines with vector search (Qdrant & FAISS), and autonomous agent frameworks.</p>';
    $cdata2->summaryformat = 1;
    $cdata2->format = 'topics';
    $cdata2->showgrades = 1;
    $cdata2->enablecompletion = 1;
    $cdata2->showcompletionconditions = 1;
    $cdata2->visible = 1;
    $cdata2->startdate = $now - 86400 * 5;
    $cdata2->enddate = 0;
    $cdata2->timecreated = $now;
    $cdata2->timemodified = $now;
    $course2 = create_course($cdata2);
} else {
    echo "Course 2 already exists with ID {$course2->id}. Updating settings...\n";
    $course2->enablecompletion = 1;
    $course2->showcompletionconditions = 1;
    $course2->showgrades = 1;
    $DB->update_record('course', $course2);
}

attach_course_image($course2->id, $course2->fullname);

// Sections for Course 2
$c2_sec0 = get_or_create_section($course2->id, 0, 'General', '<p>Welcome to AI & LLM Engineering.</p>');
$c2_sec1 = get_or_create_section($course2->id, 1, 'Module 1: Transformer Architectures & Attention Mechanics', '<p>Explore mathematical foundations of Self-Attention, Key-Query-Value projections, and Transformer decoder models.</p>');
$c2_sec2 = get_or_create_section($course2->id, 2, 'Module 2: Production Retrieval-Augmented Generation (RAG)', '<p>Build enterprise RAG with semantic embeddings, hybrid keyword-vector search, reranking, and chunking strategies.</p>');
$c2_sec3 = get_or_create_section($course2->id, 3, 'Module 3: Hands-on Capstone Engineering Assessment', '<p>Construct a production-grade RAG knowledge retrieval service with verifiable citations.</p>');
$c2_sec4 = get_or_create_section($course2->id, 4, 'Module 4: AI & LLM Professional Certification Exam', '<p>Pass the technical certification exam evaluating RAG, fine-tuning, and LLM safety (75% passing grade required).</p>');

// Page 2.1
$c2_p1_content = '<h3>Transformer Attention Mechanisms: Scaled Dot-Product Attention</h3>' .
    '<p>The core breakthrough of the Transformer architecture (Vaswani et al.) is replacing sequential recurrence with parallelizable self-attention.</p>' .
    '<p>Attention is computed via: <code>Attention(Q, K, V) = softmax((Q * K^T) / sqrt(d_k)) * V</code></p>' .
    '<ul>' .
    '<li><strong>Query (Q):</strong> Representation of what the current token is seeking.</li>' .
    '<li><strong>Key (K):</strong> Representation of what previous tokens contain or describe.</li>' .
    '<li><strong>Value (V):</strong> The information content extracted when Query matches Key.</li>' .
    '<li><strong>Multi-Head Attention:</strong> Allows the model to attend jointly to information from different representation subspaces at different positions.</li>' .
    '</ul>';
$c2_p1 = create_page_activity($course2, $c2_sec1, 'Lesson 1.1: Scaled Dot-Product Attention & Multi-Head Transformers', $c2_p1_content);

// Page 2.2
$c2_p2_content = '<h3>Parameter-Efficient Fine-Tuning (PEFT): LoRA & QLoRA</h3>' .
    '<p>Fine-tuning billions of parameters is computationally prohibitive. Low-Rank Adaptation (LoRA) freezes pretrained weights and injects trainable rank decomposition matrices into attention projections.</p>' .
    '<ul>' .
    '<li><strong>Weight Update:</strong> <code>W = W_0 + (B * A) * (alpha / r)</code> where rank <code>r &lt;&lt; d</code> (e.g., r = 8 or 16).</li>' .
    '<li><strong>Memory Savings:</strong> Reduces trainable parameters by over 99% while achieving comparable task accuracy.</li>' .
    '<li><strong>QLoRA:</strong> Quantizes the base model into 4-bit NormalFloat (NF4) with Double Quantization and Paged Optimizers, allowing 70B parameter models to fine-tune on consumer GPUs.</li>' .
    '</ul>';
$c2_p2 = create_page_activity($course2, $c2_sec1, 'Lesson 1.2: Parameter-Efficient Fine-Tuning with LoRA & QLoRA', $c2_p2_content);

// Page 2.3
$c2_p3_content = '<h3>Production RAG Pipelines: Chunking, Vector Embeddings & Reranking</h3>' .
    '<p>Standard naive RAG suffers from semantic drift, lost context, and hallucinations. Enterprise RAG incorporates:</p>' .
    '<ol>' .
    '<li><strong>Context-Aware Chunking:</strong> Semantic sentence splitting with sliding window overlaps.</li>' .
    '<li><strong>Hybrid Search:</strong> Combining dense vector embeddings (e.g. text-embedding-3-large) with sparse lexical BM25 matching.</li>' .
    '<li><strong>Cross-Encoder Reranking:</strong> Passing top 50 retrieved documents through a Cohere or BGE reranker to select top 5 highest-relevance contexts.</li>' .
    '<li><strong>Strict Grounding:</strong> Prompting LLMs with strict negative constraints ("Answer ONLY using provided context; cite page numbers").</li>' .
    '</ol>';
$c2_p3 = create_page_activity($course2, $c2_sec2, 'Lesson 2.1: Advanced Hybrid Search & Cross-Encoder Reranking in RAG', $c2_p3_content);

// Assignment in Section 3
$assign2_intro = '<p><strong>Project Specification:</strong> Develop a complete Python/FastAPI service implementing an end-to-end RAG knowledge assistant.</p>' .
    '<p><strong>Deliverables:</strong> Ingestion pipeline with chunking, Qdrant/ChromaDB vector integration, hybrid BM25 + dense search, cross-encoder reranker, and JSON response with citation sources.</p>' .
    '<p><strong>Passing Criteria:</strong> Minimum score of 75/100 required for course completion.</p>';
$assign2 = create_assignment_activity($course2, $c2_sec3, 'Enterprise AI Lab: Production RAG Assistant with Hybrid Search & Citations', $assign2_intro, 75);

// Quiz in Section 4
$quiz2_intro = '<p>Assess your mastery of generative AI systems, attention mechanisms, fine-tuning mathematics, and vector search architectures.</p><p><strong>Requirements:</strong> 5 questions | 30 minutes | 75% passing score required.</p>';
$quiz2_questions = [
    [
        'name' => 'Transformer Attention Scaling Factor',
        'text' => 'In the Scaled Dot-Product Attention formula <code>softmax((Q * K^T) / sqrt(d_k)) * V</code>, why is the dot product divided by <code>sqrt(d_k)</code>?',
        'feedback' => 'Scaling prevents large dot products from pushing softmax into regions with vanishing gradients.',
        'answers' => [
            ['text' => 'For large dimensions d_k, the dot products grow large in magnitude, pushing the softmax function into regions with extremely small gradients; dividing by sqrt(d_k) counteracts this effect.', 'fraction' => 1.0, 'feedback' => 'Correct! It prevents vanishing gradients during backpropagation.'],
            ['text' => 'To normalize token lengths so shorter sentences have equal word count to longer sentences.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'To convert floating point tensors into integer quantization format.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'To encrypt the query and key vectors against adversarial gradient inversion attacks.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
        ]
    ],
    [
        'name' => 'LoRA Matrix Decomposition',
        'text' => 'How does Low-Rank Adaptation (LoRA) enable parameter-efficient fine-tuning of large pre-trained foundation models?',
        'feedback' => 'LoRA represents the weight update delta matrix as the product of two low-rank matrices.',
        'answers' => [
            ['text' => 'It freezes the pre-trained model weights and injects trainable rank decomposition matrices (A and B) into the Transformer layers, drastically reducing trainable parameter count.', 'fraction' => 1.0, 'feedback' => 'Correct! Rank r decomposition allows massive memory and compute reductions.'],
            ['text' => 'It randomly prunes 90% of the attention heads permanently before starting training.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'It replaces the transformer decoder with a recursive LSTM recurrent cell.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'It trains only the token embedding lookup table while deleting all feedforward layers.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
        ]
    ],
    [
        'name' => 'RAG Hybrid Search Benefits',
        'text' => 'Why does combining Dense Vector Search with Sparse Lexical Search (BM25) outperform pure vector search in enterprise RAG systems?',
        'feedback' => 'Dense search excels at conceptual semantic similarity, while BM25 excels at exact keyword matching for serial numbers, acronyms, and product codes.',
        'answers' => [
            ['text' => 'Dense search captures semantic concepts and synonyms, while sparse BM25 guarantees precision for exact identifiers, product codes, acronyms, and specific names.', 'fraction' => 1.0, 'feedback' => 'Correct! Hybrid search covers both semantic understanding and exact keyword precision.'],
            ['text' => 'Sparse BM25 search eliminates the need for vector embeddings entirely.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'BM25 generates new response text faster than modern GPU LLMs.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'Dense search cannot run on Linux servers without specialized FPGA hardware.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
        ]
    ],
    [
        'name' => 'Cross-Encoder Rerankers',
        'text' => 'What is the key architectural difference between a Bi-Encoder embedding model and a Cross-Encoder reranker in information retrieval?',
        'feedback' => 'Bi-encoders embed query and document independently, while cross-encoders compute cross-attention between query and document simultaneously.',
        'answers' => [
            ['text' => 'Bi-encoders encode query and document into separate vectors independently (fast search), while cross-encoders pass query and document together through full cross-attention (slower, but higher scoring accuracy).', 'fraction' => 1.0, 'feedback' => 'Correct! That is why cross-encoders are used as a second-stage reranker on top candidates.'],
            ['text' => 'Cross-encoders do not use neural networks; they use regex pattern matching.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'Bi-encoders only work for computer vision models, while cross-encoders work for text.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'Cross-encoders require quantum computing hardware to invert matrix multiplication.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
        ]
    ],
    [
        'name' => 'Hallucination Mitigation in RAG',
        'text' => 'Which of the following techniques is most effective in preventing LLM hallucinations when generating answers from retrieved documentation?',
        'feedback' => 'Strict system prompt constraints, citations, and temperature reduction.',
        'answers' => [
            ['text' => 'Setting lower temperature (e.g. 0.0 - 0.2), enforcing strict system prompt grounding ("Only answer from context; state unknown if missing"), and requiring direct citations.', 'fraction' => 1.0, 'feedback' => 'Correct! Deterministic sampling and prompt bounding minimize confabulation.'],
            ['text' => 'Setting temperature to 1.9 to maximize model creativity and diversity.', 'fraction' => 0.0, 'feedback' => 'Incorrect. High temperature increases hallucinations.'],
            ['text' => 'Omitting the retrieved documents from the prompt to avoid confusing the model.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
            ['text' => 'Running the prompt through a spellchecker five consecutive times.', 'fraction' => 0.0, 'feedback' => 'Incorrect.'],
        ]
    ]
];
$quiz2 = create_quiz_activity($course2, $c2_sec4, 'Certified AI & Large Language Model Engineer Exam', $quiz2_intro, $quiz2_questions, 75);

// Completion criteria for Course 2
setup_course_completion_criteria($course2, [
    ['modname' => 'assign', 'cmid' => $assign2['cm']->id],
    ['modname' => 'quiz', 'cmid' => $quiz2['cm']->id],
]);

// Enrol users
enrol_course_users($course2);

// Rebuild course cache
rebuild_course_cache($course2->id);

// Record student grades and completions
$all_cms_c2 = [$c2_p1['cm'], $c2_p2['cm'], $c2_p3['cm'], $assign2['cm'], $quiz2['cm']];
echo "  [Grades & Completion] Recording Awadhesh Verma (user 3) completion on Course 2...\n";
record_student_grade_and_completion($course2, 3, $assign2, $quiz2, $all_cms_c2, 96.0, 95.0);

echo "  [Grades & Completion] Recording Gautam Jaiswal (user 5) completion on Course 2...\n";
record_student_grade_and_completion($course2, 5, $assign2, $quiz2, $all_cms_c2, 89.0, 85.0);

echo "  Course 2 setup complete! Awadhesh progress: " . \core_completion\progress::get_course_progress_percentage($course2, 3) . "%\n\n";

// ==========================================
// Polish existing courses (10, 11, 12)
// ==========================================
$other_courses = $DB->get_records_list('course', 'id', [10, 11, 12]);
foreach ($other_courses as $oc) {
    $oc->enablecompletion = 1;
    $oc->showcompletionconditions = 1;
    $oc->showgrades = 1;
    $DB->update_record('course', $oc);
    enrol_course_users($oc);
    attach_course_image($oc->id, $oc->fullname);
    rebuild_course_cache($oc->id);
    echo "  Updated completion & enrolments for course ID {$oc->id} ({$oc->shortname})\n";
}

// Purge Moodle caches so new courses appear immediately
purge_all_caches();

echo "\n=== All courses seeded, graded, and completion verified! ===\n";
