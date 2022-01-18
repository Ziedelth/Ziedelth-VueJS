<?php

require_once '../config.php';
header('Access-Control-Allow-Origin: *');

http_response_code(201);
echo '{"maintenance":' . (MAINTENANCE ? 'true' : 'false') . '}';