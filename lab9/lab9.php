<?php


// Інтерфейс для класів, що приймають  відвідувача
interface Visitable {
    public function accept(Visitor $visitor): void;
}

// Класс , що  зберігає список департаментів
class Company implements Visitable {
    private array $departments;

    
    public function __construct(array $departments) {
        $this->departments = $departments;
    }

    // Метод accept дозволяє відвідувачу "зайти" в об'єкт департаменту
    public function accept(Visitor $visitor): void {
        $visitor->visitCompany($this);
    }

    
    public function getDepartments(): array {
        return $this->departments;
    }
}

// Клас , що  зберігає список співробітників
class Department implements Visitable {
    private array $employees;

    
    public function __construct(array $employees) {
        $this->employees = $employees;
    }

    // Метод accept дозволяє відвідувачу "зайти" в об'єкт департамента
    public function accept(Visitor $visitor): void {
        $visitor->visitDepartment($this);
    }

    public function getEmployees(): array {
        return $this->employees;
    }
}

// Клас Employee, що представляє окремого співробітника з позицією та зарплатою
class Employee implements Visitable {
    private string $position;
    private float $salary;

        public function __construct(string $position, float $salary) {
        $this->position = $position;
        $this->salary = $salary;
    }

    // Метод accept позволяет посетителю "зайти" в объект сотрудника
    public function accept(Visitor $visitor): void {
        $visitor->visitEmployee($this);
    }

    
    public function getPosition(): string {
        return $this->position;
    }

  
    public function getSalary(): float {
        return $this->salary;
    }
}



// Інтерфейс Visitor визначає методи відвідування кожного рівня структури 
interface Visitor {
    public function visitCompany(Company $company): void;
    public function visitDepartment(Department $department): void;
    public function visitEmployee(Employee $employee): void;
}

// Клас,що  реалізує відвідувача для створення звіту про зарплати
class SalaryReportVisitor implements Visitor {
    private string $report = ""; 

    //  відвідування компанії та генерації звіту по всіх департаментах
    public function visitCompany(Company $company): void {
        $this->report .= "Звіт з компанії:\n";
        
        foreach ($company->getDepartments() as $department) {
            $department->accept($this);
        }
    }

    //  відвідування департаменту та генерації звіту за всіма співробітниками
    public function visitDepartment(Department $department): void {
        $this->report .= "  Звіт з департаменту:\n";
        
        foreach ($department->getEmployees() as $employee) {
            $employee->accept($this);
        }
    }

    //  відвідування співробітника та додавання його даних до звіту
    public function visitEmployee(Employee $employee): void {
        $this->report .= "    Позиція: {$employee->getPosition()}, Зарплата: {$employee->getSalary()}\n";
    }

    // отримання повного звіту
    public function getReport(): string {
        return $this->report;
    }
}




$employee1 = new Employee("Менеджер", 50000);
$employee2 = new Employee("Розробник", 70000);
$employee3 = new Employee("Тестувальник", 40000);


$department1 = new Department([$employee1, $employee2]);
$department2 = new Department([$employee3]);


$company = new Company([$department1, $department2]);


$salaryReportVisitor = new SalaryReportVisitor();

// Отримання звіту для всієї компанії
$company->accept($salaryReportVisitor);
echo "Звіт для компанії:\n";
echo $salaryReportVisitor->getReport();

// Очищення звіту та отримання звіту для конкретного департаменту
$salaryReportVisitor = new SalaryReportVisitor();
$department1->accept($salaryReportVisitor);
echo "\nЗвіт для департамента:\n";
echo $salaryReportVisitor->getReport();
