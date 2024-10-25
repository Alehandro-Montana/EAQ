<?php

abstract class EntityUpdater {       // Абстрактний клас EntityUpdater, що визначає шаблонний метод 
    public function update($entityId, $data) {
        $entity = $this->fetchEntity($entityId);
        if (!$this->validateData($data)) {
            $this->handleValidationError();
            return $this->getResponse(400, 'Validation Error');
        }

        $this->beforeSave($entity, $data);
        $this->saveEntity($entity, $data);
        $this->afterSave($entity, $data);

        return $this->getResponse(200, 'Success');
    }
// методи для отримання сутності, збереження та формування відповіді
    protected abstract function fetchEntity($entityId);
    protected abstract function validateData($data);
    protected abstract function saveEntity($entity, $data);
    
    protected function getResponse($code, $status, $data = null) {
        return [
            'code' => $code,
            'status' => $status,
            'data' => $data
        ];
    }
// Хуки для налаштування перед та після збереження
    protected function beforeSave($entity, $data) {}
    protected function afterSave($entity, $data) {}

     // Обробка помилки валідації 
    protected function handleValidationError() {}
}


// Клас для оновлення Товару
class ProductUpdater extends EntityUpdater {
    protected function fetchEntity($entityId) {
        // Отримати товар за ID (з бази даних, наприклад)
        return Database::getProductById($entityId);
    }

    protected function validateData($data) {
        // Перевірити валідність даних
        if (empty($data['name']) || empty($data['price'])) {
            return false; 
        }
        return true;
    }

    protected function saveEntity($entity, $data) {
        // Зберегти товар
        $entity->name = $data['name'];
        $entity->price = $data['price'];
        Database::saveProduct($entity);
    }

    protected function handleValidationError() {
        // Сповіщення адміну у месенджер
        Messenger::notifyAdmin("Помилка валідації товару.");
    }
}


// Клас для оновлення Користувача
class UserUpdater extends EntityUpdater {
    protected function fetchEntity($entityId) {
        // Отримати користувача за ID
        return Database::getUserById($entityId);
    }

    protected function validateData($data) {
        
        if (isset($data['email'])) {
            return false; 
        }
        return true;
    }

    protected function saveEntity($entity, $data) {
        // Зберегти користувача без зміни email
        $entity->name = $data['name']; // інші поля
        Database::saveUser($entity);
    }
}


// Клас для оновлення Замовлення
class OrderUpdater extends EntityUpdater {
    protected function fetchEntity($entityId) {
        // Отримати замовлення за ID
        return Database::getOrderById($entityId);
    }

    protected function validateData($data) {
        // Валідація даних для замовлення
        if (empty($data['items']) || !is_array($data['items'])) {
            return false; 
        }
        return true;
    }

    protected function saveEntity($entity, $data) {
        // Зберегти замовлення
        $entity->items = $data['items']; // Оновлення товарів
        $entity->totalPrice = $data['totalPrice']; // Оновлення загальної ціни
        Database::saveOrder($entity);
    }

    protected function getResponse($code, $status, $data = null) {  // Повернути код, статус та JSON-представлення замовлення
        
        $orderData = json_encode($data); 
        return parent::getResponse($code, $status, $orderData);
    }
}