<?php
namespace FseoOuter\common\view;


/**
 * Class SettingPage
 * @package FseoOuter\common\view
 *
 * страница работы с плагином
 */
class SettingPage
{
    /**
     * вывод страницы настроке постов
     */
    public static function settingPage()
    { ?>
        <div class="wrap">
            <h1>Настройки постов</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('fseo-outer-settings-group'); ?>
                <p class="submit">
                    <input type="submit" class="button-primary" value="Сохранить"/>
                </p>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="fseo_outer_social">Показывать иконки шаринга?</label></th>
                        <td><input type="checkbox" name="fseo_outer_social" value="1" <?php checked('1', get_option('fseo_outer_social')); ?> /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="fseo_outer_contents">Показывать содержание?</label></th>
                        <td><input type="checkbox" name="fseo_outer_contents" value="1" <?php checked('1', get_option('fseo_outer_contents')); ?> /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="hidden_content">Свернуть содержание в спойлер?</label></th>
                        <td><input type="checkbox" name="hidden_content" value="1" <?php checked('1', get_option('hidden_content')); ?> /></td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="Сохранить"/>
                </p>
            </form>
        </div>
    <?php
    }
}
