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
 * @package    block_enrolltranscripts
 * @copyright  2013 eabyas.in   
 * @author     Niranjan <niranjan.in>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


 /* get the conformation of the completion */
function getconform($user,$courseid) {
  global $DB;
  $course = $DB->get_record('course', array('id' => $courseid));
  $info = new completion_info($course);
  $completions = $info->get_completions($user);
  $coursecomplete = $info->is_course_complete($user);
  $criteriacomplete = $info->count_course_user_data($user);
  return $coursecomplete;
 
 }
   
 /* end of the conformation function */
  

//my learning functon which gives the current enrollments--by niranjan

function mylearningcourses($userid){
global $CFG, $DB;
// query to get the current enrollemnts 
$enrolledcourses = $DB->get_recordset_sql('SELECT c.id,u.firstname, ra.timemodified,c.fullname FROM {course} AS c 
    JOIN {context} AS ctx ON c.id = ctx.instanceid 
    JOIN {role_assignments} AS ra ON ra.contextid = ctx.id 
    JOIN {user} AS u ON u.id = ra.userid  
    WHERE u.id ='.$userid.' AND c.category>0');
//display the header of the tables 
$table = new html_table();
$table->head  = array(
get_string('enrolleddate', 'block_enrolltranscripts'),get_string('coursename', 'block_enrolltranscripts'),get_string('launchcourse', 'block_enrolltranscripts'));
$table->size  = array('20%', '20%', '20%','20%');
$table->align = array('left', 'left', 'left', 'center');
$table->width = '99%';

$data = array();									  
				
foreach($enrolledcourses as $usercourses) {
///Loop for the enrolled course starts here 
$line = array();
$courseid = $usercourses->id;
$coursename = $usercourses->fullname; 
$enrolled_date=$usercourses->timemodified;
$enrolled_date = userdate($enrolled_date, get_string('strftimedatefullshort'));
$enroll = $DB->get_record_sql('SELECT * FROM {course_completions} AS c WHERE c.userid ='.$userid.' AND c.course='.$courseid.'');
$coursecompletion=getconform($userid,$courseid);
if(!$coursecompletion) {    
$line[]=$enrolled_date;
$line[]='<a  title="Course Name:'.$coursename.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$courseid.'">'.$coursename.'</a>';
$line[]='<a  title="Course Name:'.$coursename.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$courseid.'">Launch Course</a>';

 }
    $data[] = $line;
  
 }    
  

$table->data  = $data;
echo html_writer::table($table);
  
}


/* get the grades */
function getmygrades($user,$course){
global $CFG,$DB;
$Mytranscripts = $DB->get_recordset_sql('SELECT c.fullname,c.id,gg.finalgrade,gg.timemodified FROM 
    {course} AS c,
    {grade_items} AS gi,
    {grade_grades} AS gg,
    {user} AS u 
    WHERE u.id ='.$user.' AND c.id='.$course.' AND c.id = gi.courseid AND gg.itemid = gi.id AND u.id = gg.userid AND gi.itemtype="mod"');
foreach($Mytranscripts as $transcript){
  return $transcript->finalgrade;
  }
}
  /* end of the grades list */

/* get my certificate */
function getmycertificate($userid,$courseid) {
global $CFG,$DB;
$certificate=$DB->get_records_sql('SELECT ce.id,cmc.coursemoduleid,ce.printhours FROM 
    {course_modules_completion} AS cmc,
    {course_modules} AS cm,{certificate} AS ce,
    {course} AS c 
    WHERE c.id='.$courseid.' AND c.id=ce.course AND ce.id=cm.instance AND cm.id=cmc.coursemoduleid AND cmc.userid='.$userid.' ');
    foreach($certificate as $Mycertificate){
    $output=array();
    $output[0]=$Mycertificate->id;
    $output[1]=$Mycertificate->coursemoduleid;
    $output[2]=$Mycertificate->printhours;
    return $output;
    }

}

/* end of the certificate function */

/*issued certificate */
function getcertissued($userid,$course){
global $CFG,$DB;
$getissued=$DB->get_records_sql('SELECT cm.id FROM {course_modules} AS cm,{certificate_issues} AS ci,{certificate} AS c where cm.course='.$course.' AND cm.module=20 AND cm.course=c.course AND c.id=ci.certificateid AND ci.userid='.$userid.'');
   foreach($getissued as $issue){
   $output=array();
   $output=$issue->id;
   return $output;
    }
}
/* end of the certificate issued */
 
/* print the transcript page-by niranjan--eabyas */
function mytranscripts($userid){
global $CFG, $DB;
require_once("{$CFG->libdir}/completionlib.php");
echo '<table class="generaltable" width="99%" >
      <tr style="birder:1px solid #000">		  
      	<th class="header c0" style="text-align:left;width:20%;" scope="col">Completed Dates</th>
        <th class="header c1" style="text-align:left;width:20%;" scope="col">Course Name</th>
        <th class="header c2" style="text-align:left;width:20%;" scope="col">Certificate</th>	
        <th class="header c3" style="text-align:left;width:30%;" scope="col">Activities Completed</th>
     </tr>';
$MyCompletions=$DB->get_recordset_sql('SELECT c.fullname,c.id,cc.timecompleted,cc.userid,cc.course,cc.timestarted,cc.timeenrolled FROM {course_completions} cc,{course} AS c WHERE cc.userid='.$userid.' AND cc.course=c.id');
foreach($MyCompletions as $completions){
 //Loop for the Transcript 
$courseid=$completions->id;
$coursename = $completions->fullname;
$completeddate = $completions->timecompleted; 
$startdate=$completions->timestarted;
$timeenrolled=$completions->timeenrolled;
//checking the completed date .. It is dependent on the three things for a course 
$coursecompletion=getconform($completions->userid,$courseid);
//main condition starts end of  the completion checking... */
if($coursecompletion) {
    if(!$completeddate){
       if(!$startdate) {  
         $transcripttime=$completions->timeenrolled;
        }
        else {
           $transcripttime=$completions->timestarted;
        }
    }
    else {
         $transcripttime=$completions->timecompleted;
    }
 //converting the date to the user readable format
$completeddate = userdate($transcripttime, get_string('strftimedatefullshort'));
 /* Query to get the list of completed courses and there certificate list */
$finalgrade=getmygrades($completions->userid,$completions->course);
$getcertificate=array();
$getcertificate=getmycertificate($completions->userid,$completions->course);
$getissued=getcertissued($completions->userid,$completions->course);       
 echo '<tr >';
 echo '<td class="cell c0">'.$completeddate.'</td>';
 echo '<td class="cell c1"> <a style="color:#007EBA" href="'.$CFG->wwwroot.'/course/view.php?id='.$courseid.'" class="mycourse" style="color:#27292B;">'.$coursename.'</a></td>';
      
 /* Check if there are any entries */
 if($getissued){
 $cid=  $getissued;
  echo '<td class="cell c2">'; 
  echo '<a style="color:#007EBA" href="'.$CFG->wwwroot.'/mod/certificate/view.php?id='.$cid.'&action=get" target="_blank">View Certificate </a>';
  echo '</td>';
 }
 else if($getcertificate && !$getissued) {
  echo '<td class="cell c2">'; 
   $cid=  $getissued;
  echo '<a style="color:#007EBA" href="'.$CFG->wwwroot.'/course/view.php?id='.$courseid.'" >Please click on the certificate in the course to generate your certificate. </a>';
  echo '</td>';
  }
 else {
   echo '<td class="cell c2">'; 
   echo 'No Certificate Available';
   echo '</td>';
  }
 		
  echo '<td class="cell c3">';
  $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
  $info = new completion_info($course);
  $completions = $info->get_completions($userid);
  $rows = array();

    // Loop through course criteria
 foreach ($completions as $completion) {
        $criteria = $completion->get_criteria();
        $row = array();
        $row['type'] = $criteria->criteriatype;
        $row['status'] = $completion->get_status();
        $row['complete'] = $completion->is_complete();
        $row['timecompleted'] = $completion->timecompleted;
        $row['details'] = $criteria->get_details($completion);
        $rows[] = $row;
    }
 foreach ($rows as $row) {
   echo $row['details']['criteria'].'('.$row['status'].')<br/>';
 }
echo '</td>';
echo '</tr>';
   }	  
 }/*End of the main condition */
         

  
/* end of  the completion loop */
   echo "</table>";
       
}