// -- Dynamic Core --

Ngn.projectKey = '<?= PROJECT_KEY ?>';
Ngn.isDebug = true;<?= IS_DEBUG ? 'true' : 'false' ?>;
Ngn.fileSizeMax = <?= Misc::phpIniFileSizeToBytes(ini_get('upload_max_filesize')) ?>;
Ngn.sessionId = '<?= session_id() ?>';
Ngn.siteTitle = '<?= SITE_TITLE ?>';
Ngn.siteDomain = '<?= SITE_DOMAIN ?>';
Ngn.auth = <?= json_encode(Auth::getAll()) ?>;
