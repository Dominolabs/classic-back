<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */

$model = new app\module\admin\module\feedback\models\Feedback();

?>

<section class="modal  modal-review" data-modal="review">
    <div class="modal__content">
        <a class="modal__close  js-modal-close">
            <svg width="26px" height="26px">
                <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-close"></use>
            </svg>
        </a>
        <div class="modal-review__main-content">
            <h2 class="modal-review__title"><?= Yii::t('reviews', 'Залишити відгук') ?></h2>
            <?php $form = ActiveForm::begin([
                'id' => 'review-form',
                'options' => [
                    'class' => 'modal-review__form'
                ],
                'action' => '/site/reviews'
            ]); ?>
                <div class="modal-review__form-content">
                    <div class="modal-review__input-wrap">
                        <label class="modal-review__input  input">
                            <?= $form->field($model, 'name', [
                            ])->textInput([
                                'class' => 'input__input',
                                'placeholder' => Yii::t('reviews', 'Прізвище, ім\'я'),
                            ])->label(false)
                            ?>
                        </label>
                    </div>
                    <div class="modal-review__input-wrap">
                        <label class="modal-review__input  input">
                            <?= $form->field($model, 'phone', [
                            ])->textInput([
                                'class' => 'input__input',
                                'placeholder' => Yii::t('reviews', 'Телефон'),
                            ])->label(false)
                            ?>
                        </label>
                    </div>
                    <div class="modal-review__input-wrap">
                        <label class="modal-review__input  input">
                            <?= $form->field($model, 'email', [
                            ])->textInput([
                                'class' => 'input__input',
                                'placeholder' => Yii::t('reviews', 'Email'),
                            ])->label(false)
                            ?>
                        </label>
                    </div>
                    <div class="modal-review__input-wrap">
                        <label class="modal-review__input  input">
                            <?= $form->field($model, 'text', [
                            ])->textarea([
                                'class' => 'input__input',
                                'rows' => 3,
                                'placeholder' => Yii::t('reviews', 'Відгук'),
                            ])->label(false)
                            ?>
                        </label>
                    </div>
                </div>
                <button type="submit" class="modal-review__submit btn"><?= Yii::t('reviews', 'Відправити') ?></button>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</section>

<?php
$this->registerJs( <<< EOT_JS_CODE

  $(document).on("submit", "#review-form", function(e){
    e.preventDefault();
    var data = $(this).serialize();
    $.post("/site/reviews", data, function(resp){
        if(resp.success) {
            $(".modal-review__main-content form").remove();
            $(".modal-review__main-content h2").text(resp.message);
        }
    }, "JSON");
  });

EOT_JS_CODE
);
?>