<?php

namespace App\Commands;

use App\Data\YNAB\CreateTransactionData;
use App\Services\YNAB\YNABService;
use App\ValueObjects\Transaction;
use Exception;
use Google_Service_Gmail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class SyncCommand extends Command
{
    protected $signature = 'sync';

    protected $description = 'Sync bank emails to YNAB.';

    /**
     * @throws RequestException
     * @throws Exception
     */
    public function handle(Google_Service_Gmail $gmail, YNABService $ynab): int
    {
        $optParams = ['labelIds' => 'INBOX'];
        if ($lastMessageId = Storage::get('lastMessageId.txt')) {
            $optParams['q'] = "newer:$lastMessageId";
        }

        $this->info('Fetching messages...');
        $messages = $gmail->users_messages->listUsersMessages('me', $optParams)->getMessages();
        if (empty($messages)) {
            $this->info('No new messages found.');
            return Command::SUCCESS;
        }

        $this->info('Found ' . count($messages) . ' messages.');
        $this->info('Fetching message contents...');

        $lastMessageId = '';
        $transactions = collect();
        $ynabAccounts = $ynab->accounts();
        foreach ($messages as $index => $message) {
            $messageId = $message->getId();

            if ($index === 0) {
                $lastMessageId = $messageId;
            }

            $msg = $gmail->users_messages->get('me', $messageId);
            $body = $this->extractBodyFromParts($msg->getPayload()->getParts());
            if ($body === '') {
                $this->error("Could not get body for message $messageId");
                continue;
            }

            $transaction = Transaction::fromBase64EncodedString($body);

            if (! $ynabAccounts->firstWhere('id', $transaction->accountId)) {
                $this->error("No account found in YNAB with id {$transaction->accountId}");
                continue;
            }

            $this->info(sprintf(
                "Found transaction for account %s for %s (%s)",
                $transaction->accountId,
                $transaction->formattedAmount,
                $transaction->transactionType
            ));
            $transactions->push(CreateTransactionData::fromTransaction($transaction));
        }

        Storage::put('lastMessageId.txt', $lastMessageId);

        $this->info('Creating transactions...');
        $ynab->createTransactions($transactions);
        $this->info('Transactions created.');

        return Command::SUCCESS;
    }

    protected function extractBodyFromParts(array $parts): string
    {
        foreach ($parts as $part) {
            if (in_array($part->getMimeType(), ['multipart/alternative', 'multipart/mixed', 'multipart/related'])) {
                $data = $this->extractBodyFromParts($part->getParts());
                if ($data) {
                    return $data;
                }
            }

            if (in_array($part->getMimeType(), ['text/html', 'text/plain'], true)) {
                return $part->getBody()->data;
            }
        }
        return '';
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->everyTenMinutes();
    }
}
