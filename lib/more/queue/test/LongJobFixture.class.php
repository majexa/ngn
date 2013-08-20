<?php

class LongJobObjectFixture extends LongJobObject {

  function longJob() {

  }

}

class LongJobStateFixture extends LongJobState {

  function object() {
    return new LongJobObjectFixture;
  }

}