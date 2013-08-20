<?php

abstract class LongJobObject {


  abstract function longJob();

  abstract function iterate($percentage);

}