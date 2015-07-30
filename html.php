<!doctype html public "-//W3C//DTD HTML 4.0 //EN"> 
<html>
<head>
<title>GitHup</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<link rel="stylesheet" type="text/css" media="screen" href="styles.css">
<script src="jquery-1.9.0.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
  // loading_git
  $("#loading_git").hide();
  $("#loading_git").text('');
  $('form#search_git').submit(function() {
    $("#loading_git").text('Подождите, идет обработка запроса ...');
    $("#loading_git").css('left', (($(window).width()/2)-($("#loading_git").width()/2))+'px');
    $("#loading_git").slideDown(100);
    $("#loading").slideUp(200);
    var form=$(this), var_findgit=form.find('input[name=findgit]').val();
    $.post('find',{findgit:var_findgit,ajax:'findgit'},function(data){ $('#loading').html(data);$("#loading").slideDown(200);}); 
    return false;
  });
});
</script>
</head>
<body>
<div id="loading_git"></div>
<fieldset>
  <legend>Информация о проекте (github.com)</legend>
  <form id="search_git" method="post" action="/">
    Имя: <input id="findgit" type="text" name="findgit" value="">
    <input type="submit" value="Поиск" name="search">
  </form>
  <div id="loading"></div>
</fieldset>
</body>
</html>