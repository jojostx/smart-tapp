<?php

use Illuminate\Support\HtmlString;

if (!function_exists('replaceQrCodeAttributes')) {

  /**
   *  replaces qrcode width and height attributes with class(es)
   * 
   *  @param string $qrcode a string representation of a QR Code svg, ex: <svg class="text-sm"><p style="line-height: 1.3rem;">sample</p></svg>
   *  @param string $classes a string representation of CSS classes to append
   *  @param string $id id attribute to be set on the qrcode svg
   * 
   *  @return HtmlString
   */
  function replaceQrCodeAttributes(string $qrcode = '', string $classes = 'w-full', string $id = ""): HtmlString
  {
    if (blank($qrcode)) {
      return str('<span> - </span>')->toHtmlString();
    }

    $xmlDom = new DOMDocument();

    if (!$xmlDom->loadXML($qrcode)) {
      return str($qrcode)->toHtmlString();
    }

    $nodes = $xmlDom->getElementsByTagName('svg');

    if ($nodes->length !== 0) {
      /** @var DOMElement $svg */
      $svg = $nodes[0];

      $svg->removeAttribute('width');

      $svg->removeAttribute('height');

      $svg->setAttribute('class', $classes);
      
      $svg->setAttribute('id', $id);
      
      $svg->setIdAttribute('id', true);

      $svg = $svg->ownerDocument->saveXML($svg);

      return str($svg)->toHtmlString();
    }

    return str($qrcode)->toHtmlString();
  }
}
