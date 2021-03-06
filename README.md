# F-seo Outer

* **Contributors:** Pavel Semin, Ruslan Giliazetdinov
* **Tags:** fabrica, fseo
* **Requires at least:** 5.2
* **Tested up to:** 5.2.3

Плагин для автоматизации работы со статьями через сервис https://fabrica.online

## Установка
* нажать кнопку "Clone or download" в верхнем правом углу на GitHub
* нажать "Download ZIP"
* скачать архив на компьютер
  * если плагин стоял ранее с установкой не через этот сервис, то старый удаляем в админ панели Wordpress (Плагины, Установленные, кликнуть "удалить" рядом с плагином, который хотите удалить)
* зайти в админ панель сайта
* перейти в "Плагины -> Добавить новый", кликнуть по кнопке "Загрузить плагин"
* выбрать архив, который был только что скачен и нажать "Установить" - Wordpress установит плагин
* активировать установленный плагин

## Настройки
Для настройки используется админ панель сайта Wordpress.

* **Fseo-outer -> Работа по API** нажать кнопку "Добавить плагины".
* **Fseo-outer -> Работа по API** нажать кнопку "Добавить" под надписью "Добавить/обновить кнопки в редакторе"
* **Добавить стили** в файл css используемой темы для блоков внимания (классы advice, stop, warning)
* **Настройки -> медиафайлы** - выставить размеры изображений под сайт
* **Fseo-outer -> Пользователи** нажать кнопку "Добавить пользователей fabrica(5) и пароли". После добавления пользователей на экране появятся пароли для них, которые нужно перенести на https://fabrica.online.
* **На фабрике** перейти в меню "Мои сайты" (https://fabrica.online/manager/sites) - кликнуть на название сайта
* В разделе "Пользователи" **внести пароли к пользователям**, полученные на предыдущем этапе

### Дополнительные настройки
Управление дополнительными настройками осуществляется на странице настроек **Fseo-outer -> Настройки постов** в админ панели сайта

* **Показывать иконки** - выводит иконки Yandex.Поделиться после содержания и в конце статьи
* **Показывать содержание** - выводит содержание внутри статьи после тега more
