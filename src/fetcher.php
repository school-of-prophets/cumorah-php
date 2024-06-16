<?php

if ($store_type == "LocalFiles")
    include_once("$src/fetcher-local.php");
// elseIf .. something else
//   include fetcher for something else
else   // default to local files ... perhaps soon, we default to sqlite
    include_once("$src/fetcher-local.php");

