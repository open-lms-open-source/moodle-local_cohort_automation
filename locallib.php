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
 * Internal library of functions for module local_cohort_automation
 *
 * @package    local_cohort_automation
 * @copyright  2014 NetSpot Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * retrieve a list of cohort mappings
 *
 * @return array of cohort mappings
 */
function get_cohort_mappings() {
    global $DB;

    return $DB->get_records_sql(
        'SELECT a.id, a.cohortid, b.name, a.regex, a.profilefieldid
         FROM {local_cohort_automation} a, {cohort} b
         WHERE a.cohortid = b.id
         ORDER BY b.name'
    );
}

/**
 * retrieve a list of users not in the specified cohort
 * with user names matching the supplied regex
 *
 * @param $cohortid the id number of the cohort
 * @param $profilefieldid the id number of the profile field
 * @param $regex the regex to apply to the username
 *
 * @return recordset of users not in the cohort
 */
function get_users_not_in_cohort($cohortid, $profilefieldid, $regex) {
    global $DB;

    $fields = get_profile_fields(false);

    $sql = 'SELECT u.id
            FROM {user} u
            WHERE u.id NOT IN (SELECT cm.userid
                               FROM {cohort_members} cm
                               WHERE cohortid = ?)
            AND u.' . $fields[$profilefieldid] . ' ' . $DB->sql_regex(true) . ' ?
            AND u.deleted <> 1
            AND u.suspended <> 1';

    try {
        return $DB->get_recordset_sql($sql, array($cohortid, $regex));
    } catch (Exception $e) {
        mtrace('Exception thrown while trying to find users not in a cohort: ' . $e->getMessage());
        return array();
    }
}

/**
 * retrieve a list of users who are in the specified cohort
 * with user names matching the supplied regex
 *
 * @param $cohortid the id number of cohorot
 * @param $profilefieldid the id number of the profile field
 * @param $regex the regex to apply to the username
 * 
 * @return recordset of users in the cohort
 */
function get_users_in_cohort($cohortid, $profilefieldid, $regex) {
    global $DB;

    $fields = get_profile_fields(false);

    $sql = 'SELECT u.id
            FROM {user} u
            WHERE u.id IN (SELECT cm.userid
                               FROM {cohort_members} cm
                               WHERE cohortid = ?)
            AND u.' . $fields[$profilefieldid] . ' ' . $DB->sql_regex(true) . ' ?';

    try {
        return $DB->get_recordset_sql($sql, array($cohortid, $regex));
    } catch (Exception $e) {
        mtrace('Exception thrown while trying to find users in a cohort: ' . $e->getMessage());
        return array();
    }
}

/**
 * retrieve a list of profile fields that can be matched against
 *
 * @param $fordisplay generate the list of fields for display
 *
 * @return array of possible profile fields
 */
function get_profile_fields($fordisplay=true) {

    // Define master array.
    /*
     * index:   a unique non repeating index number
     * display: the name of the field for display
     * field:   tha name of the field for queries
     */
    $master = array(
        array(
            'index' => '1',
            'display' => 'Username',
            'field' => 'username'
           ),
    );

    // Output required array based on master array.
    if ($fordisplay) {
        $tmp = array();

        foreach ($master as $item) {
            $tmp[$item['index']] = $item['display'];
        }
    } else {
        $tmp = array();

        foreach ($master as $item) {
            $tmp[$item['index']] = $item['field'];
        }
    }

    return $tmp;
}
