<?php
declare(strict_types=1);

class QuizController extends Controller
{
    public function index(...$args): void
    {
        $db = Database::pdo();
        $profile = $db->prepare(
            "SELECT class_id, series_id
             FROM users
             WHERE id = :user_id
             LIMIT 1"
        );
        $profile->execute(['user_id' => Auth::id()]);
        $user = $profile->fetch() ?: ['class_id' => null, 'series_id' => null];

        $classes = $db->query(
            "SELECT id, name, cycle, exam
             FROM classes
             WHERE is_active = 1
             ORDER BY position"
        )->fetchAll();
        $classIds = array_map(static fn(array $class): int => (int) $class['id'], $classes);
        $requestedClassId = (int) ($_GET['class_id'] ?? 0);
        $selectedClassId = in_array($requestedClassId, $classIds, true)
            ? $requestedClassId
            : (in_array((int) ($user['class_id'] ?? 0), $classIds, true)
                ? (int) $user['class_id']
                : ($classIds[0] ?? 0));
        $selectedClass = null;
        foreach ($classes as $class) {
            if ((int) $class['id'] === $selectedClassId) {
                $selectedClass = $class;
                break;
            }
        }

        $series = [];
        if ($selectedClassId) {
            $statement = $db->prepare(
                "SELECT id, code, label
                 FROM series
                 WHERE class_id = :class_id AND is_active = 1
                 ORDER BY position"
            );
            $statement->execute(['class_id' => $selectedClassId]);
            $series = $statement->fetchAll();
        }
        $seriesIds = array_map(static fn(array $item): int => (int) $item['id'], $series);
        $requestedSeriesId = (int) ($_GET['series_id'] ?? -1);
        if ($requestedSeriesId === -1) {
            $requestedSeriesId = ((int) ($user['class_id'] ?? 0) === $selectedClassId)
                ? (int) ($user['series_id'] ?? 0)
                : 0;
        }
        $selectedSeriesId = $requestedSeriesId === 0 || in_array($requestedSeriesId, $seriesIds, true)
            ? $requestedSeriesId
            : 0;

        $subjects = [];
        if ($selectedClassId) {
            $subjectSql =
                "SELECT id, name, slug, series_id
                 FROM subjects
                 WHERE class_id = :class_id AND is_active = 1";
            $subjectParams = ['class_id' => $selectedClassId];
            if ($selectedSeriesId > 0) {
                $subjectSql .= ' AND (series_id IS NULL OR series_id = :series_id)';
                $subjectParams['series_id'] = $selectedSeriesId;
            }
            $subjectSql .= ' ORDER BY position';
            $statement = $db->prepare($subjectSql);
            $statement->execute($subjectParams);
            $subjects = $statement->fetchAll();
        }
        $subjectIds = array_map(static fn(array $subject): int => (int) $subject['id'], $subjects);
        $requestedSubjectId = (int) ($_GET['subject_id'] ?? 0);
        $selectedSubjectId = in_array($requestedSubjectId, $subjectIds, true) ? $requestedSubjectId : 0;
        $selectedSubject = null;
        foreach ($subjects as $subject) {
            if ((int) $subject['id'] === $selectedSubjectId) {
                $selectedSubject = $subject;
                break;
            }
        }

        $quizzes = [];
        if ($selectedSubjectId) {
            $statement = $db->prepare(
                "SELECT q.id, q.title, q.description, q.duration_minutes, q.access_level,
                        s.name AS subject_name, s.slug AS subject_slug,
                        cl.name AS class_name, cl.slug AS class_slug, cl.exam,
                        ch.title AS chapter_title,
                        (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.quiz_id = q.id) AS question_count,
                        COALESCE(attempts.attempt_count, 0) AS attempt_count,
                        attempts.best_percentage, attempts.last_finished_at
                 FROM quizzes q
                 JOIN subjects s ON s.id = q.subject_id
                 JOIN classes cl ON cl.id = s.class_id
                 LEFT JOIN chapters ch ON ch.id = q.chapter_id
                 LEFT JOIN (
                     SELECT quiz_id,
                            COUNT(*) AS attempt_count,
                            MAX(CASE WHEN status = 'completed' THEN score / NULLIF(total, 0) * 100 END) AS best_percentage,
                            MAX(finished_at) AS last_finished_at
                     FROM quiz_attempts
                     WHERE user_id = :user_id
                     GROUP BY quiz_id
                 ) attempts ON attempts.quiz_id = q.id
                 WHERE q.is_published = 1
                   AND s.is_active = 1
                   AND cl.is_active = 1
                   AND q.subject_id = :subject_id
                 ORDER BY ch.position, q.created_at DESC"
            );
            $statement->execute([
                'user_id' => Auth::id(),
                'subject_id' => $selectedSubjectId,
            ]);
            $quizzes = $statement->fetchAll();
        }

        $this->render('user/quizzes', [
            'title'   => 'Quiz et entraînements',
            'quizzes' => $quizzes,
            'user'    => $user,
            'classes' => $classes,
            'series' => $series,
            'subjects' => $subjects,
            'selectedClassId' => $selectedClassId,
            'selectedSeriesId' => $selectedSeriesId,
            'selectedSubjectId' => $selectedSubjectId,
            'selectedClass' => $selectedClass,
            'selectedSubject' => $selectedSubject,
        ]);
    }

    public function show(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : QuizController::show';
    }

    public function result(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : QuizController::result';
    }

    public function start(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : QuizController::start';
    }

    public function submit(...$args): void
    {
        http_response_code(501);
        echo 'À implémenter : QuizController::submit';
    }

}
