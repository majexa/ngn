<?php

ini_set('include_path', ini_get('include_path').':'.LIB_PATH.'rss/rss_xml');

class Rss {

  public $header_tags = [
    'title'       => '{h_title}',
    'description' => '{h_description}',
    'copyright'   => '{h_copyright}',
    'link'        => '{h_link}'
  ];

  public $images_tags = [
    'title' => '{img_title}',
    'url'   => '{img_url}',
    'link'  => '{img_link}'
  ];

  public $body_tags = [
    'title'            => '{title}',
    'link'             => '{link}',
    'author'           => '{author}',
    'description'      => '{description}',
    'category'         => '{category}',
    'pubDate'          => '{pubDate}',
    'yandex_full_text' => '{yandex_full_text}',
    'enclosure'        => '{enclosure}',
    'guid'        => '{guid}'
  ];

  var $xml_type = false;

  /**
   * @param array $xml_header
   * @param array $data
   * @param string $xml_type default or news.yandex.ru special format
   */
  function __construct($xml_type = "default") {
    $this->xml_type = $xml_type;
  }

  /**
   * return path to template file
   * @return $tplname
   */
  function resolveTplFile($tplname) {
    return LIB_PATH.'/more/rss/rss_templates/'.$this->xml_type.'/'.$tplname;
  }

  function getXml($data) {
    $xml = $this->parseTemplateArray($data['header'], $this->resolveTplFile('header.xml'), $this->header_tags);
    if (isset($data['images'])) $xml .= $this->parseTemplateArray($data['images'], $this->resolveTplFile('image.xml'), $this->images_tags);
    foreach ($data['items'] as $x) {
      $xml .= $this->parseTemplateArray($x, $this->resolveTplFile('body.xml'), $this->body_tags);
    }
    $xml .= $this->fetchTemplate($this->resolveTplFile('footer.xml'));
    return $xml;
  }

  /**
   * fetch Template in Array
   * @param string $tplfile
   */
  function fetchTemplate($tplfile) {
    if (file_exists($tplfile)) {
      $read = file_get_contents($tplfile);
      return $read;
    }
    throw new Exception('Template file not found '.$tplfile);
    return false;
  }

  function parseTemplateArray($array, $tplfile, $tags) {
    if ($array) {
      $template = $this->fetchTemplate($tplfile);
      foreach ($tags as $name => $tag) {
        //if ($name == "description") {
        //$array[$name] = htmlentities($array[$name]);
        //htmlspecialchars($array[$name]);
        //htmlentities($array[$name]);//htmlspecialchars($array[$name]);
        //}
        if ($name == "link") $array[$name] = str_replace('&', '&amp;', $array[$name]);
        else {
          $array[$name] = (isset($array[$name]) and is_string($array[$name])) ? html_entity_decode($array[$name], ENT_NOQUOTES, CHARSET) : '';
        }
        $template = str_replace($tag, $array[$name], $template);
      }
      return $template;
    }
  }

  /**
   * parse xml and return array
   */
  function parseXmlFromString($data) {
    if ($tf = tempnam('/tmp', 'rss')) {
      $h = fopen($tf, "w+");
      fwrite($h, $data);
      fclose($h);
      $x = new XML_Rss($tf);
      $x->parse();
      $array['header'] = $x->getChannelInfo();
      $image = $x->getImages();
      $array['images'] = $image[0];
      $array['items'] = $x->getItems();
      return $array;
    }
    throw new Exception('Error: temporary file create '.$tf);
  }

  function parseXmlFromUrl($url) {
    if ($data = @file_get_contents($url)) {
      return $this->parseXmlFromString($data);
    }
    else {
      throw new Exception('File not found '.$url);
    }

  }

}