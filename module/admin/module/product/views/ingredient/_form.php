<?php

use app\module\admin\models\Language;
use app\module\admin\module\product\models\Category;
use app\module\admin\module\product\models\Ingredient;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $ingredient app\module\admin\module\product\models\Ingredient | \app\components\ImageBehavior */
/* @var $descriptions array */
/* @var $languages array */
/* @var $placeholder string */
/* @var $form yii\widgets\ActiveForm */

$pizzaCategoryId = (int)Yii::$app->params['pizzaCategoryId'] ?: 0;
?>

<div class="ingredient-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="box-body">
        <?php if (!empty($languages)): ?>
            <ul class="nav nav-tabs" id="language">
                <?php foreach ($languages as $language): ?>
                    <li role="presentation">
                        <a href="#language<?= $language['language_id'] ?>" data-toggle="tab">
                            <img src="<?= Language::getImageUrl($language['image'], 16, 16) ?>"
                                 title="<?= $language['name'] ?>"/> <?= $language['name'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="tab-content">
                <?php foreach ($descriptions as $key => $description): ?>
                    <div role="tabpanel" class="tab-pane active" id="language<?= $key ?>">
                        <div class="box-body">
                            <?= $form->field($description, 'name')->textInput([
                                'id' => 'ingredient-description-name-' . $key,
                                'name' => 'IngredientDescription[' . $key . '][name]',
                            ]) ?>

                            <?= $form->field($description, 'portion_size')->textInput([
                                'id' => 'ingredient-portion-size-' . $key,
                                'name' => 'IngredientDescription[' . $key . '][portion_size]',
                            ]) ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php else: ?>
            <p>
                <?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более
                языков!
            </p>
        <?php endif; ?>

        <div id="ingredient-images-grid-view" class="grid-view">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <td class="text-left"><strong><?= $ingredient->getAttributeLabel('image') ?><strong>
                    </td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-left">
                        <a href="" id="thumb-image" data-toggle="ingredient-image" class="img-thumbnail">
                            <img src="<?= $ingredient->resizeImage($ingredient->image, 100, 100) ?>" alt=""
                                 title="" class="image-thumbnail" data-placeholder="<?= $placeholder ?>"/>
                        </a>
                        <input type="hidden" name="Ingredient[image]" value="<?= $ingredient->image ?>"
                               id="input-image"/>
                        <input type="file" accept="image/*" id="input-file-image" class="input-file-image"
                               name="Ingredient[imageFile]" value="" onchange="onImageChange(this)"
                               style="display: none"/>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <?= $form->field($ingredient, 'price')->textInput(['maxlength' => true]) ?>

        <?= $form->field($ingredient, 'category_id')->dropDownList(Category::getList( null, 1)) ?>

        <?= $form->field($ingredient, 'status')->dropDownList(Ingredient::getStatusesList()) ?>

        <?= $form->field($ingredient, 'sort_order')->textInput() ?>
        <?= $form->field($ingredient, 'pb_id')->textInput() ?>

       <div id="pizza_ingredients_checkboxes" style="margin-top: 20px; display: none">
        <h4 style="margin-bottom:  20px">Пица-конструтор</h4>
        <?= $form->field($ingredient, 'show_in_constructor_main')->checkbox() ?>

        <?= $form->field($ingredient, 'show_in_constructor_additional')->checkbox() ?>
       </div>

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
    $('#language-data a:first').tab('show');
    
    function onImageChange(item) {
        if (!item.value) {
            return;
        }

        var src = window.URL.createObjectURL(item.files[0]);
        
        $(item).closest('tr').find('.image-thumbnail').attr('src', src);
    }
    
    
    $(document).ready(function () {
      let pizza_cat_id = '$pizzaCategoryId';
      let block = $('#pizza_ingredients_checkboxes');
      let select = $('#ingredient-category_id');
      
      let selected_cat = $('#ingredient-category_id').val();
      if(Number(pizza_cat_id) === Number(selected_cat)){
         block.css('display', 'block');
      } 
      
      select.on('change', function(){
         selected_cat = $('#ingredient-category_id').val();
         if(Number(pizza_cat_id) === Number(selected_cat)){
            block.css('display', 'block');
         } else {
            block.css('display', 'none');
         }
      });    
    });
    
    ",
    View::POS_END,
    'script'
);
?>
