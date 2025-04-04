<?php

namespace App\Services\WebhookClient;

use Illuminate\Http\Request;
use Spatie\WebhookClient\Exceptions\InvalidConfig;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class TermiiSignatureValidator implements SignatureValidator
{
  public function isValid(Request $request, WebhookConfig $config): bool
  {
    $signature = $request->header($config->signatureHeaderName);

    if (!$signature) {
      return false;
    }

    $signingSecret = $config->signingSecret;

    if (empty($signingSecret)) {
      throw InvalidConfig::signingSecretNotSet();
    }

    $computedSignature = hash_hmac('sha512', $request->getContent(), $signingSecret);

    \logger($computedSignature);
    \logger($signature);
    \logger($request->getContent());

    return hash_equals($signature, $computedSignature);
  }
}
