<?php

namespace App\Services\WebhookClient;

use Illuminate\Http\Request;
use Spatie\WebhookClient\Exceptions\InvalidConfig;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class AfricasTalkingSignatureValidator implements SignatureValidator
{
  public function isValid(Request $request, WebhookConfig $config): bool
  {
    return true;
  }
}
