<?php

function fetchPath(string $path_to_fetch) {
    global $store_location;

    // trim the leading "/" from the path
    $local_path = $store_location . substr($path_to_fetch, 1);

    // add index, if its a folder
    if (substr($local_path, -1) == "/") {
        $local_path = $local_path . 'index.json';
    }
    else {
        $local_path = $local_path . '.json';
    }

    // load that json and convert it to a php thing
    if (file_exists($local_path)) {
        $json = file_get_contents($local_path);
        $as_array = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array("type" => "Not Found", "name" => $path_to_fetch);
        }
        return $as_array;
    }
    else {
        return array("type" => "Not Found", "name" => $path_to_fetch);
    }
}