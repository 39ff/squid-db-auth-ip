<?php
$options = getopt("", [
    'dsn:',
    'user:',
    'password:'
]);
if ($options === false) {
    fwrite(STDOUT, "BH log=\"missing options\"\n");
    exit();
}
$pdo = null;
try {
    $pdo = new PDO(
        $options['dsn'],
        $options['user'],
        $options['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    $message = rawurlencode($e->getMessage());
    fwrite(STDOUT, "BH log=\"{$message}\"\n");
    exit();
}
while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if (empty($line)) {
        continue;
    }
    [$channel_id, $src, $args] = array_pad(explode(" ", $line, 3), 3, "");
    try {
        $stmt = $pdo->prepare('SELECT ip FROM allowed_ips WHERE ip = ?');
        $stmt->execute([
            $src
        ]);
        $result = $stmt->fetch();
        $stmt->closeCursor();
        if (isset($result['ip']) && strcmp($result['ip'], $src) === 0) {
            fwrite(STDOUT, "{$channel_id} OK\n");
        } else {
            fwrite(STDOUT, "{$channel_id} ERR\n");
        }
    } catch (PDOException $e) {
        fwrite(STDOUT, "{$channel_id} BH\n");
    }
}
