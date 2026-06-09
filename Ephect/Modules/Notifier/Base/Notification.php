<?php

namespace Ephect\Modules\Notifier\Base;

use Ephect\Modules\HttpStorage\Session\Session;
use Ephect\Modules\Notifier\Enums\NotificationType;
use Exception;

class Notification implements NotificationInterface
{
    private const NOTIFICATION_KEY = 'flash';
    private Session $session;

    public function __construct()
    {
        $this->session = new Session();
        $id = $this->session->start();
    }

    /**
     * @throws Exception
     */
    public function getInfo(): array
    {
        return $this->get(NotificationType::Info);
    }

    public function get(NotificationType $type): array
    {
        $flashes = $this->session->read(self::NOTIFICATION_KEY) ?? [];
        if (isset($flashes[$type->name])) {
            $messages = $flashes[$type->name];
            unset($flashes[$type->name]);
            $this->session->write(self::NOTIFICATION_KEY, $flashes);

            return $messages;
        }

        return [];
    }

    public function getError(): array
    {
        return $this->get(NotificationType::Error);
    }

    public function getSuccess(): array
    {
        return $this->get(NotificationType::Success);
    }

    public function getWarning(): array
    {
        return $this->get(NotificationType::Warning);
    }

    public function setInfo(string $message, mixed ...$params): void
    {
        $this->set(NotificationType::Info, $message, ...$params);
    }

    public function set(NotificationType $type, string $message, mixed ...$params): void
    {
        $message = sprintf($message, ...$params);
        $flashes = $this->session->read(self::NOTIFICATION_KEY) ?? [];
        $flashes[$type->name][] = $message;

        $this->session->write(self::NOTIFICATION_KEY, $flashes);
    }

    public function setError(string $message, mixed ...$params): void
    {
        $this->set(NotificationType::Error, $message, ...$params);
    }

    public function setSuccess(string $message, mixed ...$params): void
    {
        $this->set(NotificationType::Success, $message, ...$params);
    }

    public function setWarning(string $message, mixed ...$params): void
    {
        $this->set(NotificationType::Warning, $message, ...$params);
    }

    public function hasInfo(): bool
    {
        return $this->has(NotificationType::Info);
    }

    public function has(NotificationType $type): bool
    {
        if ($this->session->has(self::NOTIFICATION_KEY)) {
            $flashes = $this->session->read(self::NOTIFICATION_KEY);

            return isset($flashes[$type->name]);
        }

        return false;
    }

    public function hasError(): bool
    {
        return $this->has(NotificationType::Error);
    }

    public function hasSuccess(): bool
    {
        return $this->has(NotificationType::Success);
    }

    public function hasWarning(): bool
    {
        return $this->has(NotificationType::Warning);
    }

    public function clear(): void
    {
        $this->session->remove(self::NOTIFICATION_KEY);
    }
}
