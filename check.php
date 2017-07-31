<?php
# подключаем конфиг
include 'controller/config.php';

# проверка авторизации
if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
    $userdata = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE users_id = '".intval($_COOKIE['id'])."' LIMIT 1"));
    if(($userdata['users_hash'] !== $_COOKIE['hash']) or ($userdata['users_id'] !== $_COOKIE['id']))    {
        setcookie('id', '', time() - 60*24*30*12, '/');
        setcookie('hash', '', time() - 60*24*30*12, '/');
        setcookie('errors', '1', time() + 60*24*30*12, '/');
        header('Location: index.php'); exit();
    }
}
else {
    setcookie('errors', '2', time() + 60*24*30*12, '/');
    header('Location: index.php'); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin panel</title>
    <meta name="description" content="Примеры форм авторизации с использованием CSS3" />
    <meta name="keywords" content="login, form" />
    <meta name="author" content="artTex" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />

</head>
<body class="admin_panel">
<div class="container">
    <div class="top">
        <?php
        if(isset($_POST['submit_exit'])){
            setcookie('id', '', time() - 60*60*24*30, '/');
            setcookie('hash', '', time() - 60*60*24*30, '/');
            header('Location: index.php'); exit();
        } ?>
        <form action="" method="post" class="exit">
            <h1>Good day
                <strong>
                    <?php  echo $userdata['users_login']; ?>
                </strong>
            </h1>
            <input type='submit' name='submit_exit' class="btn btn-info" value='Sign out'/>
        </form>
    </div>
    <header>

    </header>

    <section class="main_panel">
        <div class="form-horizontal">
            <span class="heading">Admin panel</span>
        </div>
        <div class="box">
            <nav id="tabs" class="tabs">
                <a id="tabLogin" class="iconUser active blueBox" title="User"></a>
                <a id="tabRegister" class="iconProfile greenBox" title="Profile"></a>
                <a id="tabForgot" class="iconSetting redBox" title="Settings"></a>
                <a id="tabVer" class="iconForgot greyBox" title="Version"></a>
            </nav>
            <div class="containerWrapper">
<!--                <!-- User container -->
                <div id="containerLogin" class="tabContainer active">
                    <h2 class="loginTitle">User Info</h2>
                    <div class="form_body">
                        <h3>Name: <?php  echo $userdata['users_login'];?></h3>
                        <h3>E-mail: <?php  echo $userdata['user_email'];?></h3>
                        <h3>Phone: <?php  echo '+' . $userdata['phone_country'] . $userdata['user_phone'];?></h3>
                    </div>
                </div>
                <div class="clear"></div>
<!--                <!-- Profile container -->
                <div id="containerRegister" class="tabContainer">
                    <h2 class="loginTitle">Profile Edit</h2>
                    <div class="form_body">

                        <form action="" method="POST" class="form-5 form_edit clearfix" >
                            <p>
                                <input type="text" id="new_log" name="new_log" placeholder="New login">
                                <input type="text" id="new_email" name="new_email" placeholder="New email">
                                <input type="password" id="new_pass" name="new_pass" placeholder="New pass">
                                <input type="password" id="new_pass_two" name="new_pass_two" placeholder="Retutn new pass">
                                <input type="text" id="new_phone" name="new_phone" placeholder="New phone">
                            </p>
                            <button type="submit" name="save">
                                <i class="icon-arrow-right"></i>
                                <span>Save</span>
                            </button>
<!--                                <input type="submit" name="save" value="Save">-->
<!--                                <i class="icon-arrow-right"></i>-->
<!--                                <span>Save</span>-->
                        </form>
                        <?php if(isset($_POST['save'])) {
//                            if($userdata['users_hash'] == $_COOKIE['hash'] ) {
//                            $err = array();
                                // проверям логин
//                            if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['new_log'])) {
//                                $err[] = "Логин может состоять только из букв английского алфавита и цифр";
//                            }
//
//                            if(strlen($_POST['new_log']) < 3 or strlen($_POST['new_log']) > 30) {
//                                $err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
//                            }

                                // проверяем, не сущестует ли пользователя с таким именем
//                            $query = mysql_query("SELECT COUNT(users_id) FROM users WHERE users_login='".mysql_real_escape_string($_POST['new_log'])."'")or die ("<br>Invalid query: " . mysql_error());
//                            if(mysql_result($query, 0) > 0) {
//                                $err[] = "Пользователь с таким логином уже существует в базе данных";
//                            }
                                // проверям email
//                            if (!filter_var(($_POST['new_email']), FILTER_VALIDATE_EMAIL)) {
//                                $err[] = "Email может состоять только из букв английского алфавита и цифр";
//                            }

                                // проверям pass
                                $password = md5(md5(trim($_POST['new_pass'])));
                                $password_two = md5(md5(trim($_POST['new_pass_two'])));
                                if ($password == $password_two) {
                                    $err[] = "Password do not match";
                                }

                                // Если нет ошибок, то добавляем в БД нового пользователя
//                            if(count($err) == 0) {

                                $login = $_POST['new_log'];

                                //Убераем лишние пробелы и делаем двойное шифрование
                                $email = trim($_POST['new_email']);
                                $phone_county = substr($_POST['new_phone'], 1, 3);
                                $phone = substr($_POST['new_phone'], 4, 19);

                                mysql_query("UPDATE `users` SET users_login='" . $login . "', users_password='" . $password . "', user_email='" . $email . "', phone_country='" . $phone_county . "', user_phone='" . $phone . "'");

                                //header("Location: check.php"); exit();
//                            }
//                                $arr = array(
//                                    'new_log' => $_POST['new_log'],
//                                    'new_email' => $_POST['new_email'],
//                                    'new_pass' => $_POST['new_pass'],
//                                    'new_phone' => $_POST['new_phone'],
//                                );
//                                print_r($arr);
//                            }
                        } ?>
                    </div>
                </div>
                <div class="clear"></div>
<!--                <!-- Settings container -->
                <div id="containerForgot" class="tabContainer">
                    <h2 class="loginTitle">Settings</h2>
                    <div class="form_body">

                    </div>
                </div>
                <div class="clear"></div>
                <div id="containerVer" class="tabContainer">
                    <h2 class="loginTitle">Version</h2>
                    <div class="form_body">
                        <h3>Developer: artTex</h3>
                        <h3>Version: 1.0</h3>
                        <h3>Copyright: © 2016 artTex. <br/> All rights reserved.</h3>
                        <h3>Site developer: <a href="//artTex.in.ua" target="_blank">artTex</a></h3>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </section>

</div>
<!-- jQuery if needed -->
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.placeholder.min.js"></script>
<script src="js/index.js"></script>
<script src="js/jquery.maskedinput.min.js"></script>
<script type="text/javascript">
    $(function() {
        $.mask.definitions['~'] = "[+-]";
        $("#new_phone").mask("+999-9999-99-999");
    });
</script>

</body>
</html>
