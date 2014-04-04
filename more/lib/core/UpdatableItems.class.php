<?php

interface UpdatableItems {

  function getItem($id);
  //function getItems($id);
  //function getItemF($id);
  function create(array $data);
  function update($id, array $data);
  //function updateField($id, $k, $v);
  //function getItemNonFormat($id);
  function delete($id);

}
