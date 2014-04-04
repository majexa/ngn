// --------------------------Core------------------------------
Ngn.fileSizeMax = <?= Misc::phpIniFileSizeToBytes(ini_get('upload_max_filesize')) ?>;
Ngn.sessionId = '<?= session_id() ?>';
Ngn.vkApiId = <?= Arr::jsValue(Config::getVarVar('vk', 'appId', true)) ?>;
Ngn.projectKey = '<?= PROJECT_KEY ?>';
Ngn.siteTitle = '<?= SITE_TITLE ?>';
Ngn.siteDomain = '<?= SITE_DOMAIN ?>';
Ngn.auth = <?= json_encode(Auth::getAll()) ?>;
Ngn.fromVk = <?= Arr::jsValue((bool)isset($_SESSION['fromVk'])) ?>;
