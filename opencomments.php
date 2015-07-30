<?php
require_once(__DIR__."/function.php");

$html="";
$findgit=isset($_POST['findgit'])?$_POST['findgit']:"";
$page=isset($_POST['page'])&&(int)$_POST['page']>0?(int)$_POST['page']:1;
$per_page=10;
$total_count=0;
$json=array();

function array_sha($findgit,$all=false,$page=1,$per_page=100,$array=array()) {
  $url="https://api.github.com/repos/".$findgit."/commits?page=".$page."&per_page=".$per_page; 
  $json=cURL($url);
  if (isset($json['message'])) return $json;
  foreach ($json as $key => $value) {
    $file="";
    if (isset($value['sha'])) {
	  if (!$all) {
        $url="https://api.github.com/repos/".$findgit."/commits/".$value['sha']; 
        $files=cURL($url);
		if (isset($files['files']))
		  foreach ($files['files'] as $val) 
		    $file.=$val['filename']."<br>";
	  }
	  $array[]=array(
	    'sha'		=>$value['sha'],
		'name'		=>$value['commit']['committer']['name'],
		'date'		=>$value['commit']['committer']['date'],
		'message'	=>$value['commit']['message'],
		'files'		=>$file
	  );
    }
  } 
  if (++$key==$per_page&&$all) $array=array_sha($findgit,$all,++$page,$per_page,$array);
  return $array;
}

function sort_array($array, $sortby, $direction='asc') {
  $sorted = array();
  $tmp_Array = array();
  foreach($array as $key=>$value) $tmp_Array[$key]=strtolower($value[$sortby]);
  if ($direction=='asc') 
    asort($tmp_Array);
  else 
    arsort($tmp_Array);
  foreach ($tmp_Array as $key=>$tmp) $sorted[]=$array[$key];
  return $sorted;
}

if ($findgit!=="") {
  $json=array_sha($findgit,true);
  $total_count=count($json);
  if (isset($json['message'])) $html.="<br><strong>message: ".$json['message']."</strong><br>";
  else if ($total_count>0) {
    $html.="<br>Всего комментариев: ".$total_count.".<br>";

    $json=array_sha($findgit,false,$page,$per_page);
	$json=sort_array($json, 'date');
	$html.="<table border='0' id='table_git'>";
    $html.="<tr align='center'><td>N</td><td>Автор</td><td>Дата</td><td>Файлы</td><td>Комментарий</td></tr>";
    foreach ($json as $key => $value) {
	  $html.="<tr><td>".++$key."</td><td>".htmlentities($value['name'],ENT_QUOTES|ENT_IGNORE,"UTF-8")."</td><td>".$value['date']."</td><td>".$value['files']."</td><td>".htmlentities($value['message'],ENT_QUOTES|ENT_IGNORE,"UTF-8")."</td></tr>";
    }
	$html.="</table>";
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
    var page=$(this).attr('title');
    $.post('find',{findgit:'{$findgit}',ajax:'github_comment',page:page},function(data){ $('#loading').html(data);$("#loading").slideDown(200);}); 
    return false;
});

</script>
HTML;
$html=iconv('UTF-8','WINDOWS-1251',$html);
echo $html;
unset($json);
?>
