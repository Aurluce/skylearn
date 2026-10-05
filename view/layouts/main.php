<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta name="description" content="<?= e($metaDescription ?? setting('site_description', '')) ?>">
    <meta name="theme-color" content="<?= e(setting('primary_color', '#1d4ed8')) ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= e($title ?? setting('site_name', 'SKYLEARN')) ?>">
    <meta property="og:description" content="<?= e($metaDescription ?? setting('site_description', '')) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= e(setting('site_name', 'SKYLEARN')) ?>">
    <meta property="og:image" content="<?= asset('img/logo.jpeg') ?>">

    <title><?= e($title ?? setting('site_name', 'SKYLEARN')) ?> | <?= e(setting('site_name', 'SKYLEARN')) ?></title>

    <link rel="icon" type="image/jpeg" href="<?= asset('img/logo.jpeg') ?>">

    <!-- ================= TAILWIND CDN ================= -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Configuration Tailwind : couleurs dynamiques depuis la BD + animations -->
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
            keyframes: {
              'fade-up': {
                '0%':   { opacity: '0', transform: 'translateY(16px)' },
                '100%': { opacity: '1', transform: 'translateY(0)' },
              },
              'float': {
                '0%, 100%': { transform: 'translate(0, 0)' },
                '50%':      { transform: 'translate(20px, -20px)' },
              },
              'float-slow': {
                '0%, 100%': { transform: 'translate(0, 0)' },
                '50%':      { transform: 'translate(-15px, 15px)' },
              },
            },
            animation: {
              'fade-up':       'fade-up 0.7s ease-out both',
              'fade-up-delay': 'fade-up 0.9s ease-out 0.2s both',
              'float':         'float 8s ease-in-out infinite',
              'float-slow':    'float-slow 12s ease-in-out infinite',
            },
          },
        },
      };
    </script>

    <!-- ================= GOOGLE FONTS ================= -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- ================= ICONS (Font Awesome) ================= -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- ================= STYLES PERSO ================= -->
    <style>
      body { font-family: 'Inter', system-ui, sans-serif; }
      html { scroll-behavior: smooth; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <?php require BASE_PATH . '/view/partials/navbar.php'; ?>
    <main><?= $content ?></main>
    <?php require BASE_PATH . '/view/partials/footer.php'; ?>
    <?php require BASE_PATH . '/view/partials/whatsapp-button.php'; ?>

    <!-- ================= JS PERSO ================= -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Menu mobile
      const toggle = document.getElementById('nav-toggle');
      const menu   = document.getElementById('nav-mobile');
      if (toggle && menu) {
        const icon = document.getElementById('nav-toggle-icon');
        toggle.addEventListener('click', () => {
          const isOpen = menu.classList.toggle('hidden') === false;
          toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
          toggle.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
          if (icon) icon.className = `fa-solid ${isOpen ? 'fa-xmark' : 'fa-bars'} text-lg`;
        });
      }

      // Animations au scroll
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.1 });

      document.querySelectorAll('[data-animate]').forEach((el) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(16px)';
        el.style.transition = 'opacity .7s ease-out, transform .7s ease-out';
        observer.observe(el);
      });

      // Compteurs animés
      document.querySelectorAll('[data-count]').forEach((el) => {
        const target = parseInt(el.dataset.count, 10);
        if (isNaN(target)) return;
        let current = 0;
        const step = Math.max(1, Math.floor(target / 40));
        const timer = setInterval(() => {
          current += step;
          if (current >= target) { current = target; clearInterval(timer); }
          el.textContent = current.toLocaleString('fr-FR') + '+';
        }, 30);
      });
    });
    </script>
</body>
</html>