<?php

namespace App\Data\YNAB;

use App\Data\AbstractData;
use App\ValueObjects\Transaction;

class CreateTransactionData extends AbstractData
{
    public function __construct(
        public string $account_id,
        public string $date,
        public int $amount,
        public ?string $payee_id = null,
        public string $payee_name = '',
        public ?string $category_id = null,
        public string $memo = '',
        public string $cleared = 'uncleared',
        public bool $approved = false,
        public ?string $flag_color = null,
        public ?string $import_id = null,
    )
    {
    }

    public static function fromTransaction(Transaction $transaction): self
    {
        return new self(
            account_id: $transaction->accountId,
            date: now()->format('Y-m-d'),
            amount: $transaction->signedAmountInMilliunits,
        );
    }
}
