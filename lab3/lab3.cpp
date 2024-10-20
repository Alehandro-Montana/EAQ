#include <iostream>
#include <string>

using namespace std;

class IQueryBuilder {
public:
    // Метод для выбора полей
    virtual void select(const string& fields) = 0;

    // Метод для добавления условия
    virtual void where(const string& condition) = 0;

    // Метод для установки лимита
    virtual void limit(int count) = 0;

    // Метод для получения итогового SQL-запроса
    virtual string getSQL() const = 0;

    
    virtual ~IQueryBuilder() = default;
};

class PostgreSQLQueryBuilder : public IQueryBuilder {
private:
    string query; // Хранилище для SQL-запроса

public:
    PostgreSQLQueryBuilder() {
        query = ""; // Инициализация пустой строки
    }

    void select(const string& fields) override {
        query += "SELECT " + fields + " "; 
    }

    void where(const string& condition) override {
        query += "WHERE " + condition + " "; 
    }

    void limit(int count) override {
        query += "LIMIT " + to_string(count) + " "; 
    }

    string getSQL() const override {
        return query; // Возвращаем итоговый запрос
    }
};

class MySQLQueryBuilder : public IQueryBuilder {
private:
    string query; 

public:
    MySQLQueryBuilder() {
        query = ""; 
    }

    void select(const string& fields) override {
        query += "SELECT " + fields + " "; 
    }

    void where(const string& condition) override {
        query += "WHERE " + condition + " "; 
    }

    void limit(int count) override {
        query += "LIMIT " + to_string(count) + " "; 
    }

    string getSQL() const override {
        return query; 
    }
};

int main() {
    // Использование для PostgreSQL
    IQueryBuilder* pgBuilder = new PostgreSQLQueryBuilder();
    pgBuilder->select("*");
    pgBuilder->where("age > 22");
    pgBuilder->limit(10);
    cout << "PostgreSQL Query: " << pgBuilder->getSQL() << endl;
    delete pgBuilder;

    // Использование для MySQL
    IQueryBuilder* mySQLBuilder = new MySQLQueryBuilder();
    mySQLBuilder->select("name, email");
    mySQLBuilder->where("status = 'active'");
    mySQLBuilder->limit(5);
    cout << "MySQL Query: " << mySQLBuilder->getSQL() << endl;
    delete mySQLBuilder;

    return 0;
}
