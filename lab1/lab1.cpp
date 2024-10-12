#include <iostream>
#include <memory>
#include <unordered_map>
#include <locale>
#include <string>
using namespace std;


// Абстрактний клас для сховищ
class Storage {
public:
    virtual void connect() = 0; 
    virtual void uploadFile(const string& filePath) = 0; 
    virtual void downloadFile(const string& fileName) = 0; 
    virtual ~Storage() = default; 
};

// Клас для локального диска
class LocalStorage : public Storage {
public:
    void connect() override {   // для підключення до сховища
        cout << "Підключено до локального диска." << endl;
    }

    void uploadFile(const string& filePath) override {      // приймає шлях до файлу та завантажує файл на сховище
        cout << "Файл " << filePath << " завантажено на локальний диск." << endl;
    }

    void downloadFile(const string& fileName) override {  // приймає назву файла та завантажує його з сховища
        cout << "Файл " << fileName << " завантажено з локального диска." << endl;
    }
};

// Клас для Amazon S3
class S3Storage : public Storage {
public:
    void connect() override {
        cout << "Підключено до Amazon S3." << endl;
    }

    void uploadFile(const string& filePath) override {
        cout << "Файл " << filePath << " завантажено на Amazon S3." << endl;
    }

    void downloadFile(const string& fileName) override {
        cout << "Файл " << fileName << " завантажено з Amazon S3." << endl;
    }
};

// Клас для користувача
class User {
private:
    string name;
    shared_ptr<Storage> storage; 
public:
    User(const string& userName) : name(userName) {}

    void connectToStorage(const shared_ptr<Storage>& newStorage) {
        storage = newStorage;
        storage->connect();
    }

    void uploadFile(const string& filePath) {
        if (storage) {
            storage->uploadFile(filePath);
        }
        else {
            cout << "Сховище не вибрано." << endl;
        }
    }

    void downloadFile(const string& fileName) {
        if (storage) {
            storage->downloadFile(fileName);
        }
        else {
            cout << "Сховище не вибрано." << endl;
        }
    }
};

// Клас для управління користувачами та сховищами
class FileManager {
private:
    unordered_map<string, shared_ptr<User>> users; // Список користувачів

public:
    void addUser(const string& userName) {
        users[userName] = make_shared<User>(userName); // додає користувача за його ім'м
    }

    shared_ptr<User> getUser(const string& userName) {        // приймає ім'я користувача та отримує користувача за
        return users[userName];
    }

    void listAvailableStorages() {   // виводить список доступних сховищ
        cout << "Доступні сховища: 1. Локальний диск, 2. Amazon S3" << endl;
    }
};

// Патерн Одинак для менеджера файлів
class SingletonFileManager {
public:
    static SingletonFileManager& getInstance() {   // для отримання екзмепляру менеджера
        static SingletonFileManager instance;
        return instance;
    }

    FileManager& getFileManager() {    //отримання менеджеру файлів
        return fileManager;
    }

private:
    FileManager fileManager;
    SingletonFileManager() = default; 
    SingletonFileManager(const SingletonFileManager&) = delete; 
    SingletonFileManager& operator=(const SingletonFileManager&) = delete; 
};


int main() {
    setlocale(LC_ALL, "UKR");
    auto& singletonManager = SingletonFileManager::getInstance();
    auto& fileManager = singletonManager.getFileManager();

    // Додати користувачів
    fileManager.addUser("Alice");
    fileManager.addUser("Bob");

    // Вибрати користувача
    auto user = fileManager.getUser("Alice");

    // Вивести доступні сховища
    fileManager.listAvailableStorages();

    // Підключитися до локального диска
    user->connectToStorage(std::make_shared<LocalStorage>());
    user->uploadFile("document.txt");
    user->downloadFile("document.txt");

    // Підключитися до Amazon S3
    user->connectToStorage(std::make_shared<S3Storage>());
    user->uploadFile("photo.png");
    user->downloadFile("photo.png");

    return 0;
}