<?PHP
$user = 'u67293';
$pass = '3126725';
$db = new PDO(
    'mysql:host=localhost;dbname=u67293',
    $user,
    $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
try {
    function isValidName($login)
    {
        return preg_match('/^[А-ЯЁёа-я\s]+$/u', $login);
    }

    function isValidPhone($tel)
    {
        return preg_match('/^(\+7|7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/', $tel);
    }

    function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    header('Content-Type: text/html; charset=UTF-8');
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // Массив для временного хранения сообщений пользователю.
        $messages = array();
        // Выдаем сообщение об успешном сохранении.
        if (!empty($_COOKIE['save'])) {
            // Удаляем куку, указывая время устаревания в прошлом.
            setcookie('save', '', 100000);
            setcookie('login', '', 100000);
            setcookie('password', '', 100000);
            // Выводим сообщение пользователю.
            $messages[] = 'Спасибо, результаты сохранены.';
            // Если в куках есть пароль, то выводим сообщение.
            if (!empty($_COOKIE['password'])) {
              $messages[] = sprintf('Вы можете <a href="signin.php">войти</a> с логином <strong>%s</strong>
                и паролем <strong>%s</strong> для изменения данных.',
                strip_tags($_COOKIE['login']),
                strip_tags($_COOKIE['password']));
            }
          }
        // Складываем признак ошибок в массив.
        $errors = array();
        $errors['fio'] = !empty($_COOKIE['fio_error']);
        $errors['tel'] = !empty($_COOKIE['tel_error']);
        $errors['email'] = !empty($_COOKIE['email_error']);
        $errors['date'] = !empty($_COOKIE['date_error']);
        $errors['someGroupName'] = !empty($_COOKIE['someGroupName_error']);
        $errors['bio'] = !empty($_COOKIE['bio_error']);
        $errors['checkt'] = !empty($_COOKIE['checkt_error']);
        $errors['language'] = !empty($_COOKIE['language_error']);
        // Выдаем сообщения об ошибках.
        if ($errors['fio']) {
            setcookie('fio_error', '', 100000);
            setcookie('fio_value', '', 100000);
            $messages[] = '<div class="error">Заполните ФИО русскими буквами.</div>';
        }
        if ($errors['tel']) {
            setcookie('tel_error', '', 100000);
            setcookie('tel_value', '', 100000);
            $messages[] = '<div class="error">Введите номер телефона правильной длины.</div>';
        }
        if ($errors['email']) {
            setcookie('email_error', '', 100000);
            setcookie('email_value', '', 100000);
            $messages[] = '<div class="error">Введите почту.</div>';
        }
        if ($errors['date']) {
            setcookie('date_error', '', 100000);
            setcookie('date_value', '', 100000);
            $messages[] = '<div class="error">Выберите дату(Вам нет 18 лет).</div>';
        }
        if ($errors['someGroupName']) {
            setcookie('someGroupName_error', '', 100000);
            setcookie('someGroupName_value', '', 100000);
            $messages[] = '<div class="error">Выберите пол.</div>';
        }
        if ($errors['language']) {
            setcookie('language_error', '', 100000);
            setcookie('language_value', '', 100000);
            $messages[] = '<div class="error">Вы не выбрали языки программирования.</div>';
        }
        if ($errors['bio']) {
            setcookie('bio_error', '', 100000);
            setcookie('bio_value', '', 100000);
            $messages[] = '<div class="error">Расскажите о себе.</div>';
        }
        if ($errors['checkt']) {
            setcookie('checkt_error', '', 100000);
            setcookie('checkt_value', '', 100000);
            $messages[] = '<div class="error">Вы не ознакомились с правилами.</div>';
        }
        
// Складываем предыдущие значения полей в массив, если есть.
$values = array();
$values['fio'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
$values['tel'] = empty($_COOKIE['tel_value']) ? '' : $_COOKIE['tel_value'];
$values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
$values['date'] = empty($_COOKIE['date_value']) ? '' : $_COOKIE['date_value'];
$values['someGroupName'] = empty($_COOKIE['someGroupName_value']) ? '' : $_COOKIE['someGroupName_value'];
$values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
$values['checkt'] = empty($_COOKIE['checkt_value']) ? '' : $_COOKIE['checkt_value'];
$values['language'] = empty($_COOKIE['language_value']) ? '' : strip_tags($_COOKIE['language_value']);
if (
    empty($errors) && !empty($_COOKIE[session_name()]) &&
    session_start() && !empty($_SESSION['login'])
) {
    $stmt = $db->prepare("SELECT full_name, phone, email, birth_date, gender, bio, contract_agreed FROM users WHERE login = :login");
    $stmt->bindParam(':login', $_SESSION['login']);
    $stmt->execute();
    $values = $stmt->fetch(PDO::FETCH_ASSOC);
    printf('Имя пользователя: %s<br>', $values['full_name']);
    printf('Телефон: %s<br>', $values['phone']);
    printf('Email: %s<br>', $values['email']);
    printf('Дата рождения: %s<br>', $values['birth_date']);
    printf('Пол: %s<br>', $values['gender']);
    printf('Выберите языки программирования: %s<br>', $values['language']);
    printf('О себе: %s<br>', $values['bio']);
    printf('Согласие на условия: %s<br>', $values['contract_agreed']);
    printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
}
include('index.php');
}
else {
    // Проверяем ошибки.
    $errors = FALSE;
    if (empty($_POST['fio']) || !isValidName($_POST['fio'])) {
      // Выдаем куку на день с флажком об ошибке в поле fio.
      setcookie('fio_error', '1', time() + 24 * 60 * 60);
      $errors = TRUE;
    }
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('fio_value', $_POST['fio'], time() + 30 * 24 * 60 * 60);
    if (empty($_POST['tel']) || !isValidPhone($_POST['tel'])) {
        setcookie('tel_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('tel_value', $_POST['tel'],  time() + 30 * 24 * 60 * 60);

    if (empty($_POST['email']) || !isValidEmail($_POST['email'])) {
        setcookie('email_error', '1', time() + time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('email_value', $_POST['email'],  time() + 30 * 24 * 60 * 60);

    if (empty($_POST['date'])) {
        setcookie('date_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('date_value', $_POST['date'],  time() + 30 * 24 * 60 * 60);
    if (empty($_POST['someGroupName'])) {
        setcookie('someGroupName_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('someGroupName_value', $_POST['someGroupName'],  time() + 30 * 24 * 60 * 60);
    if (empty($_POST['bio'])) {
        setcookie('bio_error', '1',time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('bio_value', $_POST['bio'],  time() + 30 * 24 * 60 * 60);
    if (empty($_POST['checkt'])) {
        setcookie('checkt_error', '1',time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('checkt_value', $_POST['checkt'],  time() + 30 * 24 * 60 * 60);

   if (empty($_POST['language'])) {
            setcookie('language_error', '1', time() + 24 * 60 * 60);
            $errors = TRUE;
        } else {
            $selected_languages = $_POST['language'];
            setcookie('language_value', serialize($selected_languages), time() + 12 * 30 * 24 * 60 * 60);
        }
    if ($errors) {
        // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
        header('Location: osnova.php');
        exit();
      }
      else {
        // Удаляем Cookies с признаками ошибок.
        setcookie('fio_error', '', 100000);
        setcookie('tel_error', '', 100000);
        setcookie('email_error', '', 100000);
        setcookie('date_error', '', 100000);
        setcookie('someGroupName_error', '', 100000);
        setcookie('bio_error', '', 100000);
        setcookie('checkt_error', '', 100000);
        setcookie('language_error', '', 100000);
      }
      // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
  if (!empty($_COOKIE[session_name()]) &&
  session_start() && !empty($_SESSION['login'])) {
// TODO: перезаписать данные в БД новыми данными,
// кроме логина и пароля.
        $fio = $_POST['fio'];
        $tel = $_POST['tel'];
        $email = $_POST['email'];
        $date = $_POST['date'];
        $someGroupName = $_POST['someGroupName'];
        $bio = $_POST['bio'];
        $checkt = $_POST['checkt'];
        $stmt = $db->prepare("UPDATE users SET full_name = :full_name, phone = :phone, email = :email, birth_date = :birth_date, gender = :gender, bio = :bio, contract_agreed = :contract_agreed WHERE login = :username");
        $stmt->bindParam(':full_name', $fio);
        $stmt->bindParam(':phone', $tel);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':birth_date', $date);
        $stmt->bindParam(':gender', $someGroupName);
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':contract_agreed', $checkt);
        $stmt->bindParam(':login', $_SESSION['login']);
        $stmt->execute();
}
else {
    $login = 'user'.rand(100,999);
    $password = md5(rand(1000,9999));
    setcookie('login', $login, time() + (86400 * 30), '/'); // Устанавливаем cookie на 30 дней
    setcookie('password', $password, time() + (86400 * 30), '/'); // Устанавливаем cookie на 30 дней
        setcookie('save', '1');
        $stmt = $db->prepare("INSERT INTO users (full_name, phone,email,birth_date,gender,bio,contract_agreed) VALUES (:full_name, :phone,:email,:birth_date,:gender,:bio,:contract_agreed)");
        $login = $_POST['fio'];
        $email = $_POST['email'];
        $tel = $_POST['tel'];
        $date = $_POST['date'];
        $someGroupName = $_POST['someGroupName'];
        $bio = $_POST['bio'];
        $checkt = $_POST['checkt'];
        $stmt->bindParam(':full_name', $login);
        $stmt->bindParam(':phone', $tel);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':birth_date', $date);
        $stmt->bindParam(':gender', $someGroupName);
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':contract_agreed', $checkt);
        $stmt->execute();
        $user_id = $db->lastInsertId();
        $Languages = $_POST['language'];
        foreach ($Languages as $language_name) {
            $stmt = $db->prepare("INSERT INTO user_languages (user_id, language_name) VALUES (:user_id,:language_name)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':language_name', $language_name);
            $stmt->execute();
        }
    }
        // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: osnova.php');
    }
} catch (PDOException $e) {
    print ('Error : ' . $e->getMessage());
    exit();
}
?>