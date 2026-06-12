<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Channels;

use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelFeatures\Modules\Notifications\Contracts\DestinationValidatorInterface;
use AndyDefer\LaravelFeatures\Modules\Notifications\Contracts\NotifiableInterface;
use AndyDefer\LaravelFeatures\Modules\Notifications\Enums\NotificationChannel;
use AndyDefer\LaravelFeatures\Modules\Notifications\Records\NotificationRecord;
use AndyDefer\LaravelFeatures\Modules\Notifications\Repositories\NotificationRepository;
use AndyDefer\Logger\Contracts\LoggerInterface;
use AndyDefer\Logger\Records\LogDataRecord;

final class DatabaseChannel extends AbstractChannel
{
    public function __construct(
        private readonly NotificationRepository $repository,
        private readonly LoggerInterface $logger,
        private readonly DestinationValidatorInterface $validator,
    ) {}

    protected function before(NotifiableInterface $notifiable, NotificationRecord $record): void
    {
        $destinations = $notifiable->getNotificationDestinations()->getByChannel(NotificationChannel::DATABASE);
        $destinationsList = [];

        foreach ($destinations as $destination) {
            $destinationsList[] = $destination->value;
            $this->validator->validate($destination);
        }

        $logPayload = new StrictDataObject([
            'event' => 'database_channel_before',
            'type' => $record->type,
            'notifiable_type' => $record->notifiable_type,
            'notifiable_id' => $record->notifiable_id,
            'destinations' => $destinationsList,
            'channel' => $record->channel,
        ]);

        $this->logger->info(new LogDataRecord(
            type: 'notification',
            payload: $logPayload
        ));
    }

    protected function execute(NotifiableInterface $notifiable, NotificationRecord $record): bool
    {
        $this->repository->create($record);

        return true;
    }

    protected function after(
        NotifiableInterface $notifiable,
        NotificationRecord $record,
        bool $success,
        ?\Exception $error = null
    ): void {
        $destinations = $notifiable->getNotificationDestinations()->getByChannel(NotificationChannel::DATABASE);
        $destinationsList = [];

        foreach ($destinations as $destination) {
            $destinationsList[] = $destination->value;
        }

        if ($success) {
            $logPayload = new StrictDataObject([
                'event' => 'database_channel_success',
                'type' => $record->type,
                'notifiable_type' => $record->notifiable_type,
                'notifiable_id' => $record->notifiable_id,
                'destinations' => $destinationsList,
                'channel' => $record->channel,
            ]);

            $this->logger->info(new LogDataRecord(
                type: 'notification',
                payload: $logPayload
            ));
        } else {
            $logPayload = new StrictDataObject([
                'event' => 'database_channel_failed',
                'type' => $record->type,
                'notifiable_type' => $record->notifiable_type,
                'notifiable_id' => $record->notifiable_id,
                'destinations' => $destinationsList,
                'channel' => $record->channel,
                'error' => $error?->getMessage(),
            ]);

            $this->logger->error(new LogDataRecord(
                type: 'notification',
                payload: $logPayload
            ));
        }
    }
}
