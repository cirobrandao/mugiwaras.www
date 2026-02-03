<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Database;

$options = getopt('', ['file:']);
if (empty($options['file'])) {
    echo "Usage: php bin/import_users.php --file=users.json\n";
    exit(1);
}
$file = $options['file'];
if (!is_readable($file)) {
    echo "File not found or not readable: $file\n";
    exit(2);
}
$json = file_get_contents($file);
$data = json_decode($json, true);
if (!is_array($data)) {
    echo "Invalid JSON or not an array of users.\n";
    exit(3);
}
$pdo = Database::connection();
$created = 0;
$updated = 0;
foreach ($data as $rec) {
    if (empty($rec['email']) || empty($rec['username'])) {
        echo "Skipping record with missing email/username\n";
        continue;
    }
    $email = trim((string)$rec['email']);
    $username = trim((string)$rec['username']);
    // map phone
    $phone = preg_replace('/\D+/', '', (string)($rec['phone'] ?? ''));
    $phone_country = '+55';
    if (strpos($phone, '55') === 0) {
        $phone_country = '+55';
        // remove leading country if present
        $phone = preg_replace('/^55/', '', $phone);
    }
    // birthdate: input likely Y-m-d -> store as d-m-Y to match app format
    $birth = (string)($rec['birthdate'] ?? '');
    $birth_date = '';
    if ($birth !== '') {
        $dt = DateTimeImmutable::createFromFormat('Y-m-d', $birth);
        if ($dt) {
            $birth_date = $dt->format('d-m-Y');
        } else {
            // try generic parse
            try { $dt = new DateTimeImmutable($birth); $birth_date = $dt->format('d-m-Y'); } catch (Throwable $e) { $birth_date = ''; }
        }
    }

    // Decide role vs access_tier
    $roleField = (string)($rec['role'] ?? '');
    $role = 'none';
    $access_tier = 'user';
    $validRoles = ['none','admin','moderator','uploader','superadmin'];
    if (in_array($roleField, $validRoles, true)) {
        $role = $roleField;
    } elseif ($roleField !== '') {
        $access_tier = $roleField;
    }
    // allow explicit access_expires_at
    $subExpires = null;
    if (!empty($rec['access_expires_at'])) {
        try {
            $tmp = new DateTimeImmutable($rec['access_expires_at']);
            $subExpires = $tmp->format('Y-m-d H:i:s');
        } catch (Throwable $e) {
            $subExpires = null;
        }
    }

    // registration and login timestamps
    $registeredAt = null;
    if (!empty($rec['registered_at'])) {
        try { $registeredAt = (new DateTimeImmutable($rec['registered_at']))->format('Y-m-d H:i:s'); } catch (Throwable $e) { $registeredAt = null; }
    }
    $lastLoginAt = null;
    if (!empty($rec['last_login_at'])) {
        try { $lastLoginAt = (new DateTimeImmutable($rec['last_login_at']))->format('Y-m-d H:i:s'); } catch (Throwable $e) { $lastLoginAt = null; }
    }

    $ipUltimo = $rec['current_login_ip'] ?? ($rec['last_login_ip'] ?? null);
    $ipPenultimo = $rec['last_login_ip'] ?? null;

    // check existing by email or username
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :e OR username = :u LIMIT 1');
    $stmt->execute(['e' => $email, 'u' => $username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $id = (int)$row['id'];
        $sql = 'UPDATE users SET username = :username, phone = :phone, phone_country = :phone_country, birth_date = :birth_date, access_tier = :access_tier, role = :role, subscription_expires_at = :sub_expires, data_ultimo_login = :last_login, ip_ultimo_acesso = :ip_ultimo, ip_penultimo_acesso = :ip_penultimo WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'phone' => $phone,
            'phone_country' => $phone_country,
            'birth_date' => $birth_date ?: '01-01-1970',
            'access_tier' => $access_tier,
            'role' => $role,
            'sub_expires' => $subExpires,
            'last_login' => $lastLoginAt,
            'ip_ultimo' => $ipUltimo,
            'ip_penultimo' => $ipPenultimo,
            'id' => $id,
        ]);
        echo "Updated user id={$id} email={$email}\n";
        $updated++;
    } else {
        // create
        $password = bin2hex(random_bytes(8));
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $referralCode = bin2hex(random_bytes(4));
        $sql = 'INSERT INTO users (username,email,phone,phone_country,phone_has_whatsapp,birth_date,password_hash,access_tier,role,referral_code,referrer_id,ip_cadastro,ip_ultimo_acesso,ip_penultimo_acesso,data_registro,data_ultimo_login,subscription_expires_at) VALUES (:username,:email,:phone,:phone_country,:phone_has_whatsapp,:birth_date,:password_hash,:access_tier,:role,:referral_code,:referrer_id,:ip_cadastro,:ip_ultimo_acesso,:ip_penultimo_acesso,:data_registro,:data_ultimo_login,:subscription_expires_at)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'phone_country' => $phone_country,
            'phone_has_whatsapp' => isset($rec['whatsapp_opt_in']) ? (int)$rec['whatsapp_opt_in'] : 1,
            'birth_date' => $birth_date ?: '01-01-1970',
            'password_hash' => $passwordHash,
            'access_tier' => $access_tier,
            'role' => $role,
            'referral_code' => $referralCode,
            'referrer_id' => null,
            'ip_cadastro' => $ipUltimo,
            'ip_ultimo_acesso' => $ipUltimo,
            'ip_penultimo_acesso' => $ipPenultimo,
            'data_registro' => $registeredAt ?: date('Y-m-d H:i:s'),
            'data_ultimo_login' => $lastLoginAt,
            'subscription_expires_at' => $subExpires,
        ]);
        $newId = (int)$pdo->lastInsertId();
        echo "Created user id={$newId} email={$email} password={$password}\n";
        $created++;
    }
}

echo "Done. Created={$created} Updated={$updated}\n";

exit(0);
