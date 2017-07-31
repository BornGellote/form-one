<?php
  // Функция для генерации случайной строки
  function generateCode($length=6) {
      $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
      $code = "";
      $clen = strlen($chars) - 1;
      while (strlen($code) < $length) {
          $code .= $chars[mt_rand(0,$clen)];
      }
      return $code;
  }

  // Если есть куки с ошибкой то выводим их в переменную и удаляем куки
  if (isset($_COOKIE['errors'])){
      $errors = $_COOKIE['errors'];
      setcookie('errors', '', time() - 60*24*30*12, '/');
  }

  // Подключаем конфиг
  include 'controller/config.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Примеры форм авторизации</title>
    <meta name="description" content="Примеры форм авторизации с использованием CSS3" />
    <meta name="keywords" content="login, form" />
    <meta name="author" content="artTex" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />

</head>

<body>
<div class="container">
    <div class="top"> </div>

    <header>

    </header>

    <section class="main">
        <div class="form-horizontal">
            <span class="heading">Sign up/Register</span>
        </div>

        <div class="box">
            <nav id="tabs" class="tabs">
                <a id="tabLogin" class="iconLogin active blueBox" title="Войти"></a>
                <a id="tabRegister" class="iconRegister greenBox" title="Регистрация"></a>
                <a id="tabForgot" class="iconForgot redBox" title="Забыл пароль?"></a>
            </nav>

            <div class="containerWrapper">

                <!-- login container -->
                <div id="containerLogin" class="tabContainer active">
                    <h2 class="loginTitle">Sign Up</h2>
                    <div class="form_body">
                        <?php
                        if(isset($_POST['submit_sign'])){

                            //Вытаскиваем из БД запись, у которой логин равняеться введенному
                            $data = mysql_fetch_assoc(mysql_query("SELECT users_id, users_password FROM `users` WHERE `users_login`='".mysql_real_escape_string($_POST['login'])."' LIMIT 1"));

                            // Соавниваем пароли
                            if($data['users_password'] === md5(md5($_POST['password'])))
                            {
                                // Генерируем случайное число и шифруем его
                                $hash = md5(generateCode(10));

                                // Записываем в БД новый хеш авторизации и IP
                                mysql_query("UPDATE users SET users_hash='".$hash."' WHERE users_id='".$data['users_id']."'") or die("MySQL Error: " . mysql_error());

                               // Ставим куки
                                setcookie("id", $data['users_id'], time()+60*60*24*30);
                                setcookie("hash", $hash, time()+60*60*24*30);

                                // Переадресовываем браузер на страницу проверки нашего скрипта
                                header("Location: check.php"); exit();
                            }
                            else {
                                print "Вы ввели неправильный логин/пароль <br>";
                            }
                        } ?>
                        <form class="form-5 clearfix" action="" method="POST" id="signup">
                            <p>
                                <input type="text" id="login" name="login" placeholder="Логин" required>
                                <input type="password" name="password" id="password" placeholder="Пароль" required>
                            </p>
                            <button type="submit" name="submit_sign">
                                <i class="icon-arrow-right"></i>
                                <span>Вход</span>
                            </button>
                        </form>
                        <?php
                        // Проверяем наличие в куках номера ошибки
                            if (isset($errors)) {print '<h4>'.$error[$errors].'</h4>';}

                        ?>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <div class="main-checkbox">
                                    <input type="checkbox" value="none" id="checkbox1" name="check"/>
                                    <label for="checkbox1"></label>
                                </div>
                                <span class="text">Запомнить</span>
                            </div>
                        </div>
                        <div class="main-signin__foot">
                            <div class="foot__left">
                                <p>Войти через:</p>
                            </div>
                            <div class="foot__right">
                                <a href="#"><i class="fa fa-facebook fa-2x" aria-hidden="true"></i></a>
                                <a href="#"><i class="fa fa-twitter fa-2x" aria-hidden="true"></i></a>
                                <a href="#"><i class="fa fa-google-plus fa-2x" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>

                <!-- register container -->
                <div id="containerRegister" class="tabContainer">
                    <h2 class="loginTitle">Register</h2>
                    <div class="form_body">
                        <?php
                        if(isset($_POST['submit_reg'])) {

                            $err = array();

                            // проверям логин
                            if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['reg_name'])) {
                                $err[] = "Логин может состоять только из букв английского алфавита и цифр";
                            }

                            if(strlen($_POST['reg_name']) < 3 or strlen($_POST['reg_name']) > 30) {
                                $err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
                            }

                            // проверяем, не сущестует ли пользователя с таким именем
                            $query = mysql_query("SELECT COUNT(users_id) FROM users WHERE users_login='".mysql_real_escape_string($_POST['reg_name'])."'")or die ("<br>Invalid query: " . mysql_error());
                            if(mysql_result($query, 0) > 0) {
                                $err[] = "Пользователь с таким логином уже существует в базе данных";
                            }
                            // проверям email
                            if (!filter_var(($_POST['reg_mail']), FILTER_VALIDATE_EMAIL)) {
                                $err[] = "Email может состоять только из букв английского алфавита и цифр";
                            }

                            // Если нет ошибок, то добавляем в БД нового пользователя
                            if(count($err) == 0) {

                                $login = $_POST['reg_name'];

                                //Убераем лишние пробелы и делаем двойное шифрование
                                $password = md5(md5(trim($_POST['reg_pass'])));
                                $email = trim($_POST['reg_mail']);
                                $phone_county = substr($_POST['reg_phone'], 1, 3);
                                $phone = substr($_POST['reg_phone'], 4, 19);

                                mysql_query("INSERT INTO `users` SET users_login='" . $login . "', users_password='" . $password . "', user_email='" . $email . "', phone_country='" . $phone_county . "', user_phone='" . $phone . "'");


//                                mysql_query("INSERT INTO users SET users_login='".$login."', users_password='".$password."'");
                                //mysql_query("INSERT INTO users SET users_login='".$login."', users_password='".$password."', user_email='" .$email."', phone_country='" .$phone_county."', user_phone='" .$phone."'" );
                                //mysql_query("INSERT INTO users SET users_login='".$login."', users_password='".$password."', user_email='" .$email."', user_phone='" .$phone."'" );
                                header("Location: index.php"); exit();
                            }
                        } ?>
                        <form class="form-5 form_reg clearfix" action="" method="POST" id="signup">
                            <p>
                                <input type="text" id="reg_name" name="reg_name" placeholder="Ваше имя" required>
                                <input type="password" id="reg_pass" name="reg_pass" placeholder="Ваш пароль" required>
                                <input type="text" id="reg_mail" name="reg_mail" placeholder="Ваш email" required>
                                <input type="text" id="reg_phone" name="reg_phone" placeholder="Ваш телефон" required>
                            </p>
                            <button type="submit" name="submit_reg">
                                <i class="icon-arrow-right"></i>
                                <span>Register</span>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="clear"></div>

                <!-- forgot container -->
                <div id="containerForgot" class="tabContainer">
                    <h2 class="loginTitle">Restore password</h2>
                    <div class="form_body">
<!--                        <form class="form-5 form_restore clearfix" action="controller/restore_pass.php"  method="POST" id="rest_pass">-->
                        <form class="form-5 form_restore clearfix" action=""  id="rest_pass">
                            <p>
                                <input type="text" id="rest_mail" name="rest_mail" placeholder="Ваш email">
                            </p>
                            <button type="submit" name="submit_rest">
                                <i class="icon-arrow-right"></i>
                                <span>Restore</span>
                            </button>
                        </form>
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
    <script type="text/javascript">
        $('input, textarea').placeholder();
    </script>
<script src="js/jquery.maskedinput.min.js"></script>
    <script type="text/javascript">
        $(function() {
                $.mask.definitions['~'] = "[+-]";
                $("#reg_phone").mask("+999-9999-99-999");
            });
    </script>
<script src="js/index.js"></script>

</body>
</html>

