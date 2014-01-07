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
 * Defines the settings form of local_cohort_automation
 *
 * @package    local_cohort_automation
 * @copyright  2014 NetSpot Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/formslib.php");
require_once('locallib.php');

// Define a form to collect settings for a new mapping.
class mappings_settings_form extends moodleform {

    // Define the form.
    public function definition() {
        global $DB;

        $mform = $this->_form;

        // Show an error message and not the form if required.
        if ($this->_customdata['error'] != null) {
            $mform->addElement('html',
                html_writer::start_span('error')
                . $this->_customdata['error']
                . html_writer::end_span());
        }

        // Only try to show the form if regular expressions...
        // Ore supported by the database.
        if (!$DB->sql_regex_supported()) {
            $mform->addElement('html',
                html_writer::start_span('error')
                . get_String('regexnotsupported', 'local_cohort_automation')
                . html_writer::end_span());
            return;
        }

        // Get a list of system wide cohorts.
        $context = context_system::instance();

        $rs = $DB->get_records('cohort', array('contextid' => $context->id), 'name', 'id, name');
        $cohorts = array(0 => '');

        // Only show the form if there are cohorts available.
        if (count($rs) == 0) {
            $mform->addElement('html',
                html_writer::start_span('error')
                . get_string('nocohortsfound', 'local_cohort_automation')
                . html_writer::end_span());
            return;
        }

        foreach ($rs as $r) {
            $cohorts[$r->id] = $r->name;
        }

        $mform->addElement('select', 'cohortid', get_string('cohortid', 'local_cohort_automation'), $cohorts);
        $mform->addHelpButton('cohortid', 'cohortidhelp', 'local_cohort_automation');
        $mform->addRule('cohortid', get_string('required'), 'required', null, 'client');
        $mform->addRule('cohortid', get_string('required'), 'nonzero', null, 'client');
        $mform->setType('cohortid', PARAM_INT);

        $mform->addElement('select', 'profilefieldid',
            get_String('profilefieldid', 'local_cohort_automation'), get_profile_fields());
        $mform->addHelpButton('profilefieldid', 'profilefieldidhelp', 'local_cohort_automation');
        $mform->addRule('profilefieldid', get_string('required'), 'required', null, 'client');
        $mform->addRule('profilefieldid', get_string('required'), 'nonzero', null, 'client');
        $mform->setType('profilefieldid', PARAM_INT);

        $mform->addElement('text', 'regex', get_string('regex', 'local_cohort_automation'));
        $mform->addHelpButton('regex', 'regexhelp', 'local_cohort_automation');
        $mform->addRule('regex', get_string('required'), 'required', null, 'client');
        $mform->setType('regex', PARAM_TEXT);

        $mform->addElement('hidden', 'action', 'add');
        $mform->setType('action', PARAM_ALPHANUMEXT);

        $mform->addElement('submit', 'submitbutton', get_string('savechanges'));
    }
}
