# SKYLEARN

Plateforme éducative en ligne (PHP natif 8.x, MySQL, Tailwind CSS, Fetch API).

## Démarrage (Ubuntu)

1. Prérequis : `sudo apt install php php-cli php-mysql php-mbstring php-xml php-curl mysql-server nodejs npm`
2. Base de données : créer l'utilisateur MySQL, puis `mysql -u root -p < database/schema.sql`
3. Configurer `.env` (base de données, WhatsApp, clés MeSomb)
4. Styles : `npm install && npm run build` (ou `npm run dev` pendant le développement)
5. Serveur local : `bash bin/serve.sh` puis ouvrir http://localhost:8000

## Structure

- `public/` : seul dossier exposé au web (point d'entrée `index.php`)
- `core/` : routeur, base de données, session, CSRF, authentification
- `controller/`, `model/`, `view/` : MVC
- `routes/` : `web.php` (pages) et `api.php` (JSON / Fetch)
- `storage/` : fichiers téléversés, journaux, sauvegardes (hors accès web)
- `database/` : schéma SQL et jeux de données
- `bin/` : scripts (serveur local, sauvegarde, expiration des abonnements)

## Production

Pointer la racine du site vers `public/`. Sur un hébergement mutualisé où ce n'est
pas possible, le `.htaccess` racine redirige vers `public/`.
Planifier (cron) : `bin/backup.sh` (quotidien) et `php bin/cron_expire.php` (quotidien).
