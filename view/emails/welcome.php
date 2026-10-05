<?php
$siteName = setting('site_name', 'SKYLEARN');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bienvenue sur <?= e($siteName) ?></title>
</head>
<body style="margin:0;padding:24px;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#1e293b;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;">
        <tr>
            <td style="padding:28px 32px;background:#1d4ed8;color:#ffffff;">
                <h1 style="margin:0;font-size:24px;">Bienvenue sur <?= e($siteName) ?> !</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:32px;line-height:1.6;">
                <p style="margin:0 0 16px;">Bonjour <?= e($firstName) ?>,</p>
                <p style="margin:0 0 20px;">Ton compte a bien été créé. Nous sommes ravis de t’accueillir et te souhaitons beaucoup de réussite dans tes études.</p>

                <h2 style="margin:0 0 12px;font-size:18px;">Récapitulatif de ton inscription</h2>
                <p style="margin:0 0 8px;"><strong>Classe :</strong> <?= e($className) ?></p>
                <?php if ($seriesLabel !== ''): ?>
                    <p style="margin:0 0 8px;"><strong>Série :</strong> <?= e($seriesLabel) ?></p>
                <?php endif; ?>
                <p style="margin:0 0 20px;"><strong>Téléphone :</strong> <?= e($phone) ?></p>

                <p style="margin:0;">Connecte-toi à ton espace SKYLEARN pour découvrir les cours, exercices et épreuves disponibles pour ta classe.</p>
            </td>
        </tr>
        <tr>
            <td style="padding:18px 32px;background:#f8fafc;color:#64748b;font-size:12px;">
                Cet e-mail confirme la création de ton compte sur <?= e($siteName) ?>.
            </td>
        </tr>
    </table>
</body>
</html>
