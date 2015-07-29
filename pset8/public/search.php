<?php
    require(__DIR__ . "/../includes/config.php");
    // get querystring params, urldecoding and trimming leading/trailing whitespace in the process
    $params = array_map('trim', explode(",", urldecode($_GET["geo"])));
    
    // build sql_query statement
    $sql_query = "SELECT * FROM places WHERE ";
    for ($i = 0, $count = count($params); $i < $count; $i++) {
        // if param is numeric, assume a postal code
        if (is_numeric($params[$i])) 
	{
            $sql_query .= 'POSTAL_CODE LIKE "' . htmlspecialchars($params[$i], ENT_QUOTES) . '%"';
        } 
	else 
	{
            $sql_query .= 
                '(PLACE_NAME  LIKE "' . htmlspecialchars($params[$i], ENT_QUOTES) . '%" OR ' . 
                 (strlen($params[$i]) <= 2 ? 'ADMIN_CODE1 LIKE "' . htmlspecialchars($params[$i], ENT_QUOTES) . '%" OR ' : "") . 
                 'ADMIN_NAME1 LIKE "' . htmlspecialchars($params[$i], ENT_QUOTES) . '%")';
        }
        
        if ($i < ($count - 1)) {
            $sql_query .= " AND ";
        }
    }
    // search database for places matching $_GET["geo"]
    $places = query($sql_query);
    
    // output places as JSON (pretty-printed for debugging convenience)
    header("Content-type: application/json");
    print(json_encode($places, JSON_PRETTY_PRINT));
?>
