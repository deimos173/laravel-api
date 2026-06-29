<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400">
    </a>
</p>

<h1 align="center"> Тестовое задание: </h1> 
<p align="center">
    <strong> Разработка сервиса синхронизации данных со стороннего API маркетплейса в базу данных </strong>
</p>

### Доступ к удаленной базе данных
Удаленная база данных MySQL создана на хостинге **Aiven.io**. Параметры для подключения:

```env
DB_CONNECTION=mysql
DB_HOST=mysql-1c7548f3-absolute-bd83.h.aivencloud.com
DB_PORT=11612
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=AVNS_dlVtYMfzXLr0tm7nLkJ
```

---
###  Названия созданных таблиц:
* **`orders`** — Заказы
* **`sales`** — Продажи
* **`stocks`** — Склады 
* **`incomes`** — Доходы
