<?php
$options = getopt(null,[
    'dsn:',
    'user:',
    'password:'
]);
$dsn = $options['dsn'];
$in = fopen("php://stdin", "r");
$out = fopen('php://stdout','w');
while (!feof($in)) {
    $line = fgets($in);
    try {
        $pdo = new PDO(
            $dsn,
            $options['user'], $options['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        $line = trim($line);
        $stmt = $pdo->prepare('SELECT ip FROM allowed_ips WHERE ip = ?');
        $execute = $stmt->execute([
            $line
        ]);
        $result = $stmt->fetch();
        if (isset($result['ip']) && strcmp($result['ip'],$line) === 0) {
            fwrite($out, "OK\n");
        } else {
            fwrite($out, "ERR\n");
        }
    }catch (PDOException $e){
        fwrite($out,"BH\n");
    }
}
