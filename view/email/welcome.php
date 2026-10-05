<?php
$siteName = setting('site_name', 'SKYLEARN');
$waLink   = whatsapp_link('Bonjour, je viens de m\'inscrire sur ' . $siteName);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bienvenue sur <?= e($siteName) ?></title>
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
                        <?= e(setting('site_slogan', '')) ?>
                    </p>
                </td></tr>

                <!-- Body -->
                <tr><td style="padding:36px 32px;">
                    <h2 style="margin:0 0 8px;font-size:22px;color:#0f172a;">
                        Bonjour <?= e($firstName) ?> 👋
                    </h2>
                    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#475569;">
                        Bienvenue sur <strong><?= e($siteName) ?></strong> ! Ton compte a bien été créé.
                    </p>
                    <p style="margin:0 0 24px;font-size:15px;line-height:1.6;color:#475569;">
                        Pour accéder à toutes les ressources (cours, exercices, épreuves et corrigés de ta classe),
                        il te suffit de choisir une formule d'abonnement.
                    </p>

                    <div style="text-align:center;margin:28px 0;">
                        <a href="<?= e(setting('site_name')) ? '#' : '#' ?>"
                           style="display:inline-block;background:<?= e(setting('primary_color', '#1d4ed8')) ?>;color:#fff;padding:14px 28px;border-radius:999px;text-decoration:none;font-weight:600;font-size:15px;">
                            Accéder à mon espace
                        </a>
                    </div>

                    <div style="background:#f8fafc;border-left:4px solid <?= e(setting('primary_color', '#1d4ed8')) ?>;padding:16px;border-radius:8px;margin:24px 0;">
                        <p style="margin:0;font-size:14px;color:#475569;">
                            <strong>Récapitulatif :</strong><br>
                            Classe : <?= e($className) ?><br>
                            Téléphone : <?= e($phone) ?>
                        </p>
                    </div>

                    <p style="margin:24px 0 0;font-size:14px;color:#64748b;">
                        Une question ? Réponds à cet e-mail ou contacte-nous sur
                        <a href="<?= e($waLink) ?>" style="color:<?= e(setting('primary_color', '#1d4ed8')) ?>;">WhatsApp</a>.
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