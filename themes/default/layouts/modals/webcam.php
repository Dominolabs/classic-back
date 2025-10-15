<?php

use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $model \app\models\WebcamForm */
?>
    <section class="modal  modal--active  modal-access-code" data-modal-not-close>
        <div class="modal__content">
            <h2 class="modal-access-code__title  modal__title">
                <?= Yii::t('webcam', 'Отримати доступ до камери') ?>
            </h2>
            <p class="modal-access-code__descr">
                <?= Yii::t('webcam', 'Щоб отримати доступ до камери введіть код доступу.') ?>
            </p>
            <?php $form = ActiveForm::begin([
                'id' => 'webcam-form',
                'options' => [
                    'class' => 'modal-access-code__form'
                ],
                'action' => Yii::$app->request->url
            ]); ?>

            <?= $form->field($model, 'password', [
                'options' => [
                    'class' => 'modal-access-code__form-group  form-group',
                ]
            ])->passwordInput([
                    'class' => 'modal-access-code__input',
                    'placeholder' => Yii::t('webcam', 'Введіть код'),
                ])->label(false)
            ?>

            <button type="submit" class="modal__btn btn">
                <?= Yii::t('webcam', 'Отримати доступ') ?>
            </button>
            <?php ActiveForm::end(); ?>
            <a href="tel:<?= preg_replace('/\D+/', '',
                !empty(Yii::$app->params['phone']) ? Yii::$app->params['phone'] : ''); ?>" class="modal__contact">
                <svg width="24px" height="24px">
                    <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-phone"></use>
                </svg>
                <span><?= !empty(Yii::$app->params['phone']) ? Yii::$app->params['phone'] : '' ?></span>
            </a>
        </div>
    </section>
<?php
$this->registerJs(
    "
    // Sends webcam form
    $('#webcam-form').on('beforeSubmit', function (e) {
        var form = $(this);
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serializeArray(),
            beforeSend: function() {
                preloader = preloaderSet({container: '.modal-access-code'});
            }
        }).done(function(data) {
            preloaderDel(preloader);
    
            if (data.success) {
                var content = $('.access-code-page');
                
                content.empty();
                
                content.append(data.data.camera1);
                
                $('.modal').remove();
                
                new TaskRunner({
                    callback: function () {
                        location.reload();
                    },
                    date: new Date(),
                    duration: 4 * 1000 * 60 * 60 // 4 hours
                });
            } else if (data.validation) {
                // Server validation failed
                form.yiiActiveForm('updateMessages', data.validation, true);
            }
        }).fail(function () {
            preloaderDel(preloader);
        });
    
        return false;
    });
    
    function TaskRunner(props) {
        var timer;
        var self = this;
    
        var defaultProps = {
            callback: null,
            date: new Date(),
            duration: 1000,
            interval: 1000,
            autorun: true
        }
    
        // Check arguments
        if (!props || !props.callback) {
            throw new Error('Object expecte a \"callback\" function');
            return;
        }
    
        if (!props.date) props.date = defaultProps.date;
        if (!props.duration || props.duration < 100) props.duration = defaultProps.duration;
        if (!props.interval || props.interval < 100) props.interval = defaultProps.interval;
    
        // Run timer 
        this.run = function () {
            self.timer = setInterval(function () {
                if (Date.parse(new Date()) >=  Date.parse(props.date) + props.duration) {
                    props.callback();
                    self.stop();
                }
            }, props.interval);
        }
    
        // Stop timer
        this.stop = function () {
            clearInterval(self.timer);
        };
    
        // Autorun timer
        if (props.autorun !== false) {
            self.run();
        }
    }
    ",
    View::POS_READY,
    'script'
);
?>