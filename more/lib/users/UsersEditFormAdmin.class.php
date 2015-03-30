  <?php

class UsersEditFormAdmin extends UsersEditForm {

  protected function init() {
    parent::init();
    $this->filterFields[] = 'role';
    $this->options['title'] = 'Редактирование пользователя';
  }

  protected function extraFieldsOptions() {
    return ['getDisallowed' => true];
  }

}
