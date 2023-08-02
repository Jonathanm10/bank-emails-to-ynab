<?php

namespace App\Services\YNAB\Requests;

use JustSteveKing\Transporter\Request;
use Illuminate\Http\Client\Factory as HttpFactory;

class AbstractRequest extends Request
{
    public function __construct(HttpFactory $http)
    {
        parent::__construct($http);

        $this->withQuery(['access_token' => config('services.YNAB.api_key')]);
    }

    public function withBudgetId(?string $budgetId = null): self
    {
        return $this->setPath(sprintf($this->path(), $budgetId ?? config('services.YNAB.default_budget')));
    }
}
