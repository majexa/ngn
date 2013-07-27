<?php

class VkUserInfo extends VkAuthBase {

  function getName($id) {
    if (!preg_match('/id="title">(.+)<\/h1>/', $this->auth->get('http://vkontakte.ru/id'.$id), $m))
      throw new Exception("Name of user ID=$id not found");
    $m[1] = trim(preg_replace('/<b.*<\/b>/', '', $m[1]));
    return $m[1];
  }

}
