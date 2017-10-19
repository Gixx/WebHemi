<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

$progressId = $_GET['progress_id'] ?? '_';
$progressPath = __DIR__.'/data/progress/';

if ($progressId && file_exists($progressPath.$progressId.'.json')) {
    $data = file_get_contents($progressPath.$progressId.'.json');
} else {
    $data = ['error' => 'no progress found'];
    $data = json_encode($data);
}

header('Content-Type: application/json');
echo $data;
