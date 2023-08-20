<?php

namespace App\Commands;

use App\Data\YNAB\CreateTransactionData;
use App\Services\YNAB\YNABService;
use App\ValueObjects\Transaction;
use Exception;
use Google_Service_Gmail;
use Google_Service_Gmail_ModifyMessageRequest;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Client\RequestException;
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
        $this->info('Fetching messages...');
        $optParams = ['labelIds' => 'INBOX', 'q' => 'is:unread'];
        $messages = $gmail->users_messages->listUsersMessages('me', $optParams)->getMessages();
        if (empty($messages)) {
            $this->info('No new messages found.');
            return Command::SUCCESS;
        }

        $this->info('Found ' . count($messages) . ' messages.');
        $this->info('Fetching message contents...');

        $transactions = collect();
        $ynabAccounts = $ynab->accounts();
        foreach ($messages as $message) {
            $messageId = $message->getId();

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

            $mods = new Google_Service_Gmail_ModifyMessageRequest();
            $mods->setRemoveLabelIds(['UNREAD']);
            $gmail->users_messages->modify('me', $messageId, $mods);

            $this->info(sprintf(
                "Found transaction for account %s for %s (%s)",
                $transaction->accountId,
                $transaction->formattedAmount,
                $transaction->transactionType
            ));
            $transactions->push(CreateTransactionData::fromTransaction($transaction));
        }

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
        $scheduling = $schedule->command(static::class)->everyTenMinutes();
        if (config('app.email_output_on_failure')) {
            $scheduling->emailOutputOnFailure(config('app.email_output_on_failure'));
        }
    }
}
