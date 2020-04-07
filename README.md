# MY FIRST CMS

*Учебный проект "Простая CMS на базе PHP и MySQL" Подробные пояснения, пошаговая инструкция по написанию данной CMS, а также рекомендации для дальнейшей работы можно найти на сайте It For Free: http://fkn.ktu10.com/?q=node/9428*

## Как развернуть:

   1) Загрузите исходный код на ваш компьютер способом, указанным [в начале этой заметки (форк и затем клон форка)](http://fkn.ktu10.com/?q=node/9428)

   2) Открываем проект в своей программе для разработки (например, NetBeans)

   3) Разворачиваем дамп базы данных:
        - сначала создайте в mysql новую базу данных с имененем `cms`
        - а потом разверните в ней дамп из файла `db_cms.sql` (лежит в корне данного проекта): http://fkn.ktu10.com/?q=node/8944

   4) Создаёте в корне проекта файл `config-local.php` и добавьте в него как минимум такое содержимое (укажите пароль к бд):
      ```php
        <?php

        // вместо 1234 укажите свой пароль к базе данных
        $CmsConfiguration["DB_PASSWORD"] = "1234"; // переопределяем пароль к базе данных
       ```

   5) Следуем инструкциям http://fkn.ktu10.com/?q=node/9428

Удачной разработки!


ALTER TABLE articles ADD COLUMN active TINYINT(1) NOT NULL DEFAULT 0;