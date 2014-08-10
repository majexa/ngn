<?php

class UestCasperAdmin extends ProjectTestCase {

  function test() {
    print `pm localProject cc test`;
    Casper::run(PROJECT_KEY, [
      "god",
    ]);
  }

}