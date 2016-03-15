<?php

abstract class CtrlAdminConfigFieldSet extends CtrlAdmin {

  function action_default() {
    $form = new ConfigSetForm($this->configKey(), $this->configFields());
    if (($firstLevelKey = $this->firstLevelKey())) $form->firstLevelKey = $firstLevelKey;
    if ($form->update()) {
      $this->redirect();
      return;
    }
    $this->d['form'] = $form->html();
    $this->d['tpl'] = 'common/form';
  }

  protected function firstLevelKey() {
    return false;
  }

  abstract protected function configKey();

  abstract protected function configFields();

}