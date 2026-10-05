<?php
declare(strict_types=1);

class HomeController extends Controller
{
    public function index(): void
    {
        $db = Database::pdo();

        // --- Matières mises en avant (6 premières actives) ---
        $subjects = $db->query(
            "SELECT s.id, s.name, s.slug, s.icon, c.name AS class_name, c.slug AS class_slug
             FROM subjects s
             JOIN classes c ON c.id = s.class_id
             WHERE s.is_active = 1 AND c.is_active = 1
             ORDER BY c.position ASC, s.position ASC
             LIMIT 8"
        )->fetchAll();

        // --- Classes actives ---
        $classes = $db->query(
            "SELECT id, name, slug, cycle, exam
             FROM classes
             WHERE is_active = 1
             ORDER BY position ASC"
        )->fetchAll();

        // --- Formules d'abonnement + tarif de départ ---
        $plans = $db->query(
            "SELECT p.id, p.code, p.name, p.duration_days,
                    MIN(cpp.price) AS price_from
             FROM subscription_plans p
             LEFT JOIN class_plan_prices cpp ON cpp.plan_id = p.id AND cpp.is_active = 1
             WHERE p.is_active = 1
             GROUP BY p.id
             ORDER BY p.position ASC"
        )->fetchAll();

        // --- Témoignages publiés ---
        $testimonials = $db->query(
            "SELECT author_name, author_role, content, rating, photo_path
             FROM testimonials
             WHERE is_published = 1
             ORDER BY position ASC
             LIMIT 6"
        )->fetchAll();

        // --- Nombre de contenus publiés (compteur dynamique) ---
        $contentCount = (int) $db->query(
            "SELECT COUNT(*) FROM contents WHERE is_published = 1"
        )->fetchColumn();

        // --- Dernières annonces actives ---
        $announcements = $db->query(
            "SELECT title, body, created_at
             FROM announcements
             WHERE is_active = 1
               AND (starts_at IS NULL OR starts_at <= NOW())
               AND (ends_at   IS NULL OR ends_at   >= NOW())
             ORDER BY created_at DESC
             LIMIT 3"
        )->fetchAll();

        $this->render('pages/home', [
            'title'         => setting('site_name', 'SKYLEARN') . ' — ' . setting('site_slogan', ''),
            'metaDescription' => setting('site_description', ''),
            'subjects'      => $subjects,
            'classes'       => $classes,
            'plans'         => $plans,
            'testimonials'  => $testimonials,
            'announcements' => $announcements,
            'contentCount'  => $contentCount,
        ]);
    }

    public function about(): void
    {
        $this->render('pages/about', ['title' => 'À propos']);
    }

    public function subjects(): void
    {
        $db = Database::pdo();
        $subjects = $db->query(
            "SELECT s.id, s.class_id, s.name, s.slug, s.description, s.icon, s.position,
                    c.name AS class_name, c.slug AS class_slug, c.cycle, c.exam,
                    (SELECT COUNT(*) FROM chapters ch
                     WHERE ch.subject_id = s.id AND ch.is_active = 1) AS chapter_count,
                    (SELECT COUNT(*) FROM contents content
                     JOIN chapters ch ON ch.id = content.chapter_id
                     WHERE ch.subject_id = s.id AND ch.is_active = 1 AND content.is_published = 1) AS content_count
             FROM subjects s
             JOIN classes c ON c.id = s.class_id
             WHERE s.is_active = 1 AND c.is_active = 1
             ORDER BY c.position, s.position, s.name"
        )->fetchAll();

        $selectedCycle = $_GET['cycle'] ?? 'all';
        if (!in_array($selectedCycle, ['all', 'college', 'lycee'], true)) {
            $selectedCycle = 'all';
        }

        $classes = $db->query(
            "SELECT id, name, slug, cycle, exam
             FROM classes
             WHERE is_active = 1
             ORDER BY position"
        )->fetchAll();

        $this->render('pages/subjects', [
            'title'           => 'Matières',
            'metaDescription' => 'Explore les matières et ressources pédagogiques de SKYLEARN, organisées par classe.',
            'subjects'        => $subjects,
            'classes'         => $classes,
            'selectedCycle'   => $selectedCycle,
        ]);
    }

    public function testimonials(): void
    {
        $db = Database::pdo();
        $testimonials = $db->query(
            "SELECT * FROM testimonials WHERE is_published = 1 ORDER BY position"
        )->fetchAll();

        $this->render('pages/testimonials', [
            'title'        => 'Témoignages',
            'testimonials' => $testimonials,
        ]);
    }
}