<?php

/**
 * @method void oProcessItems(AbstractItems $items)
 * @method void oProcessForm(Form $form)
 * @method void jsonFormAction(Form $form)
 * @property Req $req
 * @property array $json
 */
trait CrudAbstractCtrl {
  use ObjectProcessorCtrl;

  /**
   * @var DbItems
   */
  protected $items;

  /**
   * Возвращает ID для работы с конкретной записью, полученный из http-запроса
   *
   * @return string
   */
  abstract protected function id();

  /**
   * @param array $options
   * @return AbstractItems
   */
  abstract protected function items(array $options = []);

  /**
   * @return GridData
   */
  abstract protected function getGrid();

  function action_json_default() {
    $this->action_json_getItems();
  }

  function action_json_getItem() {
    $this->json = $this->items()->getItem($this->id());
  }

  function action_json_getItems() {
    $grid = $this->getGrid();
    $this->json = is_object($grid) ? $grid->r : $grid;
    if (isset($this->items()->hasPagination)) {
      $this->json['pagination'] = $this->items()->getPagination();
    }
  }

}

