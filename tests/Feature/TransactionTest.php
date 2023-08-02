<?php

use App\ValueObjects\Transaction;

it('should return negative amount when transaction is debit', function () {
    $transaction = Transaction::fromBase64EncodedString(
        'TWFkYW1lLCBNb25zaWV1ciwKClVuIG1vbnRhbnQgZGUgMTIuNjAgYSDDqXTDqSBpbnNjcml0IGF1IGTDqWJpdCBkdSBjb21wdGUgYWJjLiDDiXRhdCBhY3R1ZWwgZHUgY29tcHRlOiBDSEYgM+KAmTMzMy4zMy4KCgpQb3N0RmluYW5jZQ=='
    );

    expect($transaction->signedAmountInMilliunits)->toBe(-12600);
});

it('should return positive amount when transaction is credit', function () {
    $transaction = Transaction::fromBase64EncodedString(
        'TWFkYW1lLCBNb25zaWV1ciwKClVuIG1vbnRhbnQgZGUgMTIuNjAgYSDDqXTDqSBpbnNjcml0IGF1IGNyw6lkaXQgZHUgY29tcHRlIGFiYy4gw4l0YXQgYWN0dWVsIGR1IGNvbXB0ZTogQ0hGIDPigJkzMzMuMzMuCgoKUG9zdEZpbmFuY2U='
    );

    expect($transaction->signedAmountInMilliunits)->toBe(12600);
});
