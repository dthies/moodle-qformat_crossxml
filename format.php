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
 * Code for switching question types when importing Moodle XML.
 *
 * @package    qformat_crossxml
 * @copyright  Daniel Thies <dethies@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/format/xml/format.php');

/**
 * Importer for Cross XML question format.
 *
 * See http://docs.moodle.org/en/Moodle_XML_format for a description of the format.
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_crossxml extends qformat_xml {

    public function provide_import() {
        return true;
    }

    public function provide_export() {
        return false;
    }

    // Override to change question object to short answer.
    public function import_multichoice($question) {
        $qo = parent::import_multichoice($question);
        $qo->qtype = 'shortanswer';
        for ($k = 0; $k < count($qo->answer); $k++) {
            if ($qo->answer[$k]['format'] == FORMAT_HTML) {
                $qo->answer[$k] = html_to_text($qo->answer[$k]['text']);
            } else {
                $qo->answer[$k] = $qo->answer[$k]['text'];
            }
        }
        return $qo;
    }

    // Override to change question object to multichoice.
    public function import_shortanswer($question) {
        $qo = parent::import_shortanswer($question);
        $qo->qtype = 'multichoice';
        $qo->single = 1;
        for ($k = 0; $k < count($qo->answer); $k++) {
            $qo->answer[$k] = array('text' => $qo->answer[$k], 'format' => FORMAT_PLAIN);
        }
        return $qo;
    }

    public function import_match($question) {
        $qo = parent::import_match($question);
        if (array_key_exists('ddmatch', core_component::get_plugin_list('qtype'))) {
            $qo->qtype = 'ddmatch';
            for ($k = 0; $k < count($qo->subanswers); $k++) {
                $qo->subanswers[$k] = array(
                    'text' => text_to_html($qo->subanswers[$k]),
                    'format' => FORMAT_HTML
                );
            }
        }
        return $qo;
    }

    protected function import_question($questionxml) {
        $questiontype = $questionxml['@']['type'];

        if (!array_key_exists($questiontype, core_component::get_plugin_list('qtype')) &&
                $questiontype != 'category' &&
                $questiontype != 'matching') {
            return null;
        }

        switch ($questiontype) {
            case 'multichoiceset':
            case 'oumultiresponse':
                $qo = $this->try_importing_using_qtype($questionxml, null, null, $questiontype);
                $qo->qtype = 'multichoice';
                $qo->fraction = $qo->correctanswer;
                $qo->single = 0;
                return $qo;
            case 'ddmatch':
                return $this->import_ddmatch($questionxml);
            default:
                return parent::import_question($questionxml);
        }
    }

    protected function import_ddmatch($questionxml) {
        $qo = $this->try_importing_using_qtype($questionxml, null, null, 'ddmatch');
        $qo->qtype = 'match';
        for ($k = 0; $k < count($qo->subanswers); $k++) {
            if ($qo->subanswers[$k]['format'] == FORMAT_HTML) {
                $qo->subanswers[$k] = html_to_text($qo->subanswers[$k]['text']);
            } else {
                $qo->subanswers[$k] = $qo->subanswers[$k]['text'];
            }
        }

        return $qo;
    }

    // Overwrite inherited to force XML input`.
    protected function try_importing_using_qtype($data, $question = null, $extra = null,
                $qtypehint = '') {
        $qtype = question_bank::get_qtype($qtypehint, false);

        $qo = $qtype->import_from_xml($data, null, $this);
        return $qo;

    }
}
