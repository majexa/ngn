<?php

function replaceOut($str) {
  if (is_array($str)) $str = getPrr($str);
  $numNewLines = substr_count($str, "\n");
  echo chr(27)."[0G"; // Set cursor to first column
  echo $str;
  echo chr(27)."[".$numNewLines."A"; // Set cursor up x lines
}

//$states = new LongJobStates;
//$states->add('asd');
//foreach ($states as $v) {
  //print "\n* ".$v['id'];
  //$states->remove($v['id']);
  //break;
//}
//print "\n\n";
//while (true) {
  //$jobs = LongJobRunner::storedJobs();
  //$s = '';
  //foreach ($jobs as $v) $s .= $v['id'].': ['.Tt()->enum(Arr::filterEmpties((new LongJobRunner($v['id']))->all()), ', ', '$k.`: `.$v')."]\n";
  //for ($i = count($jobs); $i < 10; $i++) $s .= "                                                          \n";
  //replaceOut($s);
  //replaceOut(getPrr((new LongJobState($v['id']))->all()));
  //sleep(1);
//}
