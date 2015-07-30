<?php
require_once(__DIR__."/function.php");

$html="";
$findgit=isset($_POST['findgit'])?$_POST['findgit']:"";
$page=isset($_POST['page'])&&(int)$_POST['page']>0?(int)$_POST['page']:1;
$per_page=20;
$total_count=0;

function array_value($array) {
  $html="";
  foreach ($array as $key => $value) {
    if (is_array($value)) {
	  $html.=array_value($value,++$count);
    } else {
	  if ($key=='full_name') $html.="<tr><td>".$value."</td><td><a href='#' id='github_curl' class='github_file' title='".$value."'>файлы</a></td><td><a href='#' id='github_curl' class='github_comment' title='".$value."'>комментарии</a></td>";
	  if ($key=='created_at') {
	    $html.="<td>".$value."</td></tr>";
	    //$value=date_parse($value);
	    //$html.="<td>".(isset($value['year'])?$value['year']."-".$value['month']."-".$value['day']." ".$value['hour'].":".$value['minute'].":".$value['second']:"")."</td></tr>";
	  }
	}  
  }
  return $html;
}

if ($findgit!=="") {
  // поиск проекта по имени
  $json=cURL("https://api.github.com/search/repositories?q=".$findgit."+in:name&page=".$page."&per_page=".$per_page); // read_github
  if (isset($json['message'])) $html.="<br><strong>message: ".$json['message']."</strong><br>";
  else {
    foreach ($json as $key => $value) {
      if (is_array($value)) {
	    $html.="<table border='0' id='table_git'>";
        $html.="<tr align='center'><td>Полное имя</td><td>Файлы</td><td>Комментарии</td><td>Дата создания</td></tr>";
	    $html.=array_value($value);
		$html.="</table>";
      } else {
	    if ($key=='total_count') {
		  $total_count=$value;
          $html.="Найдено проектов: ".$value."<br>";
		}
	  }
    }
    // ссылки по страницам
    if ($total_count>$per_page) {
      $page_count=ceil($total_count/$per_page);
      $html.="<br>страница (".$page." из ".$page_count."): ";
	  if ($page_count<=10) {
	    for ($i=1; $i<=$page_count; ++$i) {
	      $html.=$page!==$i?"<a href='#' id='page_curl' title='".$i."'>".$i."</a> ":"<font class='red'>".$i."</font> ";
	    }
	  } else {
	    $html.="<a href='#' id='page_curl' title='1'>первая</a> .. ";
	    for ($i=1; $i<=$page_count; ++$i) {
	      if ($i<=$page-5||$i>=$page+5) continue;
	      $html.=$page!==$i?"<a href='#' id='page_curl' title='".$i."'>".$i."</a> ":"<font class='red'>".$i."</font> ";
	    }
        $html.=".. <a href='#' id='page_curl' title='".$page_count."'>последняя</a> ";
	  }
      $html.="<br>";
    }
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
    var form=$('#search_git'), var_project=$(this).attr('title'), var_open=$(this).attr('class');
    $.post('find',{ajax:var_open,findgit:var_project},function(data){ $('#loading').html(data);$("#loading").slideDown(200);}); 
    return false;
});
</script>
HTML;
$html=iconv('UTF-8','WINDOWS-1251',$html);
echo $html;
unset($json);
?>
