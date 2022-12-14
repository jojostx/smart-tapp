<?php

use App\Models\Tenant;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Jojostx\Larasubs\Models\Plan;

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

if (!function_exists('in_range')) {
  /**
   * Determines if $number is between $min and $max
   *
   * @param  integer  $number     The number to test
   * @param  integer  $min        The minimum value in the range
   * @param  integer  $max        The maximum value in the range
   * @param  boolean  $inclusive  Whether the range should be inclusive or not
   * @return boolean              Whether the number was in the range
   */
  function in_range($number, $min, $max, $inclusive = FALSE)
  {
    if (is_int($number) && is_int($min) && is_int($max)) {
      return $inclusive
        ? ($number >= $min && $number <= $max)
        : ($number > $min && $number < $max);
    }

    return FALSE;
  }
}

if (!function_exists('elapsed')) {
  /**
   * Determines if $time is in the past
   *
   * @param Carbon $time
   * @param Carbon|null $referenceTime
   * 
   * @return boolean
   */
  function elapsed(Carbon $time, ?Carbon $referenceTime = null)
  {
    $referenceTime = $referenceTime ?? now();

    return $referenceTime->greaterThanOrEqualTo($time);
  }
}

if (!function_exists('flattenWithKeys')) {
  /**
   * Flattens an array recursively while preserving keys by "smushing" them
   * 
   * Also works by typecasting objects or xml, example: (array) $obj
   *
   * @param array   $array
   * @param string  $nestingDelimiter string to be used as a delimiter for nested keys
   * @param string  $root string to be prefixed to the keys
   * @param array   $result
   * 
   * @return array
   */
  function flattenWithKeys(array $array, $nestingDelimiter = '.', $root = '', $result = []): array
  {
    foreach ($array as $k => $v) {
      if ((is_array($v) || is_object($v)) && !empty($v)) $result = flattenWithKeys((array) $v, $nestingDelimiter, $root . $k . $nestingDelimiter, $result);
      else $result[$root . $k] = $v;
    }

    return $result;
  }
}

if (!function_exists('getCentralConnection')) {
  /**
   * gets the name of the central database connection 
   * @return string
   */
  function getCentralConnection(): ?string
  {
    return config('tenancy.database.central_connection');
  }
}

if (!function_exists('getTenant')) {
  /**
   * gets the name of the central database connection 
   * @return null|Tenant
   */
  function getTenant(string|Tenant $tenant): ?Tenant
  {
    return ($tenant instanceof Tenant) ? $tenant : \tenancy()->find($tenant);
  }
}

if (!function_exists('getPlanPrice')) {
  /**
   * gets the price for a plan 
   * @return int
   */
  function getPlanPrice(Plan $plan): int
  {
    return (int) \money($plan->price, $plan->currency)->getValue();
  }
}