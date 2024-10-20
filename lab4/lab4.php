<?php

interface Notification 
{
    public function send(string $title, string $message);
}

class EmailNotification implements Notification
{
    private $adminEmail;

    public function __construct(string $adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }

    public function send(string $title, string $message): void
    {
        mail($this->adminEmail, $title, $message);
        echo "Sent email with title '$title' to '{$this->adminEmail}' that says '$message'.\n";
    }
}

// Клас для відправки повідомлень у Slack
class SlackMessenger
{
    private $login;
    private $apiKey;
    private $chatId;

    public function __construct(string $login, string $apiKey, string $chatId) // параметри: логін та API-ключ для доступу до Slack, ідентифікатор чату
    {
        $this->login = $login;
        $this->apiKey = $apiKey;
        $this->chatId = $chatId;
    }

    public function sendMessage(string $title, string $message): void
    {
        echo "Sent Slack message with title '$title' to chat '$this->chatId' that says '$message'.\n";
    }
}

// Адаптер, що реалізує інтерфейс Notification і використовує SlackMessenger
class SlackAdapter implements Notification
{
    private $slackMessenger;

    public function __construct(SlackMessenger $slackMessenger)
    {
        $this->slackMessenger = $slackMessenger;
    }

    public function send(string $title, string $message): void
    {
        $this->slackMessenger->sendMessage($title, $message);
    }
}

// Клас для відправки SMS
class SMSNotification
{
    private $phone;
    private $sender;

    public function __construct(string $phone, string $sender) // параметри:номер телефону для SMS та ім'я відправника
    {
        $this->phone = $phone;
        $this->sender = $sender;
    }

    public function sendSMS(string $message): void
    {
     
        echo "Sent SMS from '$this->sender' to '$this->phone' that says '$message'.\n";
    }
}

// Адаптер, що реалізує інтерфейс Notification і використовує SMSNotification
class SMSAdapter implements Notification
{
    private $smsNotification;

    public function __construct(SMSNotification $smsNotification)
    {
        $this->smsNotification = $smsNotification;
    }

    public function send(string $title, string $message): void
    {
        $this->smsNotification->sendSMS($message);
    }
}


function sendNotifications(Notification $notification, string $title, string $message)
{
    $notification->send($title, $message);
}


$emailNotification = new EmailNotification("admin222a@gmail.com");
sendNotifications($emailNotification, "Email Title", "Email message content.");

$slackMessenger = new SlackMessenger("user_login", "api_key", "chat_id");
$slackAdapter = new SlackAdapter($slackMessenger);
sendNotifications($slackAdapter, "Slack Title", "Some words lore, ipsulm Slack.");

$smsNotification = new SMSNotification("+113366888", "Duke Monte Cr.");
$smsAdapter = new SMSAdapter($smsNotification);
sendNotifications($smsAdapter, "SMS Title", "SMS lorem ipsulm vincentro....");
