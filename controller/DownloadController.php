<?php
declare(strict_types=1);

class DownloadController extends Controller
{
    public function file(...$args): void
    {
        $fileId = (int) ($args[0] ?? 0);
        if (!$fileId) {
            http_response_code(404);
            require BASE_PATH . '/view/errors/404.php';
            return;
        }

        $db = Database::pdo();

        // Charger le fichier + son contenu parent + la chaîne pédagogique
        $st = $db->prepare(
            "SELECT cf.*,
                    c.id AS content_id, c.access_level, c.required_plan_id,
                    c.is_downloadable, c.is_published, c.title AS content_title,
                    ch.subject_id,
                    s.class_id, s.series_id
             FROM content_files cf
             JOIN contents c ON c.id = cf.content_id
             JOIN chapters ch ON ch.id = c.chapter_id
             JOIN subjects s  ON s.id  = ch.subject_id
             WHERE cf.id = :id
             LIMIT 1"
        );
        $st->execute(['id' => $fileId]);
        $file = $st->fetch();

        if (!$file || !$file['is_published']) {
            http_response_code(404);
            require BASE_PATH . '/view/errors/404.php';
            return;
        }

        // Téléchargement désactivé ?
        if (!$file['is_downloadable']) {
            http_response_code(403);
            echo 'Téléchargement non autorisé pour ce contenu.';
            return;
        }

        // Contrôle d'accès (même logique que ContentController)
        $content = [
            'access_level'     => $file['access_level'],
            'required_plan_id' => $file['required_plan_id'],
            'class_id'         => $file['class_id'],
        ];
        $access = $this->checkAccess($content);

        if (!$access['granted']) {
            if ($access['reason'] === 'need_login') {
                Response::redirect('/login');
            }
            Response::redirect('/subscriptions');
        }

        // Chemin physique du fichier
        $storagePath = BASE_PATH . '/storage/' . ltrim($file['storage_path'], '/');

        if (!is_file($storagePath)) {
            error_log("[Download] Fichier introuvable : $storagePath", 3, BASE_PATH . '/storage/logs/app.log');
            http_response_code(404);
            echo 'Fichier introuvable sur le serveur.';
            return;
        }

        // Journaliser le téléchargement
        $db->prepare(
            "INSERT INTO downloads (user_id, content_file_id, ip_address)
             VALUES (:uid, :fid, :ip)"
        )->execute([
            'uid' => Auth::id(),
            'fid' => $fileId,
            'ip'  => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        if (Auth::check()) {
            $db->prepare(
                "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, ip_address)
                 VALUES (:uid, 'content.download', 'content_file', :eid, :ip)"
            )->execute([
                'uid' => Auth::id(),
                'eid' => $fileId,
                'ip'  => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
        }

        // Servir le fichier
        header('Content-Type: ' . $file['mime_type']);
        header('Content-Length: ' . filesize($storagePath));
        header('Content-Disposition: attachment; filename="' . addslashes($file['original_name']) . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store');

        readfile($storagePath);
        exit;
    }

    private function checkAccess(array $content): array
    {
        $level = $content['access_level'];

        if ($level === 'free') return ['granted' => true, 'reason' => 'free'];
        if (!Auth::check())   return ['granted' => false, 'reason' => 'need_login'];
        if (Auth::isAdmin())  return ['granted' => true, 'reason' => 'granted'];
        if ($level === 'logged') return ['granted' => true, 'reason' => 'granted'];

        $db = Database::pdo();
        $st = $db->prepare(
            "SELECT sub.id, sub.plan_id
             FROM subscriptions sub
             WHERE sub.user_id = :uid
               AND sub.class_id = :cid
               AND sub.status = 'active'
               AND sub.ends_at > NOW()
             ORDER BY sub.ends_at DESC
             LIMIT 1"
        );
        $st->execute(['uid' => Auth::id(), 'cid' => (int) $content['class_id']]);
        $sub = $st->fetch();

        if (!$sub) return ['granted' => false, 'reason' => 'need_subscription'];
        if ($level === 'subscriber') return ['granted' => true, 'reason' => 'granted'];
        if ($level === 'plan' && (int) $sub['plan_id'] === (int) $content['required_plan_id']) {
            return ['granted' => true, 'reason' => 'granted'];
        }

        return ['granted' => false, 'reason' => 'need_plan'];
    }
}