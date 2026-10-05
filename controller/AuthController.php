<?php
declare(strict_types=1);

class AuthController extends Controller
{
    // =================================================================
    //  INSCRIPTION
    // =================================================================

    public function showRegister(): void
    {
        $db = Database::pdo();

        $classes = $db->query(
            "SELECT id, name, slug, cycle, exam
             FROM classes WHERE is_active = 1
             ORDER BY position"
        )->fetchAll();

        // Séries groupées par class_id : [class_id => [ {id, code, label}, ... ]]
        $seriesByClass = (new Series())->groupedByClass();

        $this->render('auth/register', [
            'title'         => 'Créer un compte',
            'classes'       => $classes,
            'seriesByClass' => $seriesByClass,
            'old'           => Session::flash('old')    ?? [],
            'errors'        => Session::flash('errors') ?? [],
        ], 'auth');
    }

    public function register(): void
    {
        $input = $_POST;

        // ---------- 1. Récupération + nettoyage ----------
        $old = [
            'first_name'  => trim($input['first_name']  ?? ''),
            'last_name'   => trim($input['last_name']   ?? ''),
            'phone'       => trim($input['phone']       ?? ''),
            'email'       => trim($input['email']       ?? ''),
            'class_id'    => trim($input['class_id']    ?? ''),
            'series_id'   => trim($input['series_id']   ?? ''),
            'school'      => trim($input['school']      ?? ''),
            'city'        => trim($input['city']        ?? ''),
            'school_year' => trim($input['school_year'] ?? ''),
        ];

        $errors = [];

        // ---------- 2. Validation ----------
        if ($old['first_name'] === '') {
            $errors['first_name'] = 'Le prénom est obligatoire.';
        } elseif (mb_strlen($old['first_name']) < 2) {
            $errors['first_name'] = 'Le prénom est trop court.';
        }

        if ($old['last_name'] === '') {
            $errors['last_name'] = 'Le nom est obligatoire.';
        } elseif (mb_strlen($old['last_name']) < 2) {
            $errors['last_name'] = 'Le nom est trop court.';
        }

        $normalizedPhone = $this->normalizePhone($old['phone']);
        if ($old['phone'] === '') {
            $errors['phone'] = 'Le numéro de téléphone est obligatoire.';
        } elseif ($normalizedPhone === null) {
            $errors['phone'] = 'Numéro invalide. Format : 6XX XX XX XX (Cameroun).';
        }

        if ($old['email'] !== '' && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Adresse e-mail invalide.';
        }

        $password  = (string) ($input['password'] ?? '');
        $password2 = (string) ($input['password_confirm'] ?? '');
        if (strlen($password) < 6) {
            $errors['password'] = 'Au moins 6 caractères.';
        } elseif ($password !== $password2) {
            $errors['password'] = 'Les mots de passe ne correspondent pas.';
        }

        if ($old['class_id'] === '') {
            $errors['class_id'] = 'Choisis ta classe.';
        }

        // --- Validation série dynamique ---
        $classId = (int) $old['class_id'];
        $series  = (new Series())->byClass($classId);

        if (!empty($series)) {
            if ($old['series_id'] === '') {
                $errors['series_id'] = 'Choisis ta série.';
            } else {
                // Vérifier que la série appartient bien à la classe
                $valid = false;
                foreach ($series as $s) {
                    if ((int) $s['id'] === (int) $old['series_id']) {
                        $valid = true;
                        break;
                    }
                }
                if (!$valid) {
                    $errors['series_id'] = 'Série invalide pour cette classe.';
                }
            }
        } else {
            // Classe sans série → on force null
            $old['series_id'] = '';
        }

        if ($old['school_year'] !== '' && !preg_match('/^\d{4}-\d{4}$/', $old['school_year'])) {
            $errors['school_year'] = 'Format attendu : 2025-2026.';
        }

        // ---------- 3. Unicité ----------
        $userModel = new User();
        if ($normalizedPhone && $userModel->findByPhone($normalizedPhone)) {
            $errors['phone'] = 'Ce numéro est déjà utilisé.';
        }
        if ($old['email'] !== '' && $userModel->findByEmail($old['email'])) {
            $errors['email'] = 'Cet e-mail est déjà utilisé.';
        }

        // ---------- 4. Retour en cas d'erreur ----------
        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $old);
            Response::redirect('/register');
        }

        // ---------- 5. Création du compte ----------
        $db = Database::pdo();

        $roleId = (int) $db
            ->query("SELECT id FROM roles WHERE code = 'student' LIMIT 1")
            ->fetchColumn();

        $userId = $userModel->create([
            'role_id'       => $roleId,
            'first_name'    => $old['first_name'],
            'last_name'     => $old['last_name'],
            'phone'         => $normalizedPhone,
            'email'         => $old['email'] !== '' ? $old['email'] : null,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'class_id'      => $classId,
            'series_id'     => $old['series_id'] !== '' ? (int) $old['series_id'] : null,
            'school'        => $old['school']      !== '' ? $old['school']      : null,
            'city'          => $old['city']        !== '' ? $old['city']        : null,
            'school_year'   => $old['school_year'] !== '' ? $old['school_year'] : null,
            'status'        => 'active',
        ]);

        // ---------- 6. Notification site ----------
        $db->prepare(
            "INSERT INTO notifications (user_id, type, channel, title, message, sent_at)
             VALUES (:uid, 'registration', 'site', :title, :msg, NOW())"
        )->execute([
            'uid'   => $userId,
            'title' => 'Bienvenue sur ' . setting('site_name', 'SKYLEARN'),
            'msg'   => 'Ton compte a bien été créé. Choisis une formule pour accéder aux contenus.',
        ]);

        // ---------- 7. E-mail de bienvenue ----------
        if ($old['email'] !== '') {
            $this->sendWelcomeEmail($userId, $old, $normalizedPhone);
        }

        // ---------- 8. Connexion automatique ----------
        Auth::login($userId, 'student');

        // ---------- 9. Journal ----------
        $db->prepare(
            "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, ip_address)
             VALUES (:uid, 'user.register', 'user', :eid, :ip)"
        )->execute([
            'uid' => $userId,
            'eid' => $userId,
            'ip'  => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        Response::redirect('/dashboard');
    }

    // =================================================================
    //  HELPERS
    // =================================================================

    private function normalizePhone(string $raw): ?string
    {
        $digits = preg_replace('/\D/', '', $raw);
        if ($digits === '') return null;

        if (str_starts_with($digits, '237')) {
            $digits = substr($digits, 3);
        }

        if (strlen($digits) === 9 && $digits[0] === '6') {
            return '+237' . $digits;
        }

        return null;
    }

    private function sendWelcomeEmail(int $userId, array $data, string $phone): void
    {
        $db = Database::pdo();

        $className = (string) $db
            ->query("SELECT name FROM classes WHERE id = " . (int) $data['class_id'])
            ->fetchColumn();

        $seriesLabel = '';
        if (!empty($data['series_id'])) {
            $seriesLabel = (string) $db
                ->query("SELECT label FROM series WHERE id = " . (int) $data['series_id'])
                ->fetchColumn();
        }

        ob_start();
        $firstName   = $data['first_name'];
        $className   = $className ?: 'Non précisée';
        $seriesLabel = $seriesLabel ?: '';
        $phone       = $phone;
        require BASE_PATH . '/view/emails/welcome.php';
        $html = ob_get_clean();

        $mailer = new Mailer();
        $ok = $mailer->send(
            $data['email'],
            'Bienvenue sur ' . setting('site_name', 'SKYLEARN') . ' 🎓',
            $html
        );

        $db->prepare(
            "INSERT INTO notifications (user_id, type, channel, title, message, sent_at)
             VALUES (:uid, 'registration', 'email', :title, :msg, :sent)"
        )->execute([
            'uid'   => $userId,
            'title' => 'Bienvenue sur ' . setting('site_name', 'SKYLEARN'),
            'msg'   => 'E-mail de bienvenue',
            'sent'  => $ok ? date('Y-m-d H:i:s') : null,
        ]);
    }

    // =================================================================
    //  AUTRES MÉTHODES
    // =================================================================

public function showLogin(): void
{
    $this->render('auth/login', [
        'title' => 'Connexion',
        'old'   => Session::flash('old')    ?? [],
        'errors'=> Session::flash('errors') ?? [],
        'info'  => Session::flash('info')   ?? null,
    ], 'auth');
}

public function login(): void
{
    $input      = $_POST;
    $identifier = trim($input['identifier'] ?? '');
    $password   = (string) ($input['password'] ?? '');
    $remember   = isset($input['remember']);

    $errors = [];
    $old    = ['identifier' => $identifier];

    // ---------- 1. Validation basique ----------
    if ($identifier === '') {
        $errors['identifier'] = 'Renseigne ton téléphone ou ton e-mail.';
    }
    if ($password === '') {
        $errors['password'] = 'Renseigne ton mot de passe.';
    }

    // ---------- 2. Anti-bruteforce : max 5 tentatives / 15 min par IP ----------
    $db = Database::pdo();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    $recentFails = (int) $db->prepare(
        "SELECT COUNT(*) FROM login_attempts
         WHERE ip_address = :ip
           AND success = 0
           AND attempted_at >= (NOW() - INTERVAL 15 MINUTE)"
    )->execute(['ip' => $ip]) ?: 0;

    $stmt = $db->prepare(
        "SELECT COUNT(*) FROM login_attempts
         WHERE ip_address = :ip
           AND success = 0
           AND attempted_at >= (NOW() - INTERVAL 15 MINUTE)"
    );
    $stmt->execute(['ip' => $ip]);
    $recentFails = (int) $stmt->fetchColumn();

    if ($recentFails >= 5) {
        $errors['general'] = 'Trop de tentatives échouées. Réessaie dans 15 minutes.';
    }

    // ---------- 3. Recherche de l'utilisateur ----------
    $user = null;
    if (!$errors) {
        $userModel = new User();

        // Détecte si c'est un e-mail ou un téléphone
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = $userModel->findByEmail($identifier);
        } else {
            $phone = $this->normalizePhone($identifier);
            if ($phone) {
                $user = $userModel->findByPhone($phone);
            }
        }

        // ---------- 4. Vérification du mot de passe ----------
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors['general'] = 'Identifiants incorrects.';
        } elseif ($user['status'] !== 'active') {
            $errors['general'] = 'Ton compte est suspendu. Contacte l\'administrateur.';
        }
    }

    // ---------- 5. Journalisation de la tentative ----------
    $logAttempt = $db->prepare(
        "INSERT INTO login_attempts (identifier, ip_address, success)
         VALUES (:id, :ip, :ok)"
    );
    $logAttempt->execute([
        'id' => $identifier,
        'ip' => $ip,
        'ok' => empty($errors) ? 1 : 0,
    ]);

    // ---------- 6. En cas d'erreur : retour ----------
    if ($errors) {
        Session::flash('errors', $errors);
        Session::flash('old', $old);
        Response::redirect('/login');
    }

    // ---------- 7. Mise à jour last_login_at ----------
    $db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = :id")
       ->execute(['id' => $user['id']]);

    // ---------- 8. Rôle ----------
    $roleCode = (string) $db
        ->query("SELECT code FROM roles WHERE id = " . (int) $user['role_id'])
        ->fetchColumn();

    // ---------- 9. Connexion ----------
    Auth::login((int) $user['id'], $roleCode);

    // ---------- 10. "Se souvenir de moi" (30 jours) ----------
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        // ⚠️ Pour rester simple, on stocke le token dans une colonne à ajouter
        // (voir section "Amélioration" plus bas). Sinon, on saute cette étape.
        // setcookie('skl_remember', $user['id'] . ':' . $token, [
        //     'expires'  => time() + 60 * 60 * 24 * 30,
        //     'path'     => '/',
        //     'secure'   => !empty($_SERVER['HTTPS']),
        //     'httponly' => true,
        //     'samesite' => 'Lax',
        // ]);
    }

    // ---------- 11. Journal ----------
    $db->prepare(
        "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, ip_address)
         VALUES (:uid, 'user.login', 'user', :eid, :ip)"
    )->execute([
        'uid' => (int) $user['id'],
        'eid' => (int) $user['id'],
        'ip'  => $ip,
    ]);

    // ---------- 12. Redirection selon rôle ----------
    if ($roleCode === 'admin') {
        Response::redirect('/admin');
    }
    Response::redirect('/dashboard');
}

// =================================================================
//  MOT DE PASSE OUBLIÉ
// =================================================================

public function showForgot(): void
{
    $this->render('auth/forgot-password', [
        'title'  => 'Mot de passe oublié',
        'old'    => Session::flash('old')   ?? [],
        'errors' => Session::flash('errors') ?? [],
        'info'   => Session::flash('info')  ?? null,
    ], 'auth');
}

public function sendReset(): void
{
    $email = trim($_POST['email'] ?? '');
    $errors = [];

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Renseigne une adresse e-mail valide.';
    }

    if ($errors) {
        Session::flash('errors', $errors);
        Session::flash('old', ['email' => $email]);
        Response::redirect('/forgot-password');
    }

    $db = Database::pdo();
    $userModel = new User();
    $user = $userModel->findByEmail($email);

    // ⚠️ On ne révèle JAMAIS si l'e-mail existe ou non.
    // Message générique dans tous les cas.
    $genericMessage = 'Si un compte est associé à cet e-mail, un lien de réinitialisation vient de t\'être envoyé. Pense à vérifier tes spams.';

    if ($user) {
        // -------- Anti-spam : pas plus d'1 demande / 5 min --------
        $recent = (int) $db->prepare(
            "SELECT COUNT(*) FROM password_resets
             WHERE user_id = :uid AND created_at >= (NOW() - INTERVAL 5 MINUTE)"
        ) ?: 0;

        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM password_resets
             WHERE user_id = :uid AND created_at >= (NOW() - INTERVAL 5 MINUTE)"
        );
        $stmt->execute(['uid' => (int) $user['id']]);
        $recent = (int) $stmt->fetchColumn();

        if ($recent === 0) {
            // -------- Invalider les anciens jetons --------
            $db->prepare("UPDATE password_resets SET used_at = NOW()
                          WHERE user_id = :uid AND used_at IS NULL")
               ->execute(['uid' => (int) $user['id']]);

            // -------- Créer un nouveau jeton --------
            $token = bin2hex(random_bytes(32));            // 64 caractères
            $hash  = hash('sha256', $token);                // stocké en BD

            $db->prepare(
                "INSERT INTO password_resets (user_id, token_hash, expires_at)
                 VALUES (:uid, :hash, (NOW() + INTERVAL 1 HOUR))"
            )->execute([
                'uid'  => (int) $user['id'],
                'hash' => $hash,
            ]);

            // -------- Envoyer l'e-mail --------
            $this->sendResetEmail($user, $token);
        }
    }

    Session::flash('info', $genericMessage);
    Response::redirect('/forgot-password');
}

// =================================================================
//  RÉINITIALISATION
// =================================================================

public function showReset(...$args): void
{
    $token = (string) ($args[0] ?? ($_GET['token'] ?? ''));

    if ($token === '' || !$this->isValidToken($token)) {
        $this->render('auth/reset-password', [
            'title' => 'Nouveau mot de passe',
            'token' => '',
            'invalid' => true,
        ], 'auth');
        return;
    }

    $this->render('auth/reset-password', [
        'title'   => 'Nouveau mot de passe',
        'token'   => $token,
        'invalid' => false,
        'old'     => Session::flash('old')    ?? [],
        'errors'  => Session::flash('errors') ?? [],
    ], 'auth');
}

public function reset(): void
{
    $token    = trim($_POST['token']    ?? '');
    $password = (string) ($_POST['password']         ?? '');
    $password2= (string) ($_POST['password_confirm'] ?? '');

    $errors = [];

    if ($token === '' || !$this->isValidToken($token)) {
        $errors['general'] = 'Lien invalide ou expiré. Refais une demande.';
    }

    if (strlen($password) < 6) {
        $errors['password'] = 'Au moins 6 caractères.';
    } elseif ($password !== $password2) {
        $errors['password'] = 'Les mots de passe ne correspondent pas.';
    }

    if ($errors) {
        Session::flash('errors', $errors);
        Session::flash('old', ['token' => $token]);
        Response::redirect('/reset-password/' . $token);
    }

    $db = Database::pdo();
    $hash = hash('sha256', $token);

    // -------- Récupérer le jeton --------
    $stmt = $db->prepare(
        "SELECT id, user_id FROM password_resets
         WHERE token_hash = :h
           AND used_at IS NULL
           AND expires_at > NOW()
         LIMIT 1"
    );
    $stmt->execute(['h' => $hash]);
    $row = $stmt->fetch();

    if (!$row) {
        Session::flash('errors', ['general' => 'Lien invalide ou expiré.']);
        Response::redirect('/forgot-password');
    }

    // -------- Mettre à jour le mot de passe --------
    $db->prepare("UPDATE users SET password_hash = :h WHERE id = :id")
       ->execute([
           'h'  => password_hash($password, PASSWORD_DEFAULT),
           'id' => (int) $row['user_id'],
       ]);

    // -------- Marquer le jeton comme utilisé --------
    $db->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = :id")
       ->execute(['id' => (int) $row['id']]);

    // -------- Invalider les autres jetons du user --------
    $db->prepare("UPDATE password_resets SET used_at = NOW()
                  WHERE user_id = :uid AND used_at IS NULL")
       ->execute(['uid' => (int) $row['user_id']]);

    // -------- Notification site --------
    $db->prepare(
        "INSERT INTO notifications (user_id, type, channel, title, message, sent_at)
         VALUES (:uid, 'password_reset', 'site', :title, :msg, NOW())"
    )->execute([
        'uid'   => (int) $row['user_id'],
        'title' => 'Mot de passe modifié',
        'msg'   => 'Ton mot de passe vient d\'être réinitialisé.',
    ]);

    // -------- Journal --------
    $db->prepare(
        "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, ip_address)
         VALUES (:uid, 'user.password_reset', 'user', :eid, :ip)"
    )->execute([
        'uid' => (int) $row['user_id'],
        'eid' => (int) $row['user_id'],
        'ip'  => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);

    Session::flash('info', 'Mot de passe modifié avec succès. Connecte-toi.');
    Response::redirect('/login');
}

// =================================================================
//  HELPERS PRIVÉS
// =================================================================

private function isValidToken(string $token): bool
{
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        return false;
    }

    $st = Database::pdo()->prepare(
        "SELECT COUNT(*) FROM password_resets
         WHERE token_hash = :h
           AND used_at IS NULL
           AND expires_at > NOW()"
    );
    $st->execute(['h' => hash('sha256', $token)]);

    return (int) $st->fetchColumn() > 0;
}

private function sendResetEmail(array $user, string $token): void
{
    $siteName = setting('site_name', 'SKYLEARN');
    $url      = rtrim(setting('app_url', ''), '/');

    // Détecte automatiquement l'URL
    if ($url === '') {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $url    = $scheme . '://' . $host;
    }
    $resetUrl = $url . '/reset-password/' . $token;

    ob_start();
    $firstName = $user['first_name'];
    $resetUrl  = $resetUrl;
    require BASE_PATH . '/view/emails/reset-password.php';
    $html = ob_get_clean();

    $mailer = new Mailer();
    $ok = $mailer->send(
        $user['email'],
        'Réinitialisation de ton mot de passe — ' . $siteName,
        $html
    );

    // Notifier (même si échec, on log)
    Database::pdo()->prepare(
        "INSERT INTO notifications (user_id, type, channel, title, message, sent_at)
         VALUES (:uid, 'password_reset_request', 'email', :title, :msg, :sent)"
    )->execute([
        'uid'   => (int) $user['id'],
        'title' => 'Réinitialisation de mot de passe',
        'msg'   => 'Lien envoyé par e-mail',
        'sent'  => $ok ? date('Y-m-d H:i:s') : null,
    ]);
}

    public function logout(): void      { Auth::logout(); Response::redirect('/'); }
}