<?php
declare(strict_types=1);

class Series extends Model
{
    protected string $table = 'series';
    protected string $primaryKey = 'id';

    /**
     * Toutes les séries actives d'une classe donnée.
     */
    public function byClass(int $classId): array
    {
        $st = $this->db()->prepare(
            "SELECT id, code, label, description
             FROM series
             WHERE class_id = :cid AND is_active = 1
             ORDER BY position"
        );
        $st->execute(['cid' => $classId]);
        return $st->fetchAll();
    }

    /**
     * Renvoie toutes les séries groupées par class_id.
     * Utile pour l'inscription : on charge tout d'un coup et on filtre en JS.
     */
    public function groupedByClass(): array
    {
        $rows = $this->db()->query(
            "SELECT id, class_id, code, label
             FROM series
             WHERE is_active = 1
             ORDER BY class_id, position"
        )->fetchAll();

        $out = [];
        foreach ($rows as $r) {
            $out[(int) $r['class_id']][] = $r;
        }
        return $out;
    }
}