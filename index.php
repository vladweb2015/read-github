<?php
//phpinfo();
date_default_timezone_set("Etc/GMT-4");
//setlocale(LC_ALL, 'ru_RU');
define("SITE",__DIR__);
$ajax=isset($_POST['ajax'])?$_POST['ajax']:"";

if ($ajax=="") require_once(SITE."/html.php");
else if ($ajax=="findgit") require_once(SITE."/findgit.php");
else if ($ajax=="github_file") require_once(SITE."/openfiles.php");
else if ($ajax=="github_comment") require_once(SITE."/opencomments.php");
else if ($ajax=="infofiles") require_once(SITE."/infofiles.php");