<?php
require_once(__DIR__."/function.php");

$html="";
$findgit=isset($_POST['findgit'])?$_POST['findgit']:"";
$sha=isset($_POST['sha'])?$_POST['sha']:"";
$page=isset($_POST['page'])&&(int)$_POST['page']>0?(int)$_POST['page']:1;
$per_page=20;
$total_count=0;

function array_value($array) {
  $html="";
  foreach ($array as $key => $value) {
    if (is_array($value)) {
	  $html.=array_value($value,++$count);
    } else {
	  if ($key=='full_name') $html.="<tr><td><a href='#' id='github_curl' title='".$value."'>".$value."</a></td>";
	  if ($key=='created_at') $html.="<td>".$value."</td></tr>";
	}  
  }
  return $html;
}
// https://api.github.com/repos/tan-tan-kanarek/github-php-client/commits?page=1&per_page=100&path=README.md
if ($findgit!==""&&$sha!=="") {
  $html.="<br>Проект: <strong>".$findgit."</strong><br>";
  $url="https://api.github.com/repos/".$findgit."/git/trees/".$sha."?recursive=1";
  $json=cURL($url); //print_r($json);
  if (isset($json['message'])) $html.="<br><strong>message: ".$json['message']."</strong><br>";
  else {
    if (isset($json['tree'])) {
	  $html.="<br><table border='0' id='table_git'>";
      $html.="<tr align='center'><td>N</td><td>Файлы</td></tr>";
      foreach ($json['tree'] as $value) {
        $html.="<tr><td>".++$key."</td><td>".$value['path']."</td></tr>";
      }
	  $html.="</table>";
	} else $html.="<br><strong>У проекта нет файлов!</strong><br>";
    // ссылки по страницам
  }
  //print_r($json);
} else $html.="<br><strong>Введите имя проекта!</strong><br>";
$html.=<<<HTML
<script type="text/javascript">
$("#loading_git").hide();
$('a#page_curl').click(function() {
    $("#loading_git").text('Подождите, идет обработка запроса ...');
    $("#loading_git").css('left', (($(window).width()/2)-($("#loading_git").width()/2))+'px');
    $("#loading_git").slideDown(100);
    $("#loading").slideUp(200);
    var form=$('#search_git'), var_findgit=form.find('input[name=findgit]').val(), page=$(this).attr('title');
    $.post('find',{findgit:var_findgit,ajax:'findgit',page:page},function(data){ $('#loading').html(data);$("#loading").slideDown(200);}); 
    return false;
});
$('a#github_curl').click(function() {
    $("#loading_git").text('Подождите, идет обработка запроса ...');
    $("#loading_git").css('left', (($(window).width()/2)-($("#loading_git").width()/2))+'px');
    $("#loading_git").slideDown(100);
    $("#loading").slideUp(200);
    var form=$('#search_git'), var_project=$(this).attr('title');
    $.post('find',{ajax:'opengit',findgit:var_project},function(data){ $('#loading').html(data);$("#loading").slideDown(200);}); 
    return false;
});
</script>
HTML;
$html=iconv('UTF-8','WINDOWS-1251',$html);
echo $html;
unset($json);
?>
