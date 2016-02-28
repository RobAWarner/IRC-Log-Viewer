<?php
    /*--------------------------------------------------------+
    | IRC Log Viewer                                          |
    | Copyright (C) 2016 https://monsterprojects.org          |
    +---------------------------------------------------------+
    | This program is free software and is released under     |
    | the terms of the GNU Affero General Public License      |
    | version 3 as published by the Free Software Foundation. |
    | You can redistribute it and/or modify it under the      |
    | terms of this license, which is included with this      |
    | software as LICENSE.txt or is viewable at               |
    | http://www.gnu.org/licenses/agpl-3.0.html               |
    +--------------------------------------------------------*/

    if(!defined('IN_APP')) die();

    // Regex for different log lines
    $RegNormalLine = "/^\[([0-9]{2}:[0-9]{2}:[0-9]{2})\]\s<([^>]*)>\s(.+)$/i";
    $RegActionLine = "/^\[([0-9]{2}:[0-9]{2}:[0-9]{2})\]\s(\*{3})\s(.+)$/i";

    function ParseLogLine($LogLine, $LineDate = '') {
        global $RegNormalLine, $RegActionLine;

        $LineArray = array();

        // Remove whitespace for line ends
        $LogLine = trim($LogLine);

        // Match log line type
        if(preg_match($RegNormalLine, $LogLine, $LineMatches) || preg_match($RegActionLine, $LogLine, $LineMatches)) {
            if(!count($LineMatches) == 4)
                return false;

            // Get line time and timestamp (if $LineDate is provided)
            $LineArray['time'] = $LineMatches[1];
            if(!empty($LineDate))
                $LineArray['timestamp'] = strtotime($LineDate.' '.$LineMatches[1]);

            // Get and make HTML safe the username
            $LineArray['user'] = htmlspecialchars($LineMatches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // Get and make HTML safe the line message
            $LineArray['text'] = htmlspecialchars($LineMatches[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');

            $LineArray['text'] = ReplaceURLS($LineArray['text']);
            $LineArray['text'] = ReplaceIRCFormatting($LineArray['text']);

            return $LineArray;
        }

        return false;
    }

    // Patterns and replacements for IRC colors/formatting
    $IRCFormattingPatterns = array('/00/', '/01/', '/02/','/03/', '/04/', '/05/', '/06/', '/07/', '/08/', '/09/', '/10/', '/11/', '/12/', '/13/','/14/','/15/','//', '//', '//', '//', '//');
    $IRCFormattingReplacements = array('<span class="irc-c00">', '<span class="irc-c01">', '<span class="irc-c02">', '<span class="irc-c03">', '<span class="irc-c04">', '<span class="irc-c05">', '<span class="irc-c06">', '<span class="irc-c07">', '<span class="irc-c08">', '<span class="irc-c09">', '<span class="irc-c10">', '<span class="irc-c11">', '<span class="irc-c12">', '<span class="irc-c13">', '<span class="irc-c14">', '<span class="irc-c15">', '<span class="irc-c">', '<span class="irc-bold">', '<span class="irc-normal">', '<span class="irc-underline">', '<span class="irc-italic">');

    // Replace IRC formatting in a line
    function ReplaceIRCFormatting($String) {
        global $IRCFormattingPatterns, $IRCFormattingReplacements;

        // Find IRC formatting and replace with start tags
        $String = preg_replace($IRCFormattingPatterns, $IRCFormattingReplacements, $String, -1, $ReplacementCount);

        // Add end tags
        if($ReplacementCount > 0) {
            for($i=0; $i<$ReplacementCount; $i++) {
                $String .= '</span>';
            }
        }

        return $String;
    }

    // Function to find and replace URL's with a clickable link
    function ReplaceURLS($String) {
        return preg_replace("/(([a-z0-9]{3,})\:\/\/|www\.)[a-z0-9\-\.]+\.[a-z0-9\-\.]+([a-z0-9\-\.\_\~:\?\/\\\#\[\]\@\!\$\&\'\(\)\*\+\,\;\=]+)?/i", "<a href=\"$0\" target=\"_blank\" rel=\"nofollow\">$0</a> ", $String);
    }
?>
