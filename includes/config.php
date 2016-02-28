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

    /*
        LOG DIRECTORY
            The path to the root log directory on the server, use a full or relative path.
            *Note* Is it good practice to keep logs in a directory that is not publically accessable (E.G. not on the web server).
            Example: '/home/irc/logs/'
    */
    $CONFIG['LOG_DIRECTORY'] = '';

    /*
        LOG EXCLUDES
            An array of networks and channels to exclude for the log list.
            Using an asterisk (*) in place of a channel name will exclude the entire network.
            Example:
                array(
                    'network' => array('##channel1', '#channel2'),
                );
    */
    $CONFIG['LOG_EXCLUDES']  = array();
 ?>
