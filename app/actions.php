<?php

function handle_post($APP_SCREENS, $STRINGS, &$user)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $action = $_POST['action'] ?? '';
    if ($action === '') {
        return;
    }

    $pdo = db();

    switch ($action) {
        case 'login':
            // Login por email y contraseña
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($email === '' || $password === '') {
                $_SESSION['login_error'] = 'Ingresá email y contraseña.';
                header('Location: ?screen=login&lang=' . urlencode($_SESSION['lang'] ?? 'es'));
                exit;
            }

            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $row = $stmt->fetch();

            if ($row && !empty($row['password_hash']) && password_verify($password, $row['password_hash'])) {
                $_SESSION['user_id'] = $row['id'];
                $user = $row;

                header('Location: ?screen=trips&lang=' . urlencode($_SESSION['lang'] ?? 'es'));
                exit;
            } else {
                $_SESSION['login_error'] = 'Email o contraseña incorrectos.';
                header('Location: ?screen=login&lang=' . urlencode($_SESSION['lang'] ?? 'es'));
                exit;
            }

        case 'logout':
            // Cerrar sesión y volver al login
            $lang = $_SESSION['lang'] ?? 'es';
            $_SESSION = ['lang' => $lang];
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
            header('Location: ?screen=login&lang=' . urlencode($lang));
            exit;


        case 'update_settings':
            if (!$user) break;

            $showBio       = !empty($_POST['show_bio']) ? 1 : 0;
            $sosEnabled    = !empty($_POST['sos_enabled']) ? 1 : 0;
            $shareLocation = !empty($_POST['share_location']) ? 1 : 0;

            $stmt = $pdo->prepare('UPDATE users
                                   SET show_bio = :show_bio,
                                       sos_enabled = :sos_enabled,
                                       share_location = :share_location
                                   WHERE id = :id');
            $stmt->execute([
                ':show_bio'       => $showBio,
                ':sos_enabled'    => $sosEnabled,
                ':share_location' => $shareLocation,
                ':id'             => $user['id'],
            ]);

            $user['show_bio']       = $showBio;
            $user['sos_enabled']    = $sosEnabled;
            $user['share_location'] = $shareLocation;

            header('Location: ?screen=settings&lang=' . urlencode($_SESSION['lang'] ?? 'es'));
            exit;

case 'set_locale':
            $new = $_POST['lang'] ?? 'es';
            if (valid_locale($new, $STRINGS)) {
                $_SESSION['lang'] = $new;
            }
            $screen = $_GET['screen'] ?? 'landing';
            header('Location: ?screen=' . urlencode($screen) . '&lang=' . urlencode($_SESSION['lang']));
            exit;

        
        case 'update_profile':
            if (!$user) break;

            $name        = trim($_POST['name'] ?? '');
            $country     = trim($_POST['country'] ?? '');
            $email       = trim($_POST['email'] ?? '');
            $bio         = trim($_POST['bio'] ?? '');
            $langsStr    = trim($_POST['languages'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';

            // Idiomas
            $langs = [];
            if ($langsStr !== '') {
                $parts = array_map('trim', explode(',', $langsStr));
                $parts = array_filter($parts, fn($v) => $v !== '');
                if (!empty($parts)) {
                    $langs = array_values($parts);
                }
            }

            // -------- FOTO DE PERFIL --------
            $avatarFilename = $user['avatar'] ?? null;

            if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $tmpPath = $_FILES['avatar']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];

                if (in_array($ext, $allowed, true)) {
                    $uploadDir = dirname(__DIR__) . '/public/uploads/avatars';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0775, true);
                    }

                    $newName  = 'user_' . $user['id'] . '_' . time() . '.' . $ext;
                    $destPath = $uploadDir . '/' . $newName;

                    if (move_uploaded_file($tmpPath, $destPath)) {
                        $avatarFilename = $newName;
                    }
                }
            }

            // Password
            $passwordHash = $user['password_hash'] ?? null;
            if ($newPassword !== '') {
                $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            $stmt = $pdo->prepare('UPDATE users
                                   SET name = :name,
                                       country = :country,
                                       email = :email,
                                       bio = :bio,
                                       languages = :langs,
                                       password_hash = :password_hash,
                                       avatar = :avatar
                                   WHERE id = :id');
            $stmt->execute([
                ':name'          => $name !== '' ? $name : $user['name'],
                ':country'       => $country !== '' ? $country : $user['country'],
                ':email'         => $email !== '' ? $email : ($user['email'] ?? null),
                ':bio'           => $bio,
                ':langs'         => $langs ? json_encode($langs, JSON_UNESCAPED_UNICODE) : null,
                ':password_hash' => $passwordHash,
                ':avatar'        => $avatarFilename,
                ':id'            => $user['id'],
            ]);

            if ($name !== '')    $user['name']    = $name;
            if ($country !== '') $user['country'] = $country;
            if ($email !== '')   $user['email']   = $email;
            $user['bio']           = $bio;
            $user['languages']     = $langs ? json_encode($langs, JSON_UNESCAPED_UNICODE) : null;
            $user['password_hash'] = $passwordHash;
            $user['avatar']        = $avatarFilename;

            header('Location: ?screen=profile&lang=' . urlencode($_SESSION['lang'] ?? 'es'));
            exit;

        case 'add_trip':
            if (!$user) break;

            $city  = trim($_POST['city'] ?? '');
            $start = $_POST['start'] ?? '';
            $end   = $_POST['end'] ?? '';

            if ($city !== '' && $start !== '' && $end !== '') {
                $stmt = $pdo->prepare('INSERT INTO trips (user_id, city, start_date, end_date, created_at)
                                       VALUES (:uid, :city, :start, :end, NOW())');
                $stmt->execute([
                    ':uid'   => $user['id'],
                    ':city'  => $city,
                    ':start' => $start,
                    ':end'   => $end,
                ]);
            }

            header('Location: ?screen=trips&lang=' . urlencode($_SESSION['lang']));
            exit;

        case 'create_plan':
            if (!$user) break;

            $title    = trim($_POST['title'] ?? '');
            $when     = trim($_POST['when'] ?? '');
            $where    = trim($_POST['where'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 6);
            $min      = (int)($_POST['min'] ?? 1);
            $tripId   = isset($_POST['trip_id']) ? (int)$_POST['trip_id'] : null;

            if ($title !== '' && $when !== '' && $where !== '') {
                $stmt = $pdo->prepare('INSERT INTO plans
                    (trip_id, title, when_at, where_text, capacity, min_people, created_by, created_at)
                    VALUES (:trip_id, :title, :when_at, :where_text, :cap, :minp, :uid, NOW())');
                $stmt->execute([
                    ':trip_id'    => $tripId,
                    ':title'      => $title,
                    ':when_at'    => $when,
                    ':where_text' => $where,
                    ':cap'        => $capacity,
                    ':minp'       => $min,
                    ':uid'        => $user['id'],
                ]);
            }

            $redirect = '?screen=plans&lang=' . urlencode($_SESSION['lang']);
            if ($tripId) {
                $redirect .= '&trip_id=' . $tripId;
            }
            header('Location: ' . $redirect);
            exit;

        case 'join_plan':
            if (!$user) break;

            $planId = (int)($_POST['plan_id'] ?? 0);
            if ($planId > 0) {
                $stmt = $pdo->prepare('SELECT capacity,
                                              (SELECT COUNT(*) FROM plan_participants pp WHERE pp.plan_id = p.id) AS current
                                       FROM plans p WHERE p.id = :pid');
                $stmt->execute([':pid' => $planId]);
                $row = $stmt->fetch();

                if ($row) {
                    $hasSpace = empty($row['capacity']) || (int)$row['current'] < (int)$row['capacity'];

                    if ($hasSpace) {
                        $stmt = $pdo->prepare('INSERT IGNORE INTO plan_participants (plan_id, user_id)
                                               VALUES (:pid, :uid)');
                        $stmt->execute([
                            ':pid' => $planId,
                            ':uid' => $user['id'],
                        ]);
                    }
                }
            }

            header('Location: ?screen=plans&lang=' . urlencode($_SESSION['lang']));
            exit;

        case 'leave_plan':
            if (!$user) break;

            $planId = (int)($_POST['plan_id'] ?? 0);
            if ($planId > 0) {
                $stmt = $pdo->prepare('DELETE FROM plan_participants WHERE plan_id = :pid AND user_id = :uid');
                $stmt->execute([
                    ':pid' => $planId,
                    ':uid' => $user['id'],
                ]);
            }

            header('Location: ?screen=plans&lang=' . urlencode($_SESSION['lang']));
            exit;

        case 'join_trip':
            if (!$user) break;

            $tripId = (int)($_POST['trip_id'] ?? 0);
            if ($tripId > 0) {
                $stmt = $pdo->prepare('INSERT IGNORE INTO trip_participants (trip_id, user_id, created_at)
                                       VALUES (:tid, :uid, NOW())');
                $stmt->execute([
                    ':tid' => $tripId,
                    ':uid' => $user['id'],
                ]);
            }

            header('Location: ?screen=trips&lang=' . urlencode($_SESSION['lang']));
            exit;

        case 'leave_trip':
            if (!$user) break;

            $tripId = (int)($_POST['trip_id'] ?? 0);
            if ($tripId > 0) {
                $stmt = $pdo->prepare('DELETE FROM trip_participants WHERE trip_id = :tid AND user_id = :uid');
                $stmt->execute([
                    ':tid' => $tripId,
                    ':uid' => $user['id'],
                ]);
            }

            header('Location: ?screen=trips&lang=' . urlencode($_SESSION['lang']));
            exit;

        case 'send_group_message':
            if (!$user) break;

            $tripId = (int)($_POST['trip_id'] ?? 0);
            $message = trim($_POST['message'] ?? '');

            // Verificar que el usuario sea dueño del viaje o participante
            $canPost = false;
            if ($tripId > 0 && $message !== '') {
                $stmt = $pdo->prepare('SELECT 1 FROM trips WHERE id = :tid AND user_id = :uid
                                       UNION
                                       SELECT 1 FROM trip_participants WHERE trip_id = :tid AND user_id = :uid
                                       LIMIT 1');
                $stmt->execute([
                    ':tid' => $tripId,
                    ':uid' => $user['id'],
                ]);
                $canPost = (bool)$stmt->fetchColumn();

                if ($canPost) {
                    $stmt = $pdo->prepare('INSERT INTO group_messages (trip_id, user_id, message, created_at)
                                           VALUES (:tid, :uid, :msg, NOW())');
                    $stmt->execute([
                        ':tid' => $tripId,
                        ':uid' => $user['id'],
                        ':msg' => $message,
                    ]);
                }
            }

            header('Location: ?screen=group&trip_id=' . $tripId . '&lang=' . urlencode($_SESSION['lang']));
            exit;

        case 'add_tip':
            if (!$user) break;


            $text = trim($_POST['text'] ?? '');
            $url  = trim($_POST['url'] ?? '');
            if ($text !== '') {
                $stmt = $pdo->prepare('INSERT INTO tips (user_id, text, url, created_at)
                                       VALUES (:uid, :t, :u, NOW())');
                $stmt->execute([
                    ':uid' => $user['id'],
                    ':t'   => $text,
                    ':u'   => $url ?: null,
                ]);
            }
            header('Location: ?screen=recommendations&lang=' . urlencode($_SESSION['lang']));
            exit;
    }
}

// Avatar upload handler added
