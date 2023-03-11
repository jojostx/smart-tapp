<?php

namespace App\DTOs;

class InboxSearchResult
{
    public function __construct(
        public string $title,
        public string $identifier,
        public array $details = [],
    ) {
    }
}
