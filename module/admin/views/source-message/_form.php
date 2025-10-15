<?php
/**
 * Create/update form view.
 */

use app\module\admin\models\Language;

use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\SourceMessage */
/* @var $form yii\widgets\ActiveForm */
/* @var $languages array */
/* @var $messages array */
?>

<div class="source-message-form box box-primary">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body table-responsive">

        <?= $form->field($model, 'category')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

        <?php if (!empty($languages)): ?>
        <ul class="nav nav-tabs" id="language">
            <?php foreach ($languages as $language): ?>
                <li role="presentation"><a href="#language<?= $language['language_id'] ?>" data-toggle="tab"><img src="<?= Language::getImageUrl($language['image'], 16, 16) ?>" title="<?= $language['name'] ?>" /> <?= $language['name'] ?></a></li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content">
            <?php foreach ($messages as $key => $message): ?>
                <div role="tabpanel" class="tab-pane active" id="language<?= $key ?>">
                    <div class="box-body">
                        <?= $form->field($message, 'translation')->textInput(['name' => 'Message[' . $key . '][translation]']) ?>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
        <?php else: ?>
            <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более языков!</p>
        <?php endif; ?>
    </div>
    <div class="box-footer">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(
    "
    $('#language a:first').tab('show');
    $('#language-seo a:first').tab('show');
    ",
    View::POS_READY,
    'script'
);
?>
