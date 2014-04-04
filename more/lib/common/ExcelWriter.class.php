<?php

class ExcelWriter {

  public $fp = null, $error, $state = 'CLOSED', $newRow = false;

  function __construct($file = "") {
    return $this->open($file);
  }

  function open($file) {
    if ($this->state != 'CLOSED') return false;
    if (!empty($file)) $this->fp = fopen($file, "a+");
    else return false;
    if ($this->fp == false) return false;
    $this->state = 'OPENED';
    fwrite($this->fp, $this->GetHeader());
    return $this->fp;
  }

  function close() {
    if ($this->state != 'OPENED') return false;
    if ($this->newRow) {
      fwrite($this->fp, "</tr>");
      $this->newRow = false;
    }
    fwrite($this->fp, $this->GetFooter());
    fclose($this->fp);
    $this->state = 'CLOSED';
    return;
  }

  function GetHeader() {
    $header = <<<EOH
			<html xmlns:o="urn:schemas-microsoft-com:office:office"
			xmlns:x="urn:schemas-microsoft-com:office:excel"
			xmlns="http://www.w3.org/TR/REC-html40">

			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=Windows-1251">
			<meta name=ProgId content=Excel.Sheet>
			<!--[if gte mso 9]><xml>
			 <o:DocumentProperties>
			  <o:LastAuthor>Sriram</o:LastAuthor>
			  <o:LastSaved>2005-01-02T07:46:23Z</o:LastSaved>
			  <o:Version>10.2625</o:Version>
			 </o:DocumentProperties>
			 <o:OfficeDocumentSettings>
			  <o:DownloadComponents/>
			 </o:OfficeDocumentSettings>
			</xml><![endif]-->
			<style>
			<!--table
				{mso-displayed-decimal-separator:"\.";
				mso-displayed-thousand-separator:"\,";}
			@page
				{margin:1.0in .75in 1.0in .75in;
				mso-header-margin:.5in;
				mso-footer-margin:.5in;}
			tr
				{mso-height-source:auto;}
			col
				{mso-width-source:auto;}
			br
				{mso-data-placement:same-cell;}
			.style0
				{mso-number-format:General;
				text-align:general;
				vertical-align:bottom;
				white-space:nowrap;
				mso-rotate:0;
				mso-background-source:auto;
				mso-pattern:auto;
				color:windowtext;
				font-size:10.0pt;
				font-weight:400;
				font-style:normal;
				text-decoration:none;
				font-family:Arial;
				mso-generic-font-family:auto;
				mso-font-charset:0;
				border:none;
				mso-protection:locked visible;
				mso-style-name:Normal;
				mso-style-id:0;}
			td
				{mso-style-parent:style0;
				padding-top:1px;
				padding-right:1px;
				padding-left:1px;
				mso-ignore:padding;
				color:windowtext;
				font-size:10.0pt;
				font-weight:400;
				font-style:normal;
				text-decoration:none;
				font-family:Arial;
				mso-generic-font-family:auto;
				mso-font-charset:0;
				mso-number-format:General;
				text-align:general;
				vertical-align:bottom;
				border:none;
				mso-background-source:auto;
				mso-pattern:auto;
				mso-protection:locked visible;
				white-space:nowrap;
				mso-rotate:0;}
			.xl24
				{mso-style-parent:style0;
				white-space:normal;}
			-->
			</style>
			<!--[if gte mso 9]><xml>
			 <x:ExcelWorkbook>
			  <x:ExcelWorksheets>
			   <x:ExcelWorksheet>
				<x:Name>srirmam</x:Name>
				<x:WorksheetOptions>
				 <x:Selected/>
				 <x:ProtectContents>False</x:ProtectContents>
				 <x:ProtectObjects>False</x:ProtectObjects>
				 <x:ProtectScenarios>False</x:ProtectScenarios>
				</x:WorksheetOptions>
			   </x:ExcelWorksheet>
			  </x:ExcelWorksheets>
			  <x:WindowHeight>10005</x:WindowHeight>
			  <x:WindowWidth>10005</x:WindowWidth>
			  <x:WindowTopX>120</x:WindowTopX>
			  <x:WindowTopY>135</x:WindowTopY>
			  <x:ProtectStructure>False</x:ProtectStructure>
			  <x:ProtectWindows>False</x:ProtectWindows>
			 </x:ExcelWorkbook>
			</xml><![endif]-->
			</head>

			<body link=blue vlink=purple>
			<!-- <style>table td {border: 1px solid #CCC}</style> -->
			<table x:str border=0 cellpadding=0 cellspacing=0 style='border-collapse: collapse;table-layout:fixed;'>
EOH;
    return $header;
  }

  function GetFooter() {
    return "</table></body></html>";
  }

  function writeLine($row) {
    foreach ($row as &$v) $v = str_replace('→', '/', $v);
    $row = Misc::iconvR(CHARSET, 'cp1251', $row);
    if ($this->state != 'OPENED') return false;
    if (!is_array($row)) return false;
    fwrite($this->fp, "<tr>\n");
    foreach ($row as $col) fwrite($this->fp, "  <td width=64>$col</td>\n");
    fwrite($this->fp, "</tr>\n");
  }

  function writeRow() {
    if ($this->state != 'OPENED') return false;
    if ($this->newRow == false) fwrite($this->fp, "<tr>");
    else
      fwrite($this->fp, "</tr><tr>");
    $this->newRow = true;
  }

  function writeCol($value) {
    if ($this->state != 'OPENED') return false;
    fwrite($this->fp, "<td class=xl24 width=64 >$value</td>");
  }

  function write($data) {
    if (isset($data['head'])) $this->writeLine($data['head']);
    foreach ($data['body'] as $v) $this->writeLine($v);
  }

}