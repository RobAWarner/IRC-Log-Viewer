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

    // Include the config file.
    require_once('config.php');

    // Check to ensure log directory config exists
    if(!isset($CONFIG) || !isset($CONFIG['LOG_DIRECTORY']))
        finish('Missing required configs');

    // Get the path separator for the OS
    if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        define('PATH_SEP', '\\');
    else
        define('PATH_SEP', '/');

    // Define variable for the real log directory
    define('LOG_DIRECTORY', realpath($CONFIG['LOG_DIRECTORY']).PATH_SEP);

    // Define variable for log file type/suffix
    define('LOG_SUFFIX', '.log');

    // Function to return JSON
    function finish($Content, $Success = false) {
        echo json_encode(array('success'=>$Success, 'return'=>$Content));
        die();
    }

    // Include log parser functions
    require_once('log-parser.php');

    // Function to determin if a network or channel should be excluded/hidden
    function is_excluded($Network, $Channel = '') {
        global $CONFIG;

        if(!isset($CONFIG['LOG_EXCLUDES']) || !is_array($CONFIG['LOG_EXCLUDES']))
            return false;

        // Is the entire network excluded
        if(!empty($Network)) {
            if(isset($CONFIG['LOG_EXCLUDES'][$Network]) && in_array('*', $CONFIG['LOG_EXCLUDES'][$Network]))
                return true;
        }

        // Is a specific channel excluded
        if(!empty($Network) && !empty($Channel)) {
            if(isset($CONFIG['LOG_EXCLUDES'][$Network]) && in_array($Channel, $CONFIG['LOG_EXCLUDES'][$Network]))
                return true;
        }

        return false;
    }

    // Function to get a list of network folders in log directory
    function GetNetworkList() {
        $Networks = array();

        // Get a list of directories
        $NetworkList = glob(LOG_DIRECTORY.'*', GLOB_ONLYDIR);

        foreach($NetworkList as $Network) {
            // Get HTML safe name
            $NetworkName = htmlspecialchars(basename($Network), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // Should this network be excluded?
            if(is_excluded($NetworkName))
                continue;

            $Networks[] = array('name'=>$NetworkName);
        }

        return array($Networks);
    }

    // Function to get a list of channels for a specific network folder in log directory
    function GetChannelList($ForNetwork) {
        $Channels = array();

        // Is this network excluded?
        if(is_excluded($ForNetwork))
            return 'You cannot view this network';

        // Does the directory for this network exist?
        if(!is_dir(LOG_DIRECTORY.$ForNetwork))
            return 'Network does not exist';

        // Get a list of channels
        $ChannelList = glob(LOG_DIRECTORY.$ForNetwork.PATH_SEP.'#*', GLOB_ONLYDIR);

        foreach($ChannelList as $Channel) {
            // Get HTML safe name
            $ChannelName = htmlspecialchars(basename($Channel), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // Should this channel be excluded?
            if(is_excluded($ForNetwork, $ChannelName))
                continue;

            // Number of logs in channel
            $NumberofLogs = count(glob($Channel.PATH_SEP.'*'.LOG_SUFFIX));

            // Get the channel hash prefix
            $Prefix = '';
            while(substr($ChannelName, 0, 1) === '#') {
                $Prefix .= '#';
                $ChannelName = substr($ChannelName, 1);
            }

            $Channels[] = array('name'=>$Prefix.$ChannelName, 'prefix'=>$Prefix, 'display_name'=>$ChannelName, 'log_count'=>$NumberofLogs);
        }

        return $Channels;
    }

    // Function to get a list of logs for a specific channel folder in log directory
    function GetLogList($ForNetwork, $ForChannel) {
        $Logs = array();

        // Is this channel or network excluded?
        if(is_excluded($ForNetwork, $ForChannel))
            return 'You cannot view this channel or network';

        // Does the directory exist for this channel
        if(!is_dir(LOG_DIRECTORY.$ForNetwork.PATH_SEP.$ForChannel))
            return 'This channel does not exist';

        // Get a list of logs
        $LogList = glob(LOG_DIRECTORY.$ForNetwork.PATH_SEP.$ForChannel.PATH_SEP.'*'.LOG_SUFFIX);

        foreach($LogList as $Log) {
            // Get HTML safe name
            $LogName = htmlspecialchars(substr(basename($Log), 0, -strlen(LOG_SUFFIX)), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // Convert name to a timestamp
            $LogDate = strtotime($LogName.' 00:00:00');

            $Logs[] = array('name'=>$LogName, 'timestamp'=>$LogDate);
        }

        return $Logs;
    }

    function GetLogLines($ForNetwork, $ForChannel, $ForLog) {
        $LogLines = array();

        // Is this channel or network excluded?
        if(is_excluded($ForNetwork, $ForChannel))
            return 'You cannot view this channel or network';

        // Does this log file exist
        if(!file_exists(LOG_DIRECTORY.$ForNetwork.PATH_SEP.$ForChannel.PATH_SEP.$ForLog.LOG_SUFFIX))
            return 'This log does not exist';

        // Read the log file
        $LogFileLines = file(LOG_DIRECTORY.$ForNetwork.PATH_SEP.$ForChannel.PATH_SEP.$ForLog.LOG_SUFFIX);
        if($LogFileLines === false)
            return 'There was an error reading the log file';

        // Reverse the array so as that the most recent lines are first
        $LogFileLines = array_reverse($LogFileLines);

        foreach($LogFileLines as $LogLine) {
            // Parse the log line to replace IRC formatting and URLS
            $LogLine = ParseLogLine($LogLine, $ForLog);
            if($LogLine !== false)
                $LogLines[] = $LogLine;
        }

        return $LogLines;
    }
?>
