<?php

class FieldEImagesPreview extends FieldEImage
{

    function defineOptions()
    {
        return array_merge(parent::defineOptions(), [
          'multiple' => true
        ]);
    }

    protected function htmlNav()
    {
        $s = '';
        if (($imagePaths = $this->dataValue())) {
            foreach ($imagePaths as $imagePath) {
                $imagePath = '/'.UPLOAD_DIR.'/'.$imagePath;
                $s .= '<div class="imageCont">';
                //if ($this->form->options['deleteFileUrl']) {
                //  $s .= '<a href="'.$this->form->options['deleteFileUrl'].'&fieldName='.$this->options['name'].'" class="iconBtn noborder delete confirm" title="Удалить"><i></i></a>';
                //}
                $s .= '<a href="'.$imagePath.'" class="thumb lightbox" title="Текущее изображение"><img src="'.Misc::getFilePrefexedPath($imagePath, 'sm_').'" /></a>';
                $s .= '</div>';
            }
        }
        return <<<HTML
<style>
.fileNavImagesPreview .imageCont {
position: relative;
display: inline-block;
margin: 0 8px 3px 0;
}
.fileNavImagesPreview .imageCont .iconBtn {
position: absolute;
top: 5px;
right: 0;
background: #fff;
}
.fileNavImagesPreview {
text-align: right;
width: 200px;
margin-right: -10px;
}
</style>
<div class="fileNav fileNavImagesPreview">
  <div class="">
    $s
  </div>
</div>
HTML;
    }

}
