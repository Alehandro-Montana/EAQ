<?php

//  Інтерфейс Посередника 

interface Mediator {
    public function notify(object $sender, string $event): void;
}

//  Базовий клас для елементів форми 

abstract class FormElement {
    protected Mediator $mediator;

    public function setMediator(Mediator $mediator): void {
        $this->mediator = $mediator;
    }

    public function setEnabled(bool $isActive): void {
        
        echo get_class($this) . ($isActive ? " увімкнено\n" : " вимкнено\n");// Логіка активації або деактивації елементу форми
    }
}

//  Конкретні елементи форми 

class DeliveryDatePicker extends FormElement {
    public function selectDate(string $date): void {
        // Вибір дати доставки
        echo "Обрана дата доставки: $date\n";
        $this->mediator->notify($this, "dateSelected");
    }
}

class TimeSlotSelector extends FormElement {
    public function updateTimeSlots(array $timeSlots): void {
        // Оновлення доступних проміжків часу
        echo "Доступні проміжки часу: " . implode(", ", $timeSlots) . "\n";
    }
}

class ReceiverCheckbox extends FormElement {
    private bool $isOtherReceiver = false;

    public function toggle(bool $state): void {
        // Перемикання стану чекбоксу
        $this->isOtherReceiver = $state;
        echo "Отримувач — інша особа: " . ($state ? "так" : "ні") . "\n";
        $this->mediator->notify($this, "receiverToggled");
    }

    public function isOtherReceiver(): bool {
        return $this->isOtherReceiver;
    }
}

class ReceiverNameField extends FormElement {
    public function setRequired(bool $isRequired): void {
        // Встановлення обов’язковості поля
        echo "Поле 'Ім'я отримувача' " . ($isRequired ? "обов'язкове\n" : "не обов'язкове\n");
    }
}

class ReceiverPhoneField extends FormElement {
    public function setRequired(bool $isRequired): void {
        // Встановлення обов’язковості поля
        echo "Поле 'Телефон отримувача' " . ($isRequired ? "обов'язкове\n" : "не обов'язкове\n");
    }
}

class SelfPickupCheckbox extends FormElement {
    private bool $isSelfPickup = false;

    public function toggle(bool $state): void {
        // Перемикання стану чекбоксу
        $this->isSelfPickup = $state;
        echo "Самовивіз: " . ($state ? "так" : "ні") . "\n";
        $this->mediator->notify($this, "selfPickupToggled");
    }

    public function isSelfPickup(): bool {
        return $this->isSelfPickup;
    }
}

//  Конкретний Посередник 

class OrderFormMediator implements Mediator {
    private DeliveryDatePicker $datePicker;
    private TimeSlotSelector $timeSlotSelector;
    private ReceiverCheckbox $receiverCheckbox;
    private ReceiverNameField $receiverNameField;
    private ReceiverPhoneField $receiverPhoneField;
    private SelfPickupCheckbox $selfPickupCheckbox;

    public function __construct(
        DeliveryDatePicker $datePicker,
        TimeSlotSelector $timeSlotSelector,
        ReceiverCheckbox $receiverCheckbox,
        ReceiverNameField $receiverNameField,
        ReceiverPhoneField $receiverPhoneField,
        SelfPickupCheckbox $selfPickupCheckbox
    ) {
        $this->datePicker = $datePicker;
        $this->timeSlotSelector = $timeSlotSelector;
        $this->receiverCheckbox = $receiverCheckbox;
        $this->receiverNameField = $receiverNameField;
        $this->receiverPhoneField = $receiverPhoneField;
        $this->selfPickupCheckbox = $selfPickupCheckbox;

        $this->datePicker->setMediator($this);
        $this->timeSlotSelector->setMediator($this);
        $this->receiverCheckbox->setMediator($this);
        $this->receiverNameField->setMediator($this);
        $this->receiverPhoneField->setMediator($this);
        $this->selfPickupCheckbox->setMediator($this);
    }

    public function notify(object $sender, string $event): void {
        if ($sender === $this->datePicker && $event === "dateSelected") {
            $this->timeSlotSelector->updateTimeSlots($this->getAvailableTimeSlots());
        }

        if ($sender === $this->receiverCheckbox && $event === "receiverToggled") {
            $isOtherReceiver = $this->receiverCheckbox->isOtherReceiver();
            $this->receiverNameField->setRequired($isOtherReceiver);
            $this->receiverPhoneField->setRequired($isOtherReceiver);
        }

        if ($sender === $this->selfPickupCheckbox && $event === "selfPickupToggled") {
            $isSelfPickup = $this->selfPickupCheckbox->isSelfPickup();
            $this->setDeliveryElementsActive(!$isSelfPickup);
        }
    }

    private function getAvailableTimeSlots(): array {
        return ["09:00 - 11:00", "11:00 - 13:00", "13:00 - 15:00"];
    }

    private function setDeliveryElementsActive(bool $isActive): void {
        $this->datePicker->setEnabled($isActive);
        $this->timeSlotSelector->setEnabled($isActive);
        $this->receiverCheckbox->setEnabled($isActive);
        $this->receiverNameField->setEnabled($isActive);
        $this->receiverPhoneField->setEnabled($isActive);
    }
}



$datePicker = new DeliveryDatePicker();
$timeSlotSelector = new TimeSlotSelector();
$receiverCheckbox = new ReceiverCheckbox();
$receiverNameField = new ReceiverNameField();
$receiverPhoneField = new ReceiverPhoneField();
$selfPickupCheckbox = new SelfPickupCheckbox();

$mediator = new OrderFormMediator(
    $datePicker,
    $timeSlotSelector,
    $receiverCheckbox,
    $receiverNameField,
    $receiverPhoneField,
    $selfPickupCheckbox
);


echo "Користувач обирає дату доставки:\n";
$datePicker->selectDate("2024-12-25"); // Оновлює проміжки часу на основі вибраної дати

echo "\nКористувач вказує, що отримувач — інша особа:\n";
$receiverCheckbox->toggle(true); // Поля імені та телефону отримувача стають обов'язковими

echo "\nКористувач обирає самовивіз:\n";
$selfPickupCheckbox->toggle(true); // Елементи форми доставки стають неактивними
