<?php

namespace App\Services\YNAB;

use App\Data\YNAB\AccountData;
use App\Data\YNAB\CreateTransactionData;
use App\Services\YNAB\Requests\CreateTransactionRequest;
use App\Services\YNAB\Requests\GetAccountsRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;

class YNABService
{
    /**
     * @return Collection<AccountData>
     *@throws RequestException
     */
    public function accounts(): Collection
    {
        return GetAccountsRequest::build()
            ->withBudgetId()
            ->send()
            ->throw()
            ->collect('data.accounts')
            ->map(static fn(array $data) => AccountData::fromArray($data));
    }

    /**
     * @param Collection<CreateTransactionData> $data
     * @throws RequestException
     */
    public function createTransactions(Collection $data): void
    {
        CreateTransactionRequest::build()
            ->withBudgetId()
            ->withData([
                'transactions' => $data->map(static fn(CreateTransactionData $transaction) => $transaction->toArray()),
            ])
            ->send()
            ->throw();
    }
}
