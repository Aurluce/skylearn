<?php
declare(strict_types=1);

class ContentController extends Controller
{
    public function index(): void
    {
        $db = Database::pdo();
        $classes = $db->query(
            "SELECT c.id, c.name, c.slug, c.cycle, c.exam,
                    COUNT(DISTINCT s.id) AS subject_count,
                    COUNT(DISTINCT ch.id) AS chapter_count,
                    COUNT(DISTINCT content.id) AS content_count
             FROM classes c
             LEFT JOIN subjects s ON s.class_id = c.id AND s.is_active = 1
             LEFT JOIN chapters ch ON ch.subject_id = s.id AND ch.is_active = 1
             LEFT JOIN contents content ON content.chapter_id = ch.id AND content.is_published = 1
             WHERE c.is_active = 1
             GROUP BY c.id, c.name, c.slug, c.cycle, c.exam, c.position
             ORDER BY c.position"
        )->fetchAll();

        $this->render('pages/courses', [
            'title'   => 'Cours par classe',
            'classes' => $classes,
        ]);
    }

    public function classe(...$args): void
    {
        $db = Database::pdo();
        $statement = $db->prepare(
            "SELECT id, name, slug, cycle, exam
             FROM classes
             WHERE slug = :slug AND is_active = 1
             LIMIT 1"
        );
        $statement->execute(['slug' => (string) ($args[0] ?? '')]);
        $class = $statement->fetch();

        if (!$class) {
            http_response_code(404);
            require BASE_PATH . '/view/errors/404.php';
            return;
        }

        $statement = $db->prepare(
            "SELECT s.id, s.name, s.slug, s.description, s.icon,
                    ser.code AS series_code,
                    COUNT(DISTINCT ch.id) AS chapter_count,
                    COUNT(DISTINCT c.id) AS content_count
             FROM subjects s
             LEFT JOIN series ser ON ser.id = s.series_id
             LEFT JOIN chapters ch ON ch.subject_id = s.id AND ch.is_active = 1
             LEFT JOIN contents c ON c.chapter_id = ch.id AND c.is_published = 1
             WHERE s.class_id = :class_id AND s.is_active = 1
             GROUP BY s.id, s.name, s.slug, s.description, s.icon, ser.code, s.position
             ORDER BY s.position"
        );
        $statement->execute(['class_id' => (int) $class['id']]);

        $this->render('pages/class', [
            'title'    => 'Cours — ' . $class['name'],
            'class'    => $class,
            'subjects' => $statement->fetchAll(),
        ]);
    }

    public function subject(...$args): void
    {
        $db = Database::pdo();
        $statement = $db->prepare(
            "SELECT s.id, s.name, s.slug, s.description, s.icon,
                    cl.id AS class_id, cl.name AS class_name, cl.slug AS class_slug, cl.exam,
                    ser.code AS series_code
             FROM subjects s
             JOIN classes cl ON cl.id = s.class_id
             LEFT JOIN series ser ON ser.id = s.series_id
             WHERE cl.slug = :class_slug AND s.slug = :subject_slug
               AND cl.is_active = 1 AND s.is_active = 1
             LIMIT 1"
        );
        $statement->execute([
            'class_slug' => (string) ($args[0] ?? ''),
            'subject_slug' => (string) ($args[1] ?? ''),
        ]);
        $subject = $statement->fetch();

        if (!$subject) {
            http_response_code(404);
            require BASE_PATH . '/view/errors/404.php';
            return;
        }

        $statement = $db->prepare(
            "SELECT ch.id, ch.title, ch.slug, ch.description, ch.position,
                    COUNT(DISTINCT c.id) AS content_count
             FROM chapters ch
             LEFT JOIN contents c ON c.chapter_id = ch.id AND c.is_published = 1
             WHERE ch.subject_id = :subject_id AND ch.is_active = 1
             GROUP BY ch.id, ch.title, ch.slug, ch.description, ch.position
             ORDER BY ch.position"
        );
        $statement->execute(['subject_id' => (int) $subject['id']]);
        $chapters = $statement->fetchAll();

        $statement = $db->prepare(
            "SELECT c.id, c.chapter_id, c.title, c.type, c.exam_year, c.access_level
             FROM contents c
             JOIN chapters ch ON ch.id = c.chapter_id
             WHERE ch.subject_id = :subject_id
               AND ch.is_active = 1
               AND c.is_published = 1
             ORDER BY ch.position, FIELD(c.type, 'course', 'exercise', 'exam', 'correction'), c.published_at DESC"
        );
        $statement->execute(['subject_id' => (int) $subject['id']]);
        $chapterContents = [];
        foreach ($statement->fetchAll() as $content) {
            $chapterContents[(int) $content['chapter_id']][] = $content;
        }

        $this->render('pages/subject', [
            'title'    => $subject['name'] . ' — ' . $subject['class_name'],
            'subject'  => $subject,
            'chapters' => $chapters,
            'chapterContents' => $chapterContents,
        ]);
    }

    public function show(...$args): void
    {
        $contentId = (int) ($args[0] ?? 0);
        if (!$contentId) {
            http_response_code(404);
            require BASE_PATH . '/view/errors/404.php';
            return;
        }

        $db = Database::pdo();

        // ---------- 1. Charger le contenu + chaîne pédagogique ----------
        $st = $db->prepare(
            "SELECT c.*,
                    ch.id    AS chapter_id,    ch.title AS chapter_title, ch.slug AS chapter_slug,
                    s.id     AS subject_id,    s.name  AS subject_name,  s.slug AS subject_slug,
                    cl.id    AS class_id,      cl.name AS class_name,    cl.slug AS class_slug, cl.exam,
                    ser.id   AS series_id,     ser.code AS series_code,  ser.label AS series_label,
                    u.first_name AS author_first, u.last_name AS author_last
             FROM contents c
             JOIN chapters ch ON ch.id = c.chapter_id
             JOIN subjects s  ON s.id  = ch.subject_id
             JOIN classes  cl ON cl.id = s.class_id
             LEFT JOIN series ser ON ser.id = s.series_id
             LEFT JOIN users u ON u.id = c.created_by
             WHERE c.id = :id AND c.is_published = 1
             LIMIT 1"
        );
        $st->execute(['id' => $contentId]);
        $content = $st->fetch();

        if (!$content) {
            http_response_code(404);
            require BASE_PATH . '/view/errors/404.php';
            return;
        }

        // ---------- 2. Contrôle d'accès ----------
        $access = $this->checkAccess($content);
        // $access['granted']  => bool
        // $access['reason']   => 'free' | 'granted' | 'need_login' | 'need_subscription' | 'need_plan'
        // $access['plan_name'] => nom de la formule requise (si reason='need_plan')

        // ---------- 3. Incrémenter les vues (uniquement si accès accordé) ----------
        if ($access['granted']) {
            $db->prepare("UPDATE contents SET views_count = views_count + 1 WHERE id = :id")
               ->execute(['id' => $contentId]);

            // Log de consultation
            if (Auth::check()) {
                $db->prepare(
                    "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, ip_address)
                     VALUES (:uid, 'content.view', 'content', :eid, :ip)"
                )->execute([
                    'uid' => Auth::id(),
                    'eid' => $contentId,
                    'ip'  => $_SERVER['REMOTE_ADDR'] ?? null,
                ]);
            }
        }

        // ---------- 4. Fichiers rattachés ----------
        $files = [];
        if ($access['granted']) {
            $st = $db->prepare(
                "SELECT id, file_kind, original_name, mime_type, size_bytes
                 FROM content_files
                 WHERE content_id = :cid
                 ORDER BY position, id"
            );
            $st->execute(['cid' => $contentId]);
            $files = $st->fetchAll();
        }

        // ---------- 5. Corrigé lié (si c'est une épreuve ou un exercice) ----------
        $correction = null;
        if (in_array($content['type'], ['exam', 'exercise'], true)) {
            $st = $db->prepare(
                "SELECT id, title, access_level, required_plan_id
                 FROM contents
                 WHERE correction_of_id = :cid AND is_published = 1
                 LIMIT 1"
            );
            $st->execute(['cid' => $contentId]);
            $correction = $st->fetch() ?: null;
        }

        // ---------- 6. Épreuve/exercice dont CE contenu est le corrigé ----------
        $parent = null;
        if ($content['type'] === 'correction' && $content['correction_of_id']) {
            $st = $db->prepare(
                "SELECT id, title, type FROM contents
                 WHERE id = :pid AND is_published = 1 LIMIT 1"
            );
            $st->execute(['pid' => (int) $content['correction_of_id']]);
            $parent = $st->fetch() ?: null;
        }

        // ---------- 7. Navigation : autres contenus du même chapitre ----------
        $siblings = $db->prepare(
            "SELECT id, title, type, access_level
             FROM contents
             WHERE chapter_id = :chid
               AND id <> :cid
               AND is_published = 1
             ORDER BY
               FIELD(type, 'course','exercise','exam','correction'),
               published_at DESC
             LIMIT 6"
        );
        $siblings->execute(['chid' => (int) $content['chapter_id'], 'cid' => $contentId]);
        $siblings = $siblings->fetchAll();

        // ---------- 8. Chapitres du même sujet (pour navigation) ----------
        $chapters = $db->prepare(
            "SELECT id, title, slug, position
             FROM chapters
             WHERE subject_id = :sid AND is_active = 1
             ORDER BY position"
        );
        $chapters->execute(['sid' => (int) $content['subject_id']]);
        $chapters = $chapters->fetchAll();

        // ---------- 9. Rendu ----------
        $this->render('pages/content', [
            'title'      => $content['title'] . ' — ' . $content['subject_name'],
            'content'    => $content,
            'access'     => $access,
            'files'      => $files,
            'correction' => $correction,
            'parent'     => $parent,
            'siblings'   => $siblings,
            'chapters'   => $chapters,
        ]);
    }

    // =================================================================
    //  Contrôle d'accès fin
    // =================================================================

    /**
     * Détermine si l'utilisateur connecté (ou visiteur) peut voir ce contenu.
     *
     * Règles :
     *   - free       : tout le monde
     *   - logged     : utilisateur connecté
     *   - subscriber : utilisateur connecté + abonnement actif sur la classe du contenu
     *   - plan       : abonnement actif avec la formule spécifique
     */
    private function checkAccess(array $content): array
    {
        $level = $content['access_level'];

        // 1. Contenu gratuit
        if ($level === 'free') {
            return ['granted' => true, 'reason' => 'free'];
        }

        // 2. Visiteur non connecté
        if (!Auth::check()) {
            return ['granted' => false, 'reason' => 'need_login'];
        }

        // 3. Utilisateur connecté → vérifier abonnement
        $db = Database::pdo();
        $userId = Auth::id();

        // Admin = accès total
        if (Auth::isAdmin()) {
            return ['granted' => true, 'reason' => 'granted'];
        }

        // Cas 'logged' : connecté suffit
        if ($level === 'logged') {
            return ['granted' => true, 'reason' => 'granted'];
        }

        // Cas 'subscriber' et 'plan' : chercher un abonnement actif
        // couvrant la classe du contenu (via chapitre → matière → classe)
        $classId = (int) $content['class_id'];

        $st = $db->prepare(
            "SELECT sub.id, sub.plan_id, p.code AS plan_code, p.name AS plan_name
             FROM subscriptions sub
             JOIN subscription_plans p ON p.id = sub.plan_id
             WHERE sub.user_id = :uid
               AND sub.class_id = :cid
               AND sub.status = 'active'
               AND sub.ends_at > NOW()
             ORDER BY sub.ends_at DESC
             LIMIT 1"
        );
        $st->execute(['uid' => $userId, 'cid' => $classId]);
        $sub = $st->fetch();

        if (!$sub) {
            return ['granted' => false, 'reason' => 'need_subscription'];
        }

        // Cas 'subscriber' : abonnement actif suffit
        if ($level === 'subscriber') {
            return ['granted' => true, 'reason' => 'granted', 'plan_name' => $sub['plan_name']];
        }

        // Cas 'plan' : abonnement actif + formule spécifique
        if ($level === 'plan') {
            if ((int) $sub['plan_id'] === (int) $content['required_plan_id']) {
                return ['granted' => true, 'reason' => 'granted', 'plan_name' => $sub['plan_name']];
            }

            // Récupérer le nom de la formule requise pour le message
            $planName = (string) $db
                ->query("SELECT name FROM subscription_plans WHERE id = " . (int) $content['required_plan_id'])
                ->fetchColumn();

            return [
                'granted'   => false,
                'reason'    => 'need_plan',
                'plan_name' => $planName ?: 'spécifique',
            ];
        }

        return ['granted' => false, 'reason' => 'need_subscription'];
    }
}