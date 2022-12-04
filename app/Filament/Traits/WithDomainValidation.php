<?php

namespace App\Filament\Traits;

trait WithDomainValidation
{
  /** @var string */
  public $domain = '';

  protected function getRules(): array
  {
    /**
     * append domain rule to rules array
     */
    $domainRule = $this->getDomainRule();
    $rules = [];

    if (property_exists($this, 'rules')) {
      $rules = $this->rules;
    }

    if (method_exists($this, 'rules')) {
      $rules = $this->rules();
    }

    return array_merge($domainRule, $rules);
  }

  protected function getDomainRule(): array
  {
    return [
      'domain' => ['required', 'exists:' . config('database.connections.mysql.driver') . '.domains,domain']
    ];
  }

  protected function prepareForValidation($attributes): array
  {
    if ($this->domain === $this->currentTenant?->domain) {
      return $attributes;
    }

    if (filled($this->domain) && is_string($this->domain)) {
      $attributes['domain'] = strtolower($this->domain) . '.' . config('tenancy.central_domains.main');
    }

    return $attributes;
  }

  public function getCurrentTenantProperty()
  {
    return tenant();
  }
}
