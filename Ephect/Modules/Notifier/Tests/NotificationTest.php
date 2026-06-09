<?php

namespace Ephect\Modules\Notifier\Tests;

use Ephect\Modules\HttpStorage\Session\Session;
use Ephect\Modules\Notifier\Enums\NotificationType;
use Ephect\Modules\Notifier\Base\Notification;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

//include dirname(__DIR__) . DIRECTORY_SEPARATOR .  "bootstrap.php";
class NotificationTest extends TestCase
{
    #[Test]
    public function setAndGetFlashTest()
    {
        $flash = new Notification();
        $flash->set(NotificationType::Success, "Great job!");
        $flash->set(NotificationType::Error, "Bad luck!");
        $this->assertTrue($flash->has(NotificationType::Success));
        $this->assertTrue($flash->has(NotificationType::Error));
        $this->assertEquals(['Great job!'], $flash->get(NotificationType::Success));
        $this->assertEquals([], $flash->get(NotificationType::Warning));
    }

    protected function setUp(): void
    {
        $session = new Session();
        $session->clear();
        $session->start();
    }
}
