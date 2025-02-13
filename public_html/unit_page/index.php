<?php
    require_once '../../php/functions.php';
    
    require_once DOCUMENT_ROOT . 'php/initialise.php';

    if (isset($_GET['uid'])) {
        $unit_id = sanitise_string($pdo, $_GET['uid']);

        $is_valid_id = validate_id($pdo, $unit_id, 'unit');
        
        if ($is_valid_id == '') { 
            $stats = get_unit_stats($pdo, $unit_id);
            increment_access($pdo, $unit_id, 'unit');
            $title = $stats['name'] . ": The Holonet Archives";
            display_header($title, $randstr, $logged_in_as, $logged_in, $privilege);
            echo "<br  />";

            echo <<<_END
                            <div class='unit'>
            _END;
            
            display_unit_stats($stats);

            echo <<<_END
                            </div>
                        </div>
            _END;
        } else {
            display_header($title, $randstr, $logged_in_as, $logged_in, $privilege);
            echo "<h4 class='centre'>$is_valid_id</h4></div>";
        }
    }
    else {
        display_header($title, $randstr, $logged_in_as, $logged_in, $privilege);
        echo "<h4 class='centre'>Please specify a unit to lookup stats for.</h4></div>";
    }

    include_once DOCUMENT_ROOT . 'php/footer.php';