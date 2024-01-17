# Testimonial GraphQl

**Testimonial GraphQl is a part of MageINIC Testimonial extension that adds GraphQL features.** This extension extends Testimonial definitions.

## 1. How to install

Run the following command in Magento 2 root folder:

```
composer require mageinic/testimonial-graphql

php bin/magento maintenance:enable
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento maintenance:disable
php bin/magento cache:flush
```

**Note:**
Magento 2 Testimonial GraphQL requires installing [MageINIC Testimonial](https://github.com/mageinic/Testimonial) in your Magento installation.

**Or Install via composer [Recommend]**
```
composer require mageinic/testimonial
```

## 2. How to use

- To view the queries that the **MageINIC Testimonial GraphQL** extension supports, you can check `Testimonial GraphQl User Guide.pdf` Or run `Testimonial Graphql.postman_collection.json` in Postman.

## 3. Get Support

- Feel free to [contact us](https://www.mageinic.com/contact.html) if you have any further questions.
- Like this project, Give us a **Star**
