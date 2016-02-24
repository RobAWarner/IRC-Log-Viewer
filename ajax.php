<?php
    // Set content type to JSON
    header('Content-Type: application/json; charset=utf-8');

    define('IN_APP', true);

    // Include the config file.
    require_once('includes/config.php');

    // Function to return JSON
    function finish($Content, $Success = false) {
        echo json_encode(array('success'=>$Success, 'return'=>$Content));
        die();
    }

    // Check request
    if(!isset($_GET['fetch']) || empty($_GET['fetch']))
        finish('Required \'fetch\' parameter not set');

    switch((string)$_GET['fetch']) {
        case 'network-list':

            break;
        case 'channel-list':
            if(!isset($_GET['for_network']) || empty($_GET['for_network']))
                finish('Required parameter \'for_network\' not set');
            break;
        case 'log-list':
            if(!isset($_GET['for_network'], $_GET['for_channel']) || empty($_GET['for_network']) || empty($_GET['for_channel']))
                finish('Required parameter \'for_network\' or \'for_channel\' not set');
            break;
        case 'log':
            if(!isset($_GET['for_network'], $_GET['for_channel'], $_GET['for_logdate']) || empty($_GET['for_network']) || empty($_GET['for_channel'])|| empty($_GET['for_logdate']))
                finish('Required parameter \'for_network\', \'for_channel\' or \'for_logdate\' not set');
            break;
        default:
            finish('Invalid value for \'fetch\' parameter set');
            break;
    }
 ?>
