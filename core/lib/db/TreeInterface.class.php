<?php

interface TreeInterface {

  function childrenKey();

  function getTree();

  //function getRoot();

  //function getChildren($id);

  function getParentId($id);

}
