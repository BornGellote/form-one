<?php
//if(isset($_POST['submit_rest'])) {

    $connect=mysqli_connect('localhost', 'example_user', 'example_password', 'example_base');
    if (!$connect) {
        echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
        echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
        echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
        exit;
    }

    echo "Соединение с MySQL установлено!" . PHP_EOL;
    //echo "Информация о сервере: " . mysqli_get_host_info($connect) . PHP_EOL;

    //mysqli_close($connect);
    $username = mysqli_real_escape_string($connect,$_POST['rest_mail']);
    $string = '';
    //$password_rest = mysqli_real_escape_string($connect,$_POST['users_password']);
    //$mail = mysqli_real_escape_string($connect,$_POST['user_email']);
    $zapros = "SELECT `users_id` FROM `users` WHERE `user_email`='{$username}' LIMIT 1";
    $sql = mysqli_query($connect,$zapros) or die(mysqli_error());
    if (mysqli_num_rows($sql)==1) {
        $simv = array ("92", "83", "7", "66", "45", "4", "36", "22", "1", "0",
            "k", "l", "m", "n", "o", "p", "q", "1r", "3s", "a", "b", "c", "d", "5e", "f", "g", "h", "i", "j6", "t", "u", "v9", "w", "x5", "6y", "z5");
        for ($k = 0; $k < 8; $k++)
        {
            shuffle ($simv);
            $string = $string.$simv[1];
            $pass = md5(md5($string));

        }
        $zapros = "UPDATE `users` SET  `users_password`='{$pass}' WHERE `user_email`='{$username}' ";
        $sql = mysqli_query($connect,$zapros) or die(mysqli_error());
        $zapros = "SELECT `user_email` FROM `users` WHERE `user_email`='{$username}' LIMIT 1";
        $sql = mysqli_query($connect,$zapros)or die(mysqli_error());
        $r = mysqli_fetch_assoc($sql);
        $mail = $r['user_email'];
        $subject = "Запрос на восстановление пароля";
        $message = "Hello, $username. Your new password: $string";
        $headers="FROM: Test\r\n MIME-Version: 1.0\r\n Content-type: text/html; charset=windows-1251";
        mail($mail, $subject, $message, $headers);
    }
    echo "На ваш почтовый ящик было отправлено письмо с новый паролем";
//    header("Location: ../index.php"); exit();
//}