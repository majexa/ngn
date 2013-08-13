<?php

class PartialJobDdImages extends PartialJob {
  
  protected $images;
  protected $strName;
  protected $pageId;
  protected $w;
  protected $h;
  protected $type; // small/middle
  public $jobsInStep = 50;
  
  /**
   * @var DdItemsManagerPage
   */
  protected $im;
  
  /**
   * @param string  $strName
   * @param integer $pageId
   * @param integer $w
   * @param integer $h
   * @param string  sm/md
   */
  function __construct($strName, $pageId, $w, $h, $type) {
    $this->strName = $strName;
    $this->pageId = $pageId;
    $this->w = $w;
    $this->h = $h;
    $this->im = DdCore::getItemsManager($pageId);
    if ($type == 'sm') {
      $this->im->imageSizes['smW'] = $w;
      $this->im->imageSizes['smH'] = $h;
    } else {
      $this->im->imageSizes['mdW'] = $w;
      $this->im->imageSizes['mdH'] = $h;
    }
    $this->type = $type;
    foreach (array_keys($this->im->form->fields->getFieldsByAncestor('imagePreview')) as $name) {
      foreach (db()->selectCol(
      "SELECT $name FROM ".DdCore::table($strName)." WHERE pageId=?d", $pageId) as $image) {
        if (empty($image)) continue;
        $image = UPLOAD_PATH.'/'.$image;
        if (file_exists($image)) $this->images[] = $image;
      }
    }
    parent::__construct();
  }
  
  protected function initJobs() {
    $this->jobs = $this->images;
  }
  
  protected function makeJob($n) {
    $imagePath = $this->jobs[$n];
    if ($this->type == 'sm')
      $this->im->makeSmallThumbs($imagePath);
    else
      $this->im->makeMiddleThumbs($imagePath);
  }
  
}