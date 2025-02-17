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

namespace theme_academi\output;

use cm_info;
use coding_exception;
use context;
use context_module;
use html_table;
use html_table_cell;
use html_writer;
use mod_quiz\access_manager;
use mod_quiz\form\preflight_check_form;
use mod_quiz\question\display_options;
use mod_quiz\quiz_attempt;
use moodle_url;
use plugin_renderer_base;
use popup_action;
use question_display_options;
use mod_quiz\quiz_settings;
use renderable;
use single_button;
use stdClass;

/**
 * Visualizza il tasto per uscire da SEB
 *
 * @package   mod_quiz
 * @category  output
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_renderer extends \mod_quiz\output\renderer
{
    public function view_information($quiz, $cm, $context, $messages, bool $quizhasquestions = false)
    {
                global $USER, $DB;
                $output = '';

                // Controlla se SEB è attivo nel quiz
                $seb_enabled = !empty($quiz->seb_requiresafeexambrowser);

            // Controlla se l'utente ha già consegnato almeno un tentativo
                $attempts = quiz_get_user_attempts($quiz->id, $USER->id, 'finished', true);


                // Mostra il bottone solo se SEB è attivo nelle configurazioni del quiz
                if ( $attempts && $seb_enabled) {
                    $output .= html_writer::tag(
                        'a',
                        'ESCI DA SEB',
                        [
                            'href' => "/login/logout.php",
                            'style' => "margin:20px",
                            'class' => 'btn btn-primary' // Usa una classe Bootstrap per lo stile del pulsante
                        ]
                    );
                }

            // Output any access messages.
            if ($messages) {
                $output .= $this->box($this->access_messages($messages), 'quizinfo');

            }

            // Show number of attempts summary to those who can view reports.
            if (has_capability('mod/quiz:viewreports', $context)) {
                if (
                    $strattemptnum = $this->quiz_attempt_summary_link_to_reports(
                        $quiz,
                        $cm,
                        $context
                    )
                ) {
                    $output .= html_writer::tag(
                        'div',
                        $strattemptnum,
                        ['class' => 'quizattemptcounts']
                    );
                }
            }

            if (has_any_capability(['mod/quiz:manageoverrides', 'mod/quiz:viewoverrides'], $context)) {
                if ($overrideinfo = $this->quiz_override_summary_links($quiz, $cm)) {
                    $output .= html_writer::tag('div', $overrideinfo, ['class' => 'quizattemptcounts']);
                }
            }

            return $output;
    }

}