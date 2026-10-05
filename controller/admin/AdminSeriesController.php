<?php
declare(strict_types=1);

class AdminSeriesController extends Controller
{
    public function index(): void
    {
        $db = Database::pdo();

        $classes = $db->query(
            "SELECT id, name, slug FROM classes WHERE is_active = 1 ORDER BY position"
        )->fetchAll();

        $series = $db->query(
            "SELECT s.*, c.name AS class_name
             FROM series s
             JOIN classes c ON c.id = s.class_id
             ORDER BY c.position, s.position"
        )->fetchAll();

        $this->render('admin/series', [
            'title'   => 'Séries',
            'classes' => $classes,
            'series'  => $series,
        ], 'admin');
    }

    public function store(): void
    {
        $classId  = (int) ($_POST['class_id'] ?? 0);
        $code     = strtoupper(trim($_POST['code'] ?? ''));
        $label    = trim($_POST['label'] ?? '');
        $desc     = trim($_POST['description'] ?? '');
        $position = (int) ($_POST['position'] ?? 0);

        if (!$classId || $code === '' || $label === '') {
            Session::flash('error', 'Classe, code et libellé sont obligatoires.');
            Response::redirect('/admin/series');
        }

        try {
            Database::pdo()->prepare(
                "INSERT INTO series (class_id, code, label, description, position)
                 VALUES (:cid, :code, :label, :desc, :pos)"
            )->execute([
                'cid'   => $classId,
                'code'  => $code,
                'label' => $label,
                'desc'  => $desc ?: null,
                'pos'   => $position,
            ]);
            Session::flash('success', "Série « $code » ajoutée.");
        } catch (PDOException $e) {
            Session::flash('error', "Cette série existe déjà pour cette classe.");
        }

        Response::redirect('/admin/series');
    }

    public function update($id): void
    {
        $id = (int) $id;
        Database::pdo()->prepare(
            "UPDATE series
             SET code = :code, label = :label, description = :desc,
                 position = :pos, is_active = :active
             WHERE id = :id"
        )->execute([
            'code'   => strtoupper(trim($_POST['code'] ?? '')),
            'label'  => trim($_POST['label'] ?? ''),
            'desc'   => trim($_POST['description'] ?? '') ?: null,
            'pos'    => (int) ($_POST['position'] ?? 0),
            'active' => isset($_POST['is_active']) ? 1 : 0,
            'id'     => $id,
        ]);

        Session::flash('success', 'Série mise à jour.');
        Response::redirect('/admin/series');
    }

    public function destroy($id): void
    {
        Database::pdo()->prepare("DELETE FROM series WHERE id = :id")
            ->execute(['id' => (int) $id]);

        Session::flash('success', 'Série supprimée.');
        Response::redirect('/admin/series');
    }
}