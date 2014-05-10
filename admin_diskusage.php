<?php

require_once 'main.php';

if( !$_SESSION['loggedin'] ) die( "{ERROR}" );
if( !$_SESSION['admin'] ) die( "{ERROR}" );

function foldersize($path) {
  $total_size = 0;
  $files = scandir($path);

  foreach($files as $t) {
    if (is_dir(rtrim($path, '/') . '/' . $t)) {
      if ($t<>"." && $t<>"..") {
          $size = foldersize(rtrim($path, '/') . '/' . $t);

          $total_size += $size;
      }
    } else {
      $size = filesize(rtrim($path, '/') . '/' . $t);
      $total_size += $size;
    }
  }
  return $total_size;
}

$demos = foldersize("demos");
$logs = foldersize("logs");
echo round($demos/1000000000,3) . "GB (demos) + " . round($logs/1000000,1) . "MB (logs) = ". round(($demos+$logs)/1000000000,3). "GB (total)";
?>