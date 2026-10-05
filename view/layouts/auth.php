<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'SKYLEARN') ?> | <?= e(setting('site_name', 'SKYLEARN')) ?></title>
    <link rel="icon" type="image/jpeg" href="<?= asset('img/logo.jpeg') ?>">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              brand: {
                DEFAULT: '<?= e(setting('primary_color', '#1d4ed8')) ?>',
                dark:    '<?= e(setting('primary_color_dark', '#1e3a8a')) ?>',
                light:   '<?= e(setting('primary_color_light', '#dbeafe')) ?>',
              },
            },
          },
        },
      };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', system-ui, sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-brand-light/40 antialiased">
    <?= $content ?>
</body>
</html>