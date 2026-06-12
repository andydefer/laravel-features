<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Channels;

use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelFeatures\Modules\Notifications\Contracts\DestinationValidatorInterface;
use AndyDefer\LaravelFeatures\Modules\Notifications\Contracts\NotifiableInterface;
use AndyDefer\LaravelFeatures\Modules\Notifications\Enums\NotificationChannel;
use AndyDefer\LaravelFeatures\Modules\Notifications\Records\NotificationRecord;
use AndyDefer\Logger\Contracts\LoggerInterface;
use AndyDefer\Logger\Records\LogDataRecord;
use Illuminate\Mail\Mailer;

final class MailChannel extends AbstractChannel
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly LoggerInterface $logger,
        private readonly DestinationValidatorInterface $validator,
    ) {}

    protected function before(NotifiableInterface $notifiable, NotificationRecord $record): void
    {
        $destinations = $notifiable->getNotificationDestinations()->getByChannel(NotificationChannel::MAIL);
        $destinationsList = [];

        foreach ($destinations as $destination) {
            $destinationsList[] = $destination->value;
            $this->validator->validate($destination);
        }

        $logPayload = new StrictDataObject([
            'event' => 'mail_channel_before',
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
        $destinations = $notifiable->getNotificationDestinations()->getByChannel(NotificationChannel::MAIL);
        $data = $record->data->toArray();

        foreach ($destinations as $destination) {
            $this->mailer->send([], [], function ($message) use ($destination, $data) {
                $message->to($destination->value)
                    ->subject($data['subject'] ?? 'Notification')
                    ->html($data['body'] ?? '');
            });
        }

        return true;
    }

    protected function after(
        NotifiableInterface $notifiable,
        NotificationRecord $record,
        bool $success,
        ?\Exception $error = null
    ): void {
        $destinations = $notifiable->getNotificationDestinations()->getByChannel(NotificationChannel::MAIL);
        $destinationsList = [];

        foreach ($destinations as $destination) {
            $destinationsList[] = $destination->value;
        }

        if ($success) {
            $logPayload = new StrictDataObject([
                'event' => 'mail_channel_success',
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
                'event' => 'mail_channel_failed',
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
