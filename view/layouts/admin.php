<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'Administration') ?> | <?= e(setting('site_name', 'SKYLEARN')) ?> Admin</title>
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
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="flex min-h-screen flex-col md:flex-row">
        <aside class="bg-slate-900 p-4 text-slate-100 md:w-60">
            <a href="/admin" class="mb-4 flex items-center gap-2 text-lg font-bold">
                <img src="<?= asset('img/logo.jpeg') ?>" class="h-8 w-8 rounded-full object-cover" alt="">
                <?= e(setting('site_name', 'SKYLEARN')) ?> Admin
            </a>
            <nav class="flex flex-wrap gap-2 text-sm md:flex-col">
                <a href="/admin/users" class="hover:text-brand-light">Utilisateurs</a>
                <a href="/admin/classes" class="hover:text-brand-light">Classes</a>
                <a href="/admin/subjects" class="hover:text-brand-light">Matières</a>
                <a href="/admin/chapters" class="hover:text-brand-light">Chapitres</a>
                <a href="/admin/contents" class="hover:text-brand-light">Contenus</a>
                <a href="/admin/quizzes" class="hover:text-brand-light">Quiz</a>
                <a href="/admin/subscriptions" class="hover:text-brand-light">Abonnements</a>
                <a href="/admin/payments" class="hover:text-brand-light">Paiements</a>
                <a href="/admin/notifications" class="hover:text-brand-light">Notifications</a>
                <a href="/admin/announcements" class="hover:text-brand-light">Annonces</a>
                <a href="/admin/testimonials" class="hover:text-brand-light">Témoignages</a>
                <a href="/admin/statistics" class="hover:text-brand-light">Statistiques</a>
            </nav>
        </aside>
        <main class="flex-1 p-4"><?= $content ?></main>
    </div>
</body>
</html>