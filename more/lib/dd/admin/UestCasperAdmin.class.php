<?php

class UestCasperAdmin extends ProjectTestCase {

  function test() {
    print `pm localProject cc test`;
    TestRunnerCasper::runTest(PROJECT_KEY, [
      "god",
    ]);
  }

}