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
 * Library of interface functions and constants for module local_cohort_automation
 *
 * @package    local_cohort_automation
 * @copyright  2014 NetSpot Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Function to be run periodically according to the moodle cron
 *
 * Identify users not in the configured cohorts and add them as required
 *
 * @return boolean
 **/
function local_cohort_automation_cron () {

    global $DB;

    // Check to ensure regex is supported.
    if (!$DB->sql_regex_supported()) {
        mtrace('Unable to run as regex is not supported by this database');
        return;
    }

    require_once('locallib.php');
    require_once(dirname(__FILE__) . '/../../cohort/lib.php');

    // Get a list of mappings.
    $mappings = get_cohort_mappings();

    if (count($mappings) > 0) {

        // Loop through each mapping.
        foreach ($mappings as $mapping) {

            $userrecords = get_users_not_in_cohort($mapping->cohortid, $mapping->profilefieldid, $mapping->regex);

            if ($userrecords->valid()) {
                $count = 0;

                // Users were found that need to be added to the cohort.
                foreach ($userrecords as $userrecord) {
                    // Add the user to the cohort.
                    cohort_add_member($mapping->cohortid, $userrecord->id);
                    $count++;
                }

                // Output some useful info.
                mtrace('Found ' . $count . ' records that were updated for mapping ' . $mapping->id);
            }

            $userrecords->close();
        }
    }

    return true;
}
