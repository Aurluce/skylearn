<?php
$siteName = setting('site_name', 'SKYLEARN');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation de mot de passe</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,sans-serif;color:#1e293b;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 0;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.05);">

                <!-- Header -->
                <tr><td style="background:<?= e(setting('primary_color', '#1d4ed8')) ?>;padding:32px;text-align:center;">
                    <h1 style="margin:0;color:#fff;font-size:26px;font-weight:800;letter-spacing:-.5px;">
                        <?= e($siteName) ?>
                    </h1>
                    <p style="margin:8px 0 0;color:rgba(255,255,255,.85);font-size:14px;">
                        Réinitialisation de mot de passe
                    </p>
                </td></tr>

                <!-- Body -->
                <tr><td style="padding:36px 32px;">
                    <h2 style="margin:0 0 8px;font-size:22px;color:#0f172a;">
                        Bonjour <?= e($firstName) ?> 👋
                    </h2>
                    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#475569;">
                        Tu as demandé à réinitialiser ton mot de passe sur <strong><?= e($siteName) ?></strong>.
                    </p>
                    <p style="margin:0 0 24px;font-size:15px;line-height:1.6;color:#475569;">
                        Clique sur le bouton ci-dessous pour choisir un nouveau mot de passe.
                        Ce lien est valable <strong>1 heure</strong> et ne peut être utilisé qu'une seule fois.
                    </p>

                    <div style="text-align:center;margin:28px 0;">
                        <a href="<?= e($resetUrl) ?>"
                           style="display:inline-block;background:<?= e(setting('primary_color', '#1d4ed8')) ?>;color:#fff;padding:14px 28px;border-radius:999px;text-decoration:none;font-weight:600;font-size:15px;">
                            🔐 Choisir un nouveau mot de passe
                        </a>
                    </div>

                    <div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:16px;border-radius:8px;margin:24px 0;">
                        <p style="margin:0;font-size:14px;color:#78350f;">
                            <strong>Si tu n'es pas à l'origine de cette demande</strong>, ignore cet e-mail.
                            Ton mot de passe actuel reste inchangé.
                        </p>
                    </div>

                    <p style="margin:24px 0 0;font-size:12px;color:#94a3b8;word-break:break-all;">
                        Si le bouton ne fonctionne pas, copie ce lien dans ton navigateur :<br>
                        <a href="<?= e($resetUrl) ?>" style="color:<?= e(setting('primary_color', '#1d4ed8')) ?>;"><?= e($resetUrl) ?></a>
                    </p>
                </td></tr>

                <!-- Footer -->
                <tr><td style="background:#f8fafc;padding:20px 32px;text-align:center;border-top:1px solid #e2e8f0;">
                    <p style="margin:0;font-size:12px;color:#94a3b8;">
                        &copy; <?= date('Y') ?> <?= e($siteName) ?> — Tous droits réservés.
                    </p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>