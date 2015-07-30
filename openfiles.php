<?php
require_once(__DIR__."/function.php");

$html="";
$findgit=isset($_POST['findgit'])?$_POST['findgit']:"";

if ($findgit!=="") {
  // выбор ветки проекта
  $html.="<br>Проект: <strong>".$findgit."</strong><br>";
  $json=cURL("https://api.github.com/repos/".$findgit."/branches");
  if (isset($json['message'])) $html.="<br><strong>message: ".$json['message']."</strong><br>";
  else {
    if ($json!==""&&count($json)>0) {
	  $html.="<br><table border='0' id='table_git'>";
      $html.="<tr align='center'><td>N</td><td>Ветки</td></tr>";
      foreach ($json as $key => $value) {
        if (isset($value['commit']['sha'])) $html.="<tr><td>".++$key."</td><td><a href='#' id='github_curl' title='".$value['commit']['sha']."'>".$value['name']."</a></td>";
      }
	  $html.="</table>";
	} else $html.="<br><strong>У проекта нет ветвей!</strong><br>";
  }
} else $html.="<br><strong>Введите имя проекта!</strong><br>";
$html.=<<<HTML
<script type="text/javascript">
$("#loading_git").hide();
$('a#github_curl').click(function() {
    $("#loading_git").text('Подождите, идет обработка запроса ...');
    $("#loading_git").css('left', (($(window).width()/2)-($("#loading_git").width()/2))+'px');
    $("#loading_git").slideDown(100);
    $("#loading").slideUp(200);
    var var_sha=$(this).attr('title');
    $.post('find',{ajax:'infofiles',findgit:'{$findgit}',sha:var_sha},function(data){ $('#loading').html(data);$("#loading").slideDown(200);}); 
    return false;
});
</script>
HTML;
$html=iconv('UTF-8','WINDOWS-1251',$html);
echo $html;
unset($json);
?>
