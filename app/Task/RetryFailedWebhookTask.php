<?php

declare(strict_types=1);

namespace App\Task;

use App\Receiver\AbstractReceiver;
use App\Receiver\AsaasReceiver;
use App\Repository\SenderRepository;
use Carbon\Carbon;
use Exception;
use Hyperf\Crontab\Annotation\Crontab;

#[Crontab(rule: '* * * * *', name: 'Foo', callback: 'execute', memo: 'This is an example scheduled task')]
class RetryFailedWebhookTask
{
    public function execute()
    {
        $this->getSenders();
    }

    private function getSenders()
    {
        $senderRepository = new SenderRepository();
        $failedSenders = $senderRepository->getFailed();

        foreach ($failedSenders as $failed) {

            $updateData = [
                'attempts' => $failed['attempts']++,
                'sent_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'response' => null,
                'status' => false,
            ];

            /** @var AbstractReceiver $receiver */
            $receiver = match ($failed['provider']) {
                'ASAAS' => new AsaasReceiver(),
                default => throw new Exception("{$failed['provider']} is a invalid receiver"),
            };

            [
                'response' => $updateData['response'],
                'status' => $updateData['status']
            ] = $receiver->request($failed['url'], $failed['request'], $failed['headers']);

            $senderRepository->update((string) $failed['_id'], $updateData);
        }
    }
}
