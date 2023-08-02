<?php

namespace App\Data\YNAB;

use App\Data\AbstractData;

class AccountData extends AbstractData
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $type,
        public readonly bool $onBudget,
        public readonly bool $closed,
        public readonly ?string $note,
        public readonly int $balance,
        public readonly int $clearedBalance,
        public readonly int $unclearedBalance,
        public readonly string $transferPayeeId,
        public readonly bool $directImportLinked,
        public readonly bool $directImportInError,
        public readonly ?string $lastReconciledAt,
        public readonly ?int $debtOriginalBalance,
        public readonly array $debtInterestRates,
        public readonly array $debtMinimumPayments,
        public readonly array $debtEscrowAmounts,
        public readonly bool $deleted,
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            type: $data['type'],
            onBudget: $data['on_budget'],
            closed: $data['closed'],
            note: $data['note'],
            balance: $data['balance'],
            clearedBalance: $data['cleared_balance'],
            unclearedBalance: $data['uncleared_balance'],
            transferPayeeId: $data['transfer_payee_id'],
            directImportLinked: $data['direct_import_linked'],
            directImportInError: $data['direct_import_in_error'],
            lastReconciledAt: $data['last_reconciled_at'],
            debtOriginalBalance: $data['debt_original_balance'],
            debtInterestRates: $data['debt_interest_rates'],
            debtMinimumPayments: $data['debt_minimum_payments'],
            debtEscrowAmounts: $data['debt_escrow_amounts'],
            deleted: $data['deleted'],
        );
    }
}
