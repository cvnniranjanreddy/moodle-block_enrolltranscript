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
 *
 * @package    block_enrolltranscript
 * @copyright  2013 eabyas.in
 * @author     Niranjan <niranjan@eabyas.in>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class block_enrolltranscripts extends block_list {
    public function init() {
        $this->title = get_string('pluginname', 'block_enrolltranscripts');
    }


public function get_content() {
global $CFG,$OUTPUT;
    if ($this->content !== null) {
      return $this->content;
    }
 $this->content  =  new stdClass;
$this->content->items=array();
$this->content->icons=array();
$this->content->icons[] = '<img src="'.$OUTPUT->pix_url('i/navigationitem') . '" class="iconsmall" alt="" />';
$this->content->items[]   = '<a href ="'.$CFG->wwwroot.'/blocks/enrolltranscripts/index.php" >'.get_string('enrollment','block_enrolltranscripts').'</a>';
$this->content->icons[] = '<img src="'.$OUTPUT->pix_url('i/navigationitem') . '" class="iconsmall" alt="" />';
$this->content->items[]   = '<a href ="'.$CFG->wwwroot.'/blocks/enrolltranscripts/transcript.php" >'.get_string('transcript','block_enrolltranscripts').'</a>';

 
    return $this->content;
  }
  





}//main class ending

?>
