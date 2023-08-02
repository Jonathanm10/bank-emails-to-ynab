<?php

namespace App\Services\YNAB\Requests;

class GetAccountsRequest extends AbstractRequest
{
    protected string $method = 'GET';
    protected string $path = 'budgets/%s/accounts';
}
