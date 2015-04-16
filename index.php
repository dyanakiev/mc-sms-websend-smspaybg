<?php
date_default_timezone_set('Europe/Sofia'); //Задаваме времева зона
$starttime = explode(' ', microtime());		//Стартираме
$starttime = $starttime[1] + $starttime[0];	//Микротаймера

//Websend настройки
include_once 'Websend.php';//Websend api
$ws = new Websend("0.0.0.0");//IP на сървъра 
$ws->password = "999988";//Паролата от config файла на сървъра


$errormsg = '';

//Настройки
$siteTitle = "Сайт име"; //Име на сайта

$siteDescription = "Сайт описание"; //Description

$siteNavTextTitle = "Сайт име"; //Текста на шапката

//---/

define ('USER_ID', 4601); // Попълнете вашия потр. код за smspay
$service_id = "7001";//Номер на услугата oт smspay

$smsSendInfo = "Изпрати смс на номер 0000 с текст TXTTT на цена 6.00лв с ДДС!"; //Информация за изпращане на смс-а

//Функция за връзка с smspay
function smspay_check_code ($user_id, $service_id, $code) {
   $url = sprintf ("http://rcv.smspay.bg/users/check_code.php?" .
   "user_id=%d&service_id=%d&code=%s", $user_id, $service_id, $code);
   return @file_get_contents ($url);
}

//Заявката
if(isset($_POST['submit']))
{
	$code = htmlspecialchars(addslashes(trim($_POST['code'])));
	$playername = htmlspecialchars(addslashes($_POST['playername']));
	$usergroup = htmlspecialchars(addslashes($_POST['usergroup']));
	
	if($ws->connect()){ //проверяваме дали сървъра е пуснат...
		 if($playername==NULL | $code==NULL )
		 {
		$errormsg = '<div class="alert alert-danger" role="alert">Попълнете всички полета!</div>'; //Ако полетата са празни изписва това.
		 }else{
		 	 if(smspay_check_code (USER_ID, $service_id, $code) ==1) {
				$ws->doCommandAsConsole("pex user $playername group set $usergroup"); //Съответно ако искаш за един месец можеш да видиш в wiki-то на pex за lifetime
				$ws->doCommandAsConsole("say $playername buy $usergroup");
        		$ws->disconnect();
				$errormsg = '<div class="alert alert-success" role="alert">Честито <font color="black">('.$playername.')</font> групата <font color="orange">('.$usergroup.')</font> е активирана!</div>'; //Активирана група...
			}else{
				$errormsg = '<div class="alert alert-danger" role="alert">СМС КОДА Е ГРЕШЕН! Опитай отново!</div>'; //Ако кода е грешен изписва това.
			}
		 }
	}else{
		$errormsg = '<div class="alert alert-danger" role="alert">Сървъра е офлайн, моля ела отново когато е пуснат!</div>'; //Ако сървъра е спрян изписва това..
    }

}
?>
<!DOCTYPE html>
<html lang="bg">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo $siteDescription; ?>">
    <link rel="icon" href="http://getbootstrap.com/favicon.ico">

    <title><?php echo $siteTitle; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="http://getbootstrap.com/examples/jumbotron-narrow/jumbotron-narrow.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="http://getbootstrap.com/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="http://getbootstrap.com/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">
      <div class="header clearfix">
        <nav>
          <ul class="nav nav-pills pull-right">
            <li role="presentation" class="active"><a href="/">Начало</a></li>
          </ul>
        </nav>
        <h3 class="text-muted"><?php echo $siteNavTextTitle; ?></h3>
      </div>

  

      <div class="row marketing">
    <div class="alert alert-warning" role="alert"><?php echo $smsSendInfo; ?></div>

        <form method="post" action="">
        
		<div class="form-group">
			<label for="playername">Minecraft име <font color="red">*</font></label>
			<input type="text" class="form-control" name="playername" placeholder="Въведи точно името си от сървъра!">
		</div>
		
		<div class="form-group">
			<label for="usergroup">Избери ранг <font color="red">*</font></label>
			<!-- Можеш да добавяш още групи или да махаш съответно за да добавиш <option value="Vip">Vip</option> го добави след select  -->
		<select class="form-control" name="usergroup">
			<option value="MegaUser">MegaUser</option>
			<option value="SuperUser">SuperUser</option>
		</select>
		
		</div>
		
		<div class="form-group">
    		<label for="smscode">СМС Код <font color="red">*</font></label>
    		<input type="text" class="form-control" name="code" placeholder="Въведи смс кода който получи!">
		</div>
		
  		<input type="submit" class="btn btn-default" name="submit" value="Изпълни" />

		</form>
		
	<br />
	
	<?php echo $errormsg; ?>
      </div>

      <footer class="footer">
        <p>&copy; <a href="https://github.com/TheEVILbg">TheEVIL</a><span style="float:right;"><?php $mtime = explode(' ', microtime());$totaltime = $mtime[0] + $mtime[1] - $starttime;printf('Страницата се генерира за %.3f секунди.', $totaltime); //показваме микротаймера?></span></p>
      </footer>

    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="http://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
