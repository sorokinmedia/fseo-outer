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
    public static function settingPage()
    { ?>

        <div class="wrap">
            <h2>F-Seo Настройки с постах</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('fseo-outer-settings-group'); ?>
                <div class="input_section">
                    <div class="input_title">
                        <h3>Показывать соц. сети?</h3>
                        <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
                        </p>
                    </div>
                    <div class="option_input option_text">
                        <div class="option_full">
                            <div class="option_check">
                                <label for="fseo_outer_social" style="margin-right: 5px;">Показывать иконки?</label>
                                <input type="checkbox" name="fseo_outer_social"
                                       value="1" <?php checked('1', get_option('fseo_outer_social')); ?> />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="input_section">
                    <div class="input_title">
                        <h3>Показывать содержание?</h3>
                        <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
                        </p>
                    </div>
                    <div class="option_input option_text">
                        <div class="option_full">
                            <div class="option_check">
                                <label for="fseo_outer_contents" style="margin-right: 5px;">Показывать содержание?</label>
                                <input type="checkbox" name="fseo_outer_contents"
                                       value="1" <?php checked('1', get_option('fseo_outer_contents')); ?> />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    <?php
    }
}