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

    // Set content type to JSON
    header('Content-Type: application/json; charset=utf-8');

    define('IN_APP', true);

    // Include the core file.
    require_once('includes/core.php');

    // Check request
    if(!isset($_GET['fetch']) || empty($_GET['fetch']))
        finish('Required parameter \'fetch\' not set');

    switch($_GET['fetch']) {
        case 'network-list':
            $NetworkList = GetNetworkList();
            if(!is_array($NetworkList))
                finish($NetworkList);
            else
                finish(array('networks'=>$NetworkList), true);

            break;

        case 'channel-list':
            if(!isset($_GET['for_network']) || empty($_GET['for_network']))
                finish('Required parameter \'for_network\' not set');

            $ChannelList = GetChannelList($_GET['for_network']);
            if(!is_array($ChannelList))
                finish($ChannelList);
            else
                finish(array('channels'=>$ChannelList), true);

            break;

        case 'log-list':
            if(!isset($_GET['for_network'], $_GET['for_channel']) || empty($_GET['for_network']) || empty($_GET['for_channel']))
                finish('Required parameter \'for_network\' or \'for_channel\' not set');

            $LogList = GetLogList($_GET['for_network'], $_GET['for_channel']);
            if(!is_array($LogList))
                finish($LogList);
            else
                finish(array('logs'=>$LogList), true);

            break;

        case 'log':
            if(!isset($_GET['for_network'], $_GET['for_channel'], $_GET['for_logdate']) || empty($_GET['for_network']) || empty($_GET['for_channel'])|| empty($_GET['for_logdate']))
                finish('Required parameter \'for_network\', \'for_channel\' or \'for_logdate\' not set');

            $LogLines = GetLogLines($_GET['for_network'], $_GET['for_channel'], $_GET['for_logdate']);
            if(!is_array($LogLines))
                finish($LogLines);
            else
                finish(array('loglines'=>$LogLines), true);

            break;

        default:
            finish('Invalid value for parameter \'fetch\' set');
            break;
    }
 ?>
