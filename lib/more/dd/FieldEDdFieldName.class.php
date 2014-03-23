<?php

class FieldEDdFieldName extends FieldEName {

  protected function validate3() {
    if (in_array($this->options['value'], Db::getReservedNames())) {
      $this->error = '"'.$this->options['value'].'" является зарезервированым словом';
      return;
    }
    if (Misc::hasSuffix('From', $this->options['value']) or Misc::hasSuffix('To', $this->options['value'])) {
      $this->error = 'Нельзя использовать слова "From" или "To" в конце имени';
      return;
    }
    if ($this->valueChanged and (new DdFields($this->form->strName))->exists($this->options['value'])
    ) {
      $this->error = "Поле с именем «{$this->options['value']}» (структура «{$this->form->strName}») уже существует";
      return;
    }
  }

}