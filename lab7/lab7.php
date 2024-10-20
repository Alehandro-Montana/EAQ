<?php

// Інтерфейс стратегії доставки
interface DeliveryStrategy
{
    public function calculateCost(float $distance): float;
    public function getDescription(): string;
}

// Клас для самовивозу
class PickupStrategy implements DeliveryStrategy
{
    public function calculateCost(float $distance): float
    {
        return 0; 
    }

    public function getDescription(): string
    {
        return "Самовивіз: безкоштовно";
    }
}

// Клас для доставки зовнішньою службою
class ExternalDeliveryStrategy implements DeliveryStrategy
{
    private $baseCost;
    private $costPerKm;

    public function __construct(float $baseCost, float $costPerKm)
    {
        $this->baseCost = $baseCost;
        $this->costPerKm = $costPerKm;
    }

    public function calculateCost(float $distance): float
    {
        if ($distance < 0) {
            throw new InvalidArgumentException("Відстань не може бути від'ємною.");
        }
        return $this->baseCost + ($this->costPerKm * $distance);
    }

    public function getDescription(): string
    {
        return "Зовнішня доставка: базова вартість {$this->baseCost}, вартість за км {$this->costPerKm}";
    }
}

// Клас для доставки власною службою
class InternalDeliveryStrategy implements DeliveryStrategy
{
    private $baseCost;
    private $costPerKm;

    public function __construct(float $baseCost, float $costPerKm) // в параметрах базова вартість доставки та вартість за км
    {
        $this->baseCost = $baseCost;
        $this->costPerKm = $costPerKm;
    }

    public function calculateCost(float $distance): float // парамаетри: відстань доставки
    {
        if ($distance < 0) {
            throw new InvalidArgumentException("Відстань не може бути від'ємною.");
        }
        return $this->baseCost + ($this->costPerKm * $distance);
    }

    public function getDescription(): string
    {
        return "Власна доставка: базова вартість {$this->baseCost}, вартість за км {$this->costPerKm}";
    }
}

// Контекст для вибору способу доставки
class DeliveryContext
{
    private $strategy;

    public function setStrategy(DeliveryStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function calculateDeliveryCost(float $distance): float
    {
        return $this->strategy->calculateCost($distance);
    }

    public function getDeliveryDescription(): string
    {
        return $this->strategy->getDescription();
    }
}

$deliveryContext = new DeliveryContext();

// Вибір способу самовивозу
$deliveryContext->setStrategy(new PickupStrategy());
echo $deliveryContext->getDeliveryDescription() . "\n"; 
echo "Вартість самовивозу: $" . $deliveryContext->calculateDeliveryCost(0) . "\n";

// Вибір зовнішньої служби доставки
$externalDelivery = new ExternalDeliveryStrategy(6.0, 1.5); 
$deliveryContext->setStrategy($externalDelivery);
echo $deliveryContext->getDeliveryDescription() . "\n"; 
echo "Вартість доставки зовнішньою службою за 10 км: $" . $deliveryContext->calculateDeliveryCost(10) . "\n";

// Вибір власної служби доставки
$internalDelivery = new InternalDeliveryStrategy(4.5, 1.0); 
$deliveryContext->setStrategy($internalDelivery);
echo $deliveryContext->getDeliveryDescription() . "\n"; 
echo "Вартість доставки власною службою за 10 км: $" . $deliveryContext->calculateDeliveryCost(10) . "\n";
