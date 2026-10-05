<?php
declare(strict_types=1);

class Setting extends Model
{
    protected string $table = 'settings';
    protected string $primaryKey = 'setting_key';

    /**
     * Retourne tous les paramètres sous forme de tableau associatif.
     */
    public function map(): array
    {
        $rows = $this->db()
            ->query("SELECT setting_key, setting_value FROM settings")
            ->fetchAll();

        $out = [];
        foreach ($rows as $row) {
            $out[$row['setting_key']] = $row['setting_value'];
        }
        return $out;
    }

    /**
     * Met à jour un paramètre (insert ou update).
     */
    public function put(string $key, string $value): void
    {
        $st = $this->db()->prepare(
            "INSERT INTO settings (setting_key, setting_value)
             VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
        );
        $st->execute(['k' => $key, 'v' => $value]);
    }
}