<?php
$user = 'u67293';
$pass = '3126725';
$db = new PDO(
    'mysql:host=localhost;dbname=u67293',
    $user,
    $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
header('Content-Type: text/html; charset=UTF-8');
$session_started = false;
if ($_COOKIE[session_name()] && session_start()) {
    $session_started = true;
    if (!empty($_SESSION['username'])) {
        $_SESSION = array();
        session_destroy();
        // Удаляем куки сессии
        setcookie(session_name(), '', time() - 3600, '/');
        header('Location: index.php');
        exit();
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_SESSION['username'])) {
        header('Location: index.php');
        exit();
    } else {
        ?>

        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>Zadanie5</title>
            <link rel="stylesheet" href="style.css" />
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
                integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/icheck-material@1.0.1/icheck-material.min.css" />
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/icheck-material@1.0.1/icheck-material-custom.min.css" />

            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" />
            <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
            <link rel="stylesheet" type="text/css"
                href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

        </head>
        <div class="tg">
            <div class="wrap1 lh-lg font-monospace">
                <form action="" method="post">
                    <label for="validationCustom01" class="form-label">Логин</label>
                    <input class="form-control rounded-pill" name="login" />
                    <label for="validationCustom01" class="form-label">Пароль</label>
                    <input class="form-control rounded-pill" name="password" />
                    </br>
                    <input class="btn btn-primary " type="submit" value="Войти" />
                    </br>
                    <input class="btn btn-primary " type="submit" value="Выйти">
            </div>
        </div>
        <?php
    }
} else {
    try {
        $username = $_POST['username'];
        $query = "SELECT * FROM users WHERE username = :username ";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['username'] = $username;
            $_SESSION['password'] = '123';
            header('Location: osnova.php');
            exit();
        }
    } catch (PDOException $e) {
        die("Ошибка подключения к базе данных: " . $e->getMessage());
    }
}


if (isset($_SESSION['username'])) {
    ?>

    <?php
}
?>