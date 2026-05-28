<?php

namespace Ephect\Modules\Notifier\Base;

use Ephect\Modules\Notifier\Enums\NotificationType;

interface NotificationInterface
{
    public function get(NotificationType $type): array;

    public function set(NotificationType $type, string $message, mixed ...$params): void;

    public function has(NotificationType $type): bool;

    public function getInfo(): array;

    public function getError(): array;

    public function getSuccess(): array;

    public function getWarning(): array;

    public function setInfo(string $message, mixed ...$params): void;

    public function setError(string $message, mixed ...$params): void;

    public function setSuccess(string $message, mixed ...$params): void;

    public function setWarning(string $message, mixed ...$params): void;

    public function hasInfo(): bool;

    public function hasError(): bool;

    public function hasSuccess(): bool;

    public function hasWarning(): bool;

    public function clear(): void;
}
