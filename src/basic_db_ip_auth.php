<?php
$options = getopt("", [
    'dsn:',
    'user:',
    'password:'
]);
if ($options === false) {
    fwrite(STDOUT, "ERR log=\"missing options\"\n");
    exit();
}
while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if (empty($line)) {
        continue;
    }
    list($channel_id, $src, $args) = array_pad(explode(" ", $line, 3), 3, "");
    try {
        $pdo = new PDO(
            $options['dsn'],
            $options['user'],
            $options['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT=>true,
            ]
        );
        $stmt = $pdo->prepare('SELECT ip FROM squid_allowed_ips WHERE ip = ?');
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
    } catch (\Exception $e) {
        //non-recoverable,Defined keywords log= is not working?? so using user=
        //http://www.squid-cache.org/Versions/v3/3.5/cfgman/external_acl_type.html
        $message = rawurlencode($src.' '.$e->getMessage());
        fwrite(STDOUT, "{$channel_id} ERR user=\"{$message}\"\n");
        exit(1);
    }
}
