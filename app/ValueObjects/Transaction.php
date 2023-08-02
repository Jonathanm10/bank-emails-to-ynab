<?php

namespace App\ValueObjects;

class Transaction
{
    public function __construct(
        public readonly string $accountId,
        public readonly int $amount,
        public readonly string $formattedAmount,
        public readonly int $signedAmountInMilliunits,
        public readonly string $transactionType,
    )
    {
    }

    public static function fromBase64EncodedString(string $string): self
    {
        $string = base64_decode(str_replace(['-', '_'], ['+', '/'], $string));

        preg_match('/compte ([\w-]+)/', $string, $accountMatches);
        $accountId = $accountMatches[1] ?? '';

        preg_match('/montant de ([\d.]+)/', $string, $amountMatches);
        $amount = $amountMatches[1] ?? '';

        $isDebit = str_contains($string, 'au débit du compte');

        return new self(
            accountId: $accountId,
            amount: $amount,
            formattedAmount: "CHF $amount",
            signedAmountInMilliunits: $isDebit ? $amount * -1000 : $amount * 1000,
            transactionType: $isDebit ? 'debit' : 'credit',
        );
    }
}
