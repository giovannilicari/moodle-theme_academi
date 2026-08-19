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
 * Course renderer.
 *
 * @package theme_academi
 * @copyright 2023 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author LMSACE Dev Team
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_academi\output;

use html_writer;
use moodle_url;
use custom_menu;


/**
 * The core course renderer.
 *
 * Can be retrieved with the following:
 * $renderer = $PAGE->get_renderer('core','course');
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Returns the moodle_url for the favicon.
     *
     * This renderer function is copied and modified from /lib/outputrenderers.php
     *
     * @since Moodle 2.5.1 2.6
     * @return moodle_url The moodle_url for the favicon
     */
    public function favicon() {
        $logo = $this->image_url('favicon', 'theme');
        if (!empty($this->page->theme->settings->favicon)) {
            $logo = $this->page->theme->setting_file_url('favicon', 'favicon');
        } else {
            $logo = parent::favicon();
        }
        return $logo;
    }

    /**
     * Footer info links.
     * @return string
     */
    public function footer_infolinks() {
        $infolink = theme_academi_get_setting('infolink');
        $menu = new custom_menu($infolink, current_language());
        $haslangmenu = $this->lang_menu() != '';

        if (!$menu->has_children() && !$haslangmenu) {
            return '';
        }

        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('theme_academi/custom_menu_item', $context);
        }
        return $content;
    }


/**
     * Aggiunge uno script per precompilare il messaggio
     * nel pop-up "Sending a message to selected course participants"
     * con il nome del corso corrente.
     */
    public function standard_head_html() {
        global $PAGE, $COURSE, $USER;

        $html = parent::standard_head_html();

        // Applichiamo lo script solo nella pagina dei partecipanti o dei messaggi
        $url = $PAGE->url->out_as_local_url();
        if (strpos($url, '/user/index.php') !== false || strpos($url, '/message/index.php') !== false) {

            // Nome del corso o fallback
            $coursename = !empty($COURSE->id) && $COURSE->id != SITEID
                ? addslashes(format_string($COURSE->fullname))
                : 'Sito principale';

            // Testo predefinito
            $defaultmessage = "Gentili studenti,\\n\\n"
                . "questo messaggio riguarda il corso: {$coursename}.\\n\\n"
                . "Cordiali saluti,\\n{$USER->firstname} {$USER->lastname}";

            // Script JS che intercetta l’apertura del pop-up
            $js = <<<JS
                require(['jquery'], function($) {
                    $(document).ready(function() {
                        const observer = new MutationObserver(() => {
                            const textarea = $('#bulk-message');
                            if (textarea.length && !textarea.val().trim()) {
                                textarea.val("{$defaultmessage}");
                            }
                        });
                        observer.observe(document.body, { childList: true, subtree: true });
                    });
                });
            JS;

            $PAGE->requires->js_amd_inline($js);
        }

        return $html;
    }


}
