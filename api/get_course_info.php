<?php

namespace tool_painelava;

if (!defined('NO_MOODLE_COOKIES')) {
    define('NO_MOODLE_COOKIES', true);
}

require_once('../../../../config.php');
require_once('../locallib.php');
require_once("servicelib.php");

class get_course_info_service extends \tool_painelava\service
{
    function do_call()
    {
        global $DB, $USER;

        $courseid = \tool_painelava\aget($_GET, 'courseid', 0);
        $username = strtolower(\tool_painelava\aget($_GET, 'username', ''));

        // Busca o curso
        $course = $DB->get_record('course', ['id' => $courseid], 'id, fullname, shortname, summary', MUST_EXIST);
        
        // Verifica se o usuário atual já está inscrito no curso
        $is_enrolled = false;
        if (!empty($username)) {
            $USER = $DB->get_record('user', ['username' => $username]);
            if ($USER) {
                $context = \context_course::instance($course->id);
                $is_enrolled = is_enrolled($context, $USER->id);
            }
        }

        return [
            "id" => $course->id,
            "fullname" => $course->fullname,
            "shortname" => $course->shortname,
            "summary" => trim(strip_tags($course->summary)), // Limpa o HTML do Moodle
            "is_enrolled" => $is_enrolled
        ];
    }
}