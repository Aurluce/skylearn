<?php
declare(strict_types=1);

class ExamController extends Controller
{
    /**
     * Liste des épreuves : toutes classes confondues + filtres.
     */
    public function index(): void
    {
        $db = Database::pdo();

        $classes = $db->query(
            "SELECT id, name, slug, exam FROM classes WHERE is_active = 1 ORDER BY position"
        )->fetchAll();

        // 20 dernières épreuves publiées
        $exams = $db->query(
            "SELECT c.id, c.title, c.description, c.exam_year, c.access_level,
                    c.published_at, c.views_count,
                    ch.title AS chapter_title,
                    s.name   AS subject_name, s.slug AS subject_slug,
                    cl.name  AS class_name,   cl.slug AS class_slug, cl.exam,
                    ser.code AS series_code,
                    (SELECT COUNT(*) FROM contents corr WHERE corr.correction_of_id = c.id AND corr.is_published = 1) AS has_correction
             FROM contents c
             JOIN chapters ch ON ch.id = c.chapter_id
             JOIN subjects s  ON s.id  = ch.subject_id
             JOIN classes  cl ON cl.id = s.class_id
             LEFT JOIN series ser ON ser.id = s.series_id
             WHERE c.type = 'exam' AND c.is_published = 1
             ORDER BY c.exam_year DESC, c.published_at DESC
             LIMIT 20"
        )->fetchAll();

        // Compteur par année (pour filtres)
        $years = $db->query(
            "SELECT DISTINCT exam_year
             FROM contents
             WHERE type='exam' AND is_published=1 AND exam_year IS NOT NULL
             ORDER BY exam_year DESC"
        )->fetchAll();

        $this->render('pages/exams', [
            'title'   => 'Épreuves d\'examen',
            'classes' => $classes,
            'exams'   => $exams,
            'years'   => $years,
        ]);
    }

    /**
     * Épreuves d'une classe donnée (par slug).
     */
    public function classe(...$args): void
    {
        $slug = (string) ($args[0] ?? '');
        $db = Database::pdo();

        $st = $db->prepare("SELECT * FROM classes WHERE slug = :s AND is_active = 1 LIMIT 1");
        $st->execute(['s' => $slug]);
        $class = $st->fetch();

        if (!$class) {
            http_response_code(404);
            require BASE_PATH . '/view/errors/404.php';
            return;
        }

        // Séries de la classe (pour filtres)
        $series = (new Series())->byClass((int) $class['id']);

        // Matières avec épreuves
        $subjects = $db->prepare(
            "SELECT DISTINCT s.id, s.name, s.slug,
                    (SELECT COUNT(*) FROM contents c
                     JOIN chapters ch ON ch.id = c.chapter_id
                     WHERE ch.subject_id = s.id
                       AND c.type='exam' AND c.is_published=1) AS exam_count
             FROM subjects s
             WHERE s.class_id = :cid AND s.is_active = 1
             ORDER BY s.position"
        );
        $subjects->execute(['cid' => (int) $class['id']]);
        $subjects = $subjects->fetchAll();

        // Épreuves de la classe
        $exams = $db->prepare(
            "SELECT c.id, c.title, c.exam_year, c.access_level, c.views_count,
                    s.name AS subject_name, s.slug AS subject_slug,
                    ser.code AS series_code,
                    (SELECT COUNT(*) FROM contents corr WHERE corr.correction_of_id = c.id AND corr.is_published = 1) AS has_correction
             FROM contents c
             JOIN chapters ch ON ch.id = c.chapter_id
             JOIN subjects s  ON s.id  = ch.subject_id
             LEFT JOIN series ser ON ser.id = s.series_id
             WHERE s.class_id = :cid
               AND c.type = 'exam'
               AND c.is_published = 1
             ORDER BY c.exam_year DESC, s.position"
        );
        $exams->execute(['cid' => (int) $class['id']]);
        $exams = $exams->fetchAll();

        $this->render('pages/exams-class', [
            'title'    => 'Épreuves — ' . $class['name'],
            'class'    => $class,
            'series'   => $series,
            'subjects' => $subjects,
            'exams'    => $exams,
        ]);
    }

    /**
     * Épreuves d'une matière dans une classe.
     */
    public function subject(...$args): void
    {
        $classSlug   = (string) ($args[0] ?? '');
        $subjectSlug = (string) ($args[1] ?? '');
        $db = Database::pdo();

        $st = $db->prepare(
            "SELECT s.*, c.name AS class_name, c.slug AS class_slug, c.exam,
                    ser.code AS series_code
             FROM subjects s
             JOIN classes c ON c.id = s.class_id
             LEFT JOIN series ser ON ser.id = s.series_id
             WHERE c.slug = :cs AND s.slug = :ss
             LIMIT 1"
        );
        $st->execute(['cs' => $classSlug, 'ss' => $subjectSlug]);
        $subject = $st->fetch();

        if (!$subject) {
            http_response_code(404);
            require BASE_PATH . '/view/errors/404.php';
            return;
        }

        $exams = $db->prepare(
            "SELECT c.id, c.title, c.description, c.exam_year, c.access_level,
                    c.views_count, c.published_at,
                    ch.title AS chapter_title,
                    (SELECT COUNT(*) FROM contents corr WHERE corr.correction_of_id = c.id AND corr.is_published = 1) AS has_correction
             FROM contents c
             JOIN chapters ch ON ch.id = c.chapter_id
             WHERE ch.subject_id = :sid
               AND c.type = 'exam'
               AND c.is_published = 1
             ORDER BY c.exam_year DESC, ch.position"
        );
        $exams->execute(['sid' => (int) $subject['id']]);
        $exams = $exams->fetchAll();

        $this->render('pages/exams-subject', [
            'title'   => 'Épreuves — ' . $subject['name'] . ' ' . $subject['class_name'],
            'subject' => $subject,
            'exams'   => $exams,
        ]);
    }
}