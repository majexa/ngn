<?php

abstract class CtrlAdminConfigFieldSet extends CtrlAdmin {

  function action_default() {
    $form = new ConfigForm($this->configKey(), $this->configFields());
    if ($form->update()) {
      $this->redirect();
      return;
    }
    $this->d['form'] = $form->html();
    $this->d['tpl'] = 'common/form';
  }

  abstract protected function configKey();

  abstract protected function configFields();

}