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
 * Defines the settings page of local_cohort_automation
 *
 * @package    local_cohort_automation
 * @copyright  2014 NetSpot Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

require_once('mappings_form.php');
require_once('locallib.php');

// Be mindful of security.
require_login();
$context = context_system::instance();
require_capability('moodle/cohort:manage', $context);

// Setup the admin page.
admin_externalpage_setup('cohort_automation');

// Determine what action, if any, to take.
$action = optional_param('action', null, PARAM_ALPHANUMEXT);
$error = null;

if ($action == 'add') {
    // Add a new mapping.

    require_sesskey();

    $record = new stdClass();
    $record->cohortid   = required_param('cohortid', PARAM_INT);
    $record->profilefieldid = required_param('profilefieldid', PARAM_INT);
    $record->regex = required_param('regex', PARAM_TEXT);

    // Stop whitespace inadvertantly making regex fail.
    $record->regex = trim($record->regex);

    try {
        $DB->insert_record('local_cohort_automation', $record);

        redirect(new moodle_url('/local/cohort_automation/mappings.php'));
    } catch (Exception $e) {
        $error = get_string('recordexists', 'local_cohort_automation');
    }

}

if ($action == 'delete') {
    // Delete a mapping.

    require_sesskey();

    $mappingid = required_param('id', PARAM_INT);

    try {
        // Get the details of this mapping.
        $mappingdetails = $DB->get_records('local_cohort_automation', array('id' => $mappingid));

        if (count($mappingdetails) > 0) {
            $mapping = array_shift($mappingdetails);
        } else {
            $error = get_string('recordnotfound', 'local_cohort_automation');
        }
    } catch (Exception $e) {
        $error = get_string('recordnotfound', 'local_cohort_automation');
    }

    // Use the mapping if it was found.
    if (isset($mapping)) {
        try {

            $users = get_users_in_cohort($mapping->cohortid, $mapping->profilefieldid, $mapping->regex);

            // Remove the users from the cohort..
            require_once(dirname(__FILE__) . '/../../cohort/lib.php');

            foreach ($users as $user) {
                cohort_remove_member($mapping->cohortid, $user->id);
            }

            // Delete the mapping record itself.
            $DB->delete_records('local_cohort_automation', array('id' => $mappingid));

            redirect(new moodle_url('/local/cohort_automation/mappings.php'));
        } catch (Exception $e) {
            $error = get_string('errorremovemembers', 'local_cohort_automation');
        }
    }
}


// Output the page header.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_cohort_automation'), 1, '', '');
echo $OUTPUT->heading(get_string('newmappingheader', 'local_cohort_automation'), 2, '', '');

// Output the new mapping form.
$settingsform = new mappings_settings_form(null, array('error' => $error));

$settingsform->display();

// List all existing mappings.
echo $OUTPUT->heading(get_string('existingmappingheader', 'local_cohort_automation'), 2, '', '');

$table = new html_table();
$table->head = array(
    get_string('cohorttable', 'local_cohort_automation'),
    get_string('profilefieldtable', 'local_cohort_automation'),
    get_string('membercounttable', 'local_cohort_automation'),
    get_string('regextable', 'local_cohort_automation'),
    get_string('deletetable', 'local_cohort_automation')
);

$records = get_cohort_mappings();
$profilefields = get_profile_fields();

if (count($records) > 0) {

    $linktext = get_string('deletelink', 'local_cohort_automation');

    $mappings = array();

    foreach ($records as $record) {
        $linkurl = new moodle_url(
            '/local/cohort_automation/mappings.php',
            array(
                'action' => 'delete',
                'id' => $record->id,
                'sesskey' => sesskey()
            )
        );

        $mappings[] = array(
             $record->name,
             $profilefields[$record->profilefieldid],
             $DB->count_records('cohort_members', array('cohortid' => $record->cohortid)),
             $record->regex,
             $OUTPUT->action_link(
                 $linkurl,
                 $linktext
             )
         );
    }

    $table->data = $mappings;
}

echo html_writer::table($table);

// Output the page footer.
echo $OUTPUT->footer();
