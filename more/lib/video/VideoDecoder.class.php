<?php

class VideoDecoder {

  function decode($inputFile, $outputFile, $w = 320, $h = 240) {
//    list($initW, $initH) = $this->getSize($inputFile);
//    if ($initW > $w or $initH > $h) {
//      if ($initW / $initH > $w / $h) {
//        $h = round($w / ($initW / $initH));
//      }
//      else {
//        $w = round($h / ($initH / $initW));
//      }
//    }
//    else {
//      $w = $initW;
//      $h = $initH;
//    }
    $cmd = "ffmpeg -i $inputFile "."-acodec libfaac -ab 128k -ar 44100 ". // audio codec
      "-vcodec libx264 -vpre slow -vpre baseline -crf 22 -threads 0 ".  // video codec
      //"-s {$w}x{$h} ".
      //"-r 29 ".
      "$outputFile";
    File::delete($outputFile);
    sys($cmd);
    sys("MP4Box -add $outputFile $outputFile");
    if (!file_exists($outputFile)) {
      throw new Exception('Decode video problems. Video "'.$inputFile.'". Command: '.$cmd, 1038);
    }
  }

}
