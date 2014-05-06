<? $this->tpl($d['name'].'/head', $d) ?>
<body<?= $d['bodyClass'] ? ' class="'.$d['bodyClass'].'"' : ''?>>
<? // $this->tpl('common/sflmDebug') ?>
<table cellpadding="0" cellspacing="0" height="100%" width="100%" id="body">
<tr><td height="100%" valign="top">
<div class="admin">
  <div id="top">
    <div id="header">
      <table cellpadding="0" cellspacing="0" width="100%" class="valignSimple">
      <tr>
        <td>
          <div class="logo"><a href="<?= $this->getPath(1) ?>"><?= Config::getVarVar('cpTheme', 'logo') ?></a></div>
        </td>
        <td>
        <div class="auth">
          <div class="cont">
            <? $this->tpl('admin/loginInfo', $d) ?>
          </div>
        </div>
        </td>
        <td>
          <div class="pageTitle">
            <div class="cont">
              <h1><?= $d['moduleTitle'] ?></h1>
              <h2><?= $d['pageTitle'] ?></h2>
            </div>
          </div>
        </td>
        <td width="100%"><div class="longJobs"></div></td>
      </tr>
      </table>
    </div>
    <div class="navTop iconsSet">
      <? $this->tpl('cp/links', $d['topLinks']) ?>
      <div class="clear"><!-- --></div>
    </div>
    <? $this->tpl('cp/path', $d) ?>
  </div>
  <div class="<?= $d['mainContentCssClass'] ?>" id="mainContent">
    <? $this->tpl($d['tpl'], $d) ?>
    <div class="clear"><!-- --></div>
  </div>
</div>
</td>
</tr>
<!--
<tr>
  <td>
  <div id="bottom">
    <div class="cont">
      <a target="_blank" class="smallLogo" title="<?= Config::getVarVar('cpTheme', 'title') ?>">
        <?= Config::getVarVar('cpTheme', 'smallLogo') ?>
      </a>
      <span class="copy"><?= Config::getVarVar('cpTheme', 'copyright') ?></span>
    </div>
  </div>
  </td>
</tr>
-->
</table>
</body>
</html>
