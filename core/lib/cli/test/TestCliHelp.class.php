<?php


class TestCliHelp extends NgnTestCase {

  function test() {
    (new CliHelpArgsFixture([
      '...',
      'localProjects',
      'aaa'
    ]));


    //print `history 10`;
    //``;
  }

}