<?php

namespace App\Services\YNAB\Requests;

class CreateTransactionRequest extends AbstractRequest
{
    protected string $method = 'POST';
    protected string $path = 'budgets/%s/transactions';
}
