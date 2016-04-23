// -- Dynamic Core --

Ngn.projectKey = '<?= PROJECT_KEY ?>';
Ngn.isDebug = <?= getConstant('IS_DEBUG') ? 'true' : 'false' ?>;
Ngn.fileSizeMax = <?= Misc::phpIniFileSizeToBytes(ini_get('upload_max_filesize')) ?>;
Ngn.siteTitle = '<?= defined('SITE_TITLE') ? SITE_TITLE : 'dummy' ?>';
Ngn.sflmFrontend = '<?= Sflm::frontendName(true) ?>';
<? if (Config::getVarVar('userReg', 'vkAuthEnable')) { ?>
Ngn.vkApiId = <?= Config::getVarVar('vk', 'appId') ?>
<? } ?>