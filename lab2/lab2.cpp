#include <iostream>
#include <memory>
#include <string>
#include <unordered_map>
using namespace std;

// Абстрактний клас для соціальних мереж
class SocialMedia {          //Визначаю методи connect та publishMessage, які реалізують підключення 
                            //до соціальної мережі та публікацію повідомлень
public:
    virtual void connect(const string& login, const string& password) = 0; 
    virtual void publishMessage(const string& message) = 0; 
    virtual ~SocialMedia() = default; 
};

// Клас для Facebook
class Facebook : public SocialMedia {
public:
    void connect(const string& login, const string& password) override {
        cout << "Підключено до Facebook з логіном: " << login << endl;
    }

    void publishMessage(const string& message) override {
        cout << "Повідомлення опубліковано у Facebook: " << message << endl;
    }
};

// Клас для LinkedIn
class LinkedIn : public SocialMedia {
public:
    void connect(const string& email, const string& password) override {
        cout << "Підключено до LinkedIn з електронною поштою: " << email << endl;
    }

    void publishMessage(const string& message) override {
        cout << "Повідомлення опубліковано у LinkedIn: " << message << endl;
    }
};
// Ці класи /|\ реалізують методи підключення та публікації повідомлень, використовуючи  параметри( логін і пароль для Facebook, електронна пошта і пароль для LinkedIn)

// Фабричний клас для створення об'єктів соціальних мереж
class SocialMediaFactory {
public:
    virtual shared_ptr<SocialMedia> createSocialMedia() = 0; // Фабричний метод
    virtual ~SocialMediaFactory() = default; 
};

// Фабрика для Facebook
class FacebookFactory : public SocialMediaFactory {
public:
    shared_ptr<SocialMedia> createSocialMedia() override {
        return make_shared<Facebook>(); 
    }
};

// Фабрика для LinkedIn
class LinkedInFactory : public SocialMediaFactory {
public:
    shared_ptr<SocialMedia> createSocialMedia() override {
        return make_shared<LinkedIn>(); 
    }
};


int main() {
    setlocale(LC_ALL, "UKR");
    // Використання фабрик для створення соціальних мереж
    shared_ptr<SocialMediaFactory> fbFactory = make_shared<FacebookFactory>();
    shared_ptr<SocialMedia> facebook = fbFactory->createSocialMedia();
    facebook->connect("GubanovA", "password123456789");
    facebook->publishMessage("Привіт, Facebook!");

    //Метод connect викликається з відповідними параметрами для кожної соціальної мережі, і публікується повідомлення

    shared_ptr<SocialMediaFactory> liFactory = make_shared<LinkedInFactory>();
    shared_ptr<SocialMedia> linkedIn = liFactory->createSocialMedia();
    linkedIn->connect("gubanov21@gmail.com", "password123");
    linkedIn->publishMessage("Привіт, LinkedIn!");

    return 0;
}
