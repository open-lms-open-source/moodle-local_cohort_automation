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
 * English strings for local_cohort_automation
 *
 * @package    local_cohort_automation
 * @copyright  2014 NetSpot Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'NetSpot Cohort Automation';
$string['local_auto_cohort_maint'] = 'NetSpot Cohort Automation';

$string['newmappingheader'] = 'Add New Mapping';

$string['cohortid'] = 'Cohort';
$string['cohortidhelp'] = 'Select a Cohort';
$string['cohortidhelp_help'] = 'Select a Cohort that user should be added to if their username matches the specified pattern below';

$string['profilefieldid'] = 'User Profile Field';
$string['profilefieldidhelp'] = 'Select a User Profile Field';
$string['profilefieldidhelp_help'] = 'Select a Profile field that will be matched against using the pattern specified below';

$string['regex'] = 'Regular Expression';
$string['regexhelp'] = 'Enter a Regular Expression';
$string['regexhelp_help'] = 'Enter a regular expression that will be used to match against the username of a user';

$string['nocohortsfound'] = 'Please add at least one Cohort at the system context level before using this plugin';
$string['regexnotsupported'] = 'Regular expressions must be supported by your database platform for you to use this plugin';

$string['existingmappingheader'] = 'Existing Mappings';

$string['recordexists'] = 'A mapping matching the settings provided already exists';
$string['recordnotfound'] = 'A mapping matching the specified id does not exist';
$string['errorremovemembers'] = 'An error occured while deleting the cohort members';

$string['cohorttable'] = 'Cohort Name';
$string['profilefieldtable'] = 'Profile Field';
$string['membercounttable'] = 'Cohort Size';
$string['regextable']  = 'Regular Expression';
$string['deletetable'] = 'Delete';
$string['deletelink'] = 'Delete Mapping';


$string['save'] = 'Save';
