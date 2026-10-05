<?php
declare(strict_types=1);

class DashboardController extends Controller
{
    public function index(): void
    {
        $userId = Auth::id();
        if (!$userId) {
            Response::redirect('/login');
        }

        $db = Database::pdo();

        // ---------- 1. Profil utilisateur + classe + série + rôle ----------
        $st = $db->prepare(
            "SELECT u.*,
                    c.name  AS class_name,  c.slug AS class_slug, c.exam,
                    s.code  AS series_code, s.label AS series_label,
                    r.code  AS role_code,   r.label AS role_label
             FROM users u
             LEFT JOIN classes c ON c.id = u.class_id
             LEFT JOIN series  s ON s.id = u.series_id
             LEFT JOIN roles   r ON r.id = u.role_id
             WHERE u.id = :id
             LIMIT 1"
        );
        $st->execute(['id' => $userId]);
        $user = $st->fetch();

        if (!$user) {
            Auth::logout();
            Response::redirect('/login');
        }

        // ---------- 2. Abonnement actif (le plus récent encore valide) ----------
        $st = $db->prepare(
            "SELECT sub.*, p.code AS plan_code, p.name AS plan_name, p.duration_days
             FROM subscriptions sub
             JOIN subscription_plans p ON p.id = sub.plan_id
             WHERE sub.user_id = :uid
               AND sub.status = 'active'
               AND sub.ends_at > NOW()
             ORDER BY sub.ends_at DESC
             LIMIT 1"
        );
        $st->execute(['uid' => $userId]);
        $subscription = $st->fetch() ?: null;

        // Jours restants
        $daysLeft = null;
        $isExpiringSoon = false;
        if ($subscription) {
            $endsAt   = new DateTime($subscription['ends_at']);
            $now      = new DateTime();
            $daysLeft = (int) $now->diff($endsAt)->days;
            $isExpiringSoon = $daysLeft <= (int) setting('expiry_reminder_days', '3');
        }

        // ---------- 3. Compteur : contenus accessibles ----------
        $contentCount = 0;
        $quizCount    = 0;
        if ($user['class_id']) {
            $st = $db->prepare(
                "SELECT COUNT(*) FROM contents c
                 JOIN chapters ch ON ch.id = c.chapter_id
                 JOIN subjects s  ON s.id  = ch.subject_id
                 WHERE s.class_id = :cid
                   AND (s.series_id IS NULL OR s.series_id = :sid)
                   AND c.is_published = 1"
            );
            $st->execute([
                'cid' => (int) $user['class_id'],
                'sid' => (int) ($user['series_id'] ?? 0),
            ]);
            $contentCount = (int) $st->fetchColumn();

            $st = $db->prepare(
                "SELECT COUNT(*) FROM quizzes q
                 JOIN subjects s ON s.id = q.subject_id
                 WHERE s.class_id = :cid
                   AND (s.series_id IS NULL OR s.series_id = :sid)
                   AND q.is_published = 1"
            );
            $st->execute([
                'cid' => (int) $user['class_id'],
                'sid' => (int) ($user['series_id'] ?? 0),
            ]);
            $quizCount = (int) $st->fetchColumn();
        }

        // ---------- 4. Contenus récents (4 derniers publiés) ----------
        $recentContents = [];
        if ($user['class_id']) {
            $st = $db->prepare(
                "SELECT c.id, c.title, c.type, c.access_level, c.published_at,
                        ch.title AS chapter_title,
                        s.name   AS subject_name, s.slug AS subject_slug
                 FROM contents c
                 JOIN chapters ch ON ch.id = c.chapter_id
                 JOIN subjects s  ON s.id  = ch.subject_id
                 WHERE s.class_id = :cid
                   AND (s.series_id IS NULL OR s.series_id = :sid)
                   AND c.is_published = 1
                 ORDER BY c.published_at DESC
                 LIMIT 4"
            );
            $st->execute([
                'cid' => (int) $user['class_id'],
                'sid' => (int) ($user['series_id'] ?? 0),
            ]);
            $recentContents = $st->fetchAll();
        }

        // ---------- 5. Dernier quiz + moyenne ----------
        $st = $db->prepare(
            "SELECT qa.score, qa.total, qa.finished_at, q.title, q.id AS quiz_id
             FROM quiz_attempts qa
             JOIN quizzes q ON q.id = qa.quiz_id
             WHERE qa.user_id = :uid AND qa.status = 'completed'
             ORDER BY qa.finished_at DESC
             LIMIT 1"
        );
        $st->execute(['uid' => $userId]);
        $lastQuiz = $st->fetch() ?: null;

        $st = $db->prepare(
            "SELECT AVG(score / NULLIF(total,0) * 100) AS average
             FROM quiz_attempts
             WHERE user_id = :uid AND status = 'completed'"
        );
        $st->execute(['uid' => $userId]);
        $quizAvg = $st->fetchColumn();
        $quizAvg = $quizAvg !== null ? round((float) $quizAvg, 1) : null;

        // ---------- 6. Notifications récentes (3 non lues) ----------
        $st = $db->prepare(
            "SELECT id, type, title, message, created_at
             FROM notifications
             WHERE user_id = :uid AND is_read = 0 AND channel = 'site'
             ORDER BY created_at DESC
             LIMIT 3"
        );
        $st->execute(['uid' => $userId]);
        $notifications = $st->fetchAll();

        // Compteur global non lues
        $st = $db->prepare(
            "SELECT COUNT(*) FROM notifications
             WHERE user_id = :uid AND is_read = 0 AND channel = 'site'"
        );
        $st->execute(['uid' => $userId]);
        $unreadCount = (int) $st->fetchColumn();

        // ---------- 7. Annonce active (bandeau haut de page) ----------
        $announcement = $db->query(
            "SELECT title, body
             FROM announcements
             WHERE is_active = 1
               AND (starts_at IS NULL OR starts_at <= NOW())
               AND (ends_at   IS NULL OR ends_at   >= NOW())
             ORDER BY created_at DESC
             LIMIT 1"
        )->fetch() ?: null;

        // ---------- 8. Historique abonnements (3 derniers) ----------
        $st = $db->prepare(
            "SELECT sub.*, p.name AS plan_name
             FROM subscriptions sub
             JOIN subscription_plans p ON p.id = sub.plan_id
             WHERE sub.user_id = :uid
             ORDER BY sub.created_at DESC
             LIMIT 3"
        );
        $st->execute(['uid' => $userId]);
        $history = $st->fetchAll();

        // ---------- Rendu ----------
        $this->render('user/dashboard', [
            'title'          => 'Mon espace',
            'user'           => $user,
            'subscription'   => $subscription,
            'daysLeft'       => $daysLeft,
            'isExpiringSoon' => $isExpiringSoon,
            'contentCount'   => $contentCount,
            'quizCount'      => $quizCount,
            'recentContents' => $recentContents,
            'lastQuiz'       => $lastQuiz,
            'quizAvg'        => $quizAvg,
            'notifications'  => $notifications,
            'unreadCount'    => $unreadCount,
            'announcement'   => $announcement,
            'history'        => $history,
        ]);
    }
}