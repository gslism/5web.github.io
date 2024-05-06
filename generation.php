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
    $login = 'user'.rand(100,999); // Генерируем случайный логин
    $password = md5(rand(1000,9999)); // Генерируем случайный пароль и шифруем его с помощью md5

    // Запись данных в базу данных
    $stmt = $db->prepare("INSERT INTO user (login, password) VALUES (:login,:password)");

    if ($conn->query($db) === TRUE) {
        echo "Запись успешно добавлена в базу данных<br>";
        echo 'Ваш логин: '.$login.'<br>';
        echo 'Ваш пароль: '.$password;
    } else {
        echo "Ошибка при записи в базу данных: " . $conn->error;

    }
} catch (PDOException $e) {
    print ('Error : ' . $e->getMessage());
    exit();
}
// Закрытие соединения с базой данных


?>