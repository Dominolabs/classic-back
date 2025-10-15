<?php
/**
 * Create/update form view.
 */

use app\module\admin\models\Language;
use app\module\admin\models\Restaurant;
use app\module\admin\module\currency\models\Currency;
use app\module\admin\module\product\models\Category;
use app\module\admin\module\product\models\Product;
use dosamigos\ckeditor\CKEditor;
use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $category app\module\admin\module\product\models\Category | \app\components\ImageBehavior */
/* @var $descriptions array */
/* @var $error string */
/* @var $seoUrls array */
/* @var $languages array */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchModel app\module\admin\module\product\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $topProduct Product*/
/* @var $operation_id int*/

$addTopProductUrl = Url::to('/admin/product/category/add-top-product');
$op_id = $operation_id?:0;
$deleteTopProductUrl = Url::to('/admin/product/category/delete-top-product?operation_id='. $op_id . '&category_id=' . $category->category_id . '&product_id=');
?>

<div class="category-form box box-primary">
   <?php
      if(isset($error) && !empty($error)) {
          echo "<h3 style='color: red'>{$error}</h3>";
      }
      ?>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
   <div class="box-body table-responsive">

       <input type="hidden" name="operation_id" value="<?=$operation_id?>">

      <!-- Nav tabs -->
      <ul class="nav nav-tabs" role="tablist">
         <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Основное</a>
         </li>
         <li role="presentation"><a href="#data" aria-controls="data" role="tab" data-toggle="tab">Данные</a></li>
         <li role="presentation"><a href="#seo" aria-controls="messages" role="tab" data-toggle="tab">SEO</a></li>
          <li role="presentation"><a href="#topProducts" aria-controls="data" role="tab" data-toggle="tab">Топ товары</a></li>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content">
         <div role="tabpanel" class="tab-pane active" id="main">
            <div class="box-body">
                <?php if (!empty($languages)): ?>
                   <ul class="nav nav-tabs" id="language">
                       <?php foreach ($languages as $language): ?>
                          <li role="presentation" <?= $language['language_id'] == 1 ? ' class="active"' : ''?>><a href="#language<?= $language['language_id'] ?>"
                                                     data-toggle="tab"><img
                                   src="<?= Language::getImageUrl($language['image'], 16, 16) ?>"
                                   title="<?= $language['name'] ?>"/> <?= $language['name'] ?></a></li>
                       <?php endforeach; ?>
                   </ul>
                   <div class="tab-content">
                       <?php foreach ($descriptions as $key => $description): ?>
                          <div role="tabpanel" class="tab-pane <?= $key == 1 ? ' active' : ''?>" id="language<?= $key ?>">
                             <div class="box-body">
                                 <?= $form->field($description, 'name')->textInput([
                                     'id' => 'category-description-name-' . $key,
                                     'name' => 'CategoryDescription[' . $key . '][name]',
                                 ]) ?>

                                 <?= $form->field($description, 'description')->widget(CKEditor::class, [
                                     'options' => [
                                         'id' => 'category-description-description-' . $key,
                                         'name' => 'CategoryDescription[' . $key . '][description]',
                                         'rows' => 6,
                                     ],
                                     'clientOptions' => [
                                         'allowedContent' => true,
                                         'fillEmptyBlocks' => false,
                                         'autoParagraph' => false,
                                         'extraPlugins' => 'uploadimage',
                                         'filebrowserUploadUrl' => Url::to(['/admin/default/upload'])
                                     ],
                                     'preset' => 'custom',
                                 ]) ?>

                                 <?= $form->field($description, 'meta_title')->textInput([
                                     'id' => 'category-description-meta_title-' . $key,
                                     'name' => 'CategoryDescription[' . $key . '][meta_title]',
                                 ]) ?>

                                 <?= $form->field($description, 'meta_description')->textInput([
                                     'id' => 'category-description-meta_description-' . $key,
                                     'name' => 'CategoryDescription[' . $key . '][meta_description]'
                                 ]) ?>

                                 <?= $form->field($description, 'meta_keyword')->textInput([
                                     'id' => 'category-description-meta_keyword-' . $key,
                                     'name' => 'CategoryDescription[' . $key . '][meta_keyword]',
                                 ]) ?>
                             </div>
                          </div>
                       <?php endforeach ?>
                   </div>
                <?php else: ?>
                   <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более
                      языков!</p>
                <?php endif; ?>
            </div>
         </div>
         <div role="tabpanel" class="tab-pane" id="data">
            <div class="box-body">

                    <?= $form->field($category, 'restaurant_id')->widget(Select2::class, [
                        'data' => Restaurant::getListWithOnlineDelivery(),
                        'language' => 'ru',
                        'options' => ['placeholder' => 'Выберите ресторан ...'],
                        'pluginOptions' => [
                            'allowClear' => false
                        ],
                    ]); ?>

                    <?= $form->field($category, 'parent_id')->widget(Select2::class, [
                        'data' => Category::getList(!empty($category->category_id) ? $category->category_id : null),
                        'language' => 'ru',
                        'options' => ['placeholder' => 'Выберите родительскую категорию ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]); ?>

                <?= $form->field($category, 'top')->checkbox() ?>

                <?= $form->field($category, 'contains_ingredients')->checkbox() ?>

                <?= $form->field($category, 'imageFile')->widget(FileInput::class, [
                    'options' => [
                        'accept' => 'image/*',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'showRemove' => false,
                        'showUpload' => false,
                        'showCaption' => false,
                        'initialPreview' => [
                            $category->resizeImage($category->image, 320, 320)
                        ],
                        'initialPreviewAsData' => true,
                        'overwriteInitial' => true,
                    ]
                ]); ?>


                <?= $form->field($category, 'packing_price')->textInput(['maxlength' => true]) ?>
               <div class="form-group field-category-top required">
                  <label>
                     <input type="checkbox" id="category-top" name="apply_packaging_price_to_products" value="1"  aria-invalid="false"> Применить стоимость упаковки ко всем товарам категории
                  </label>
                  <div class="help-block"></div>
               </div>


                <?= $form->field($category, 'status')->dropDownList(Category::getStatusesList()) ?>

                <?= $form->field($category, 'sort_order')->textInput() ?>

            </div>
         </div>
         <div role="tabpanel" class="tab-pane" id="seo">
            <div class="box-body">

               <div class="alert alert-info seo-url-alert"><i class="fa fa-info-circle"></i> SEO URL должен быть
                  уникальным на всю систему и не содержать пробелов.
               </div>

                <?php if (!empty($languages)): ?>
                   <ul class="nav nav-tabs" id="language-seo">
                       <?php foreach ($languages as $language): ?>
                          <li role="presentation"><a href="#language-seo<?= $language['language_id'] ?>"
                                                     data-toggle="tab"><img
                                   src="<?= Language::getImageUrl($language['image'], 16, 16) ?>"
                                   title="<?= $language['name'] ?>"/> <?= $language['name'] ?></a></li>
                       <?php endforeach; ?>
                   </ul>
                   <div class="tab-content">
                       <?php foreach ($seoUrls as $key => $seoUrl): ?>
                          <div role="tabpanel" class="tab-pane active" id="language-seo<?= $key ?>">
                             <div class="box-body">
                                 <?= $form->field($seoUrl, 'keyword')->textInput([
                                     'id' => 'seourl-keyword-' . $key,
                                     'name' => 'SeoUrl[' . $key . '][keyword]',
                                 ]) ?>
                             </div>
                          </div>
                       <?php endforeach ?>
                   </div>
                <?php else: ?>
                   <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более
                      языков!</p>
                <?php endif; ?>
            </div>
         </div>
          <div role="tabpanel" class="tab-pane" id="topProducts">
              <?php Pjax::begin(['id' => "top-product-pjax-container-op_id-$op_id"]) ?>
              <?= GridView::widget([
                  'dataProvider' => $dataProvider,
                  'filterModel' => $searchModel,
                  'layout' => "{items}\n{summary}\n{pager}",
                  'columns' => [
                      ['class' => 'yii\grid\SerialColumn'],

                      [
                          'attribute' => 'imageFile',
                          'format' => 'image',
                          'content' =>  function($data) {
                              /** @var Product $data */
                              $url = $data->resizeImage($data->image, 40, 40);
                              return Html::img($url, ['class' => 'img-thumbnail', 'style' => 'width: 50px;']);
                          },
                          'contentOptions' => ['class' => 'text-center'],
                      ],
                      [
                          'attribute' => 'productName',
                          'format' => 'raw',
                          'value' => static function ($model) {
                              /** @var Product $model */
                              if ($model->name) {
                                  $label = $model->name;
                                  $url =  "<a target=\"_blank\" href=\"" . Yii::$app->params['webUrl'] . $model->product_id . "\">$label</a> ";
                                  return $url;
                              }
                              return '';
                          }
                      ],
                      'weight', [
                          'attribute' => 'price',
                          'value' => static function ($model) {
                              return  Currency::format($model->price, 'UAH');
                          }
                      ],
                      [
                          'attribute' => 'categoryName',
                          'filter' => Category::getList(),
                          'value' => static function ($model) {
                              /** @var Product $model */
                              return  $model->productCategory ? $model->productCategory->category->name : '';
                          }
                      ],
                      [
                          'attribute' => 'status',
                          'filter' => Product::getStatusesList(),
                          'value' => static function ($model) {
                              return  Product::getStatusName($model->status);
                          }
                      ],
                      'sort_order',
                      [
                          'attribute' => 'created_at',
                          'format' => ['datetime', 'php:d.m.Y H:i'],
                          'filter' => false,
                      ], [
                          'attribute' => 'updated_at',
                          'format' => ['datetime', 'php:d.m.Y H:i'],
                          'filter' => false,
                      ],

                      [
                          'class' => 'yii\grid\ActionColumn',
                          'template' => '{delete}',
                          'buttons' => [
                              'delete' => function ($url) use ($op_id) {
                                  return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
                                      'title' => Yii::t('yii', 'Удалить'),
                                      'aria-label' => Yii::t('yii', 'Удалить'),
                                      'onclick' => "
                                            if (confirm('Вы уверены, что хотите удалить этот элемент?')) {
                                                $.ajax('$url', {
                                                    type: 'POST'
                                                }).done(function(data) {
                                                    $.pjax.reload({container: '#top-product-pjax-container-op_id-$op_id'});
                                                });
                                            }
                                            return false;
                                        ",
                                  ]);
                              },
                          ],
                          'urlCreator' => function ($action, $model, $key, $index) use ($deleteTopProductUrl){
                              if ($action === 'delete') {
                                  return $deleteTopProductUrl . $model->product_id;
                              }

                          }
                      ],
                  ],
              ]); ?>
              <?php Pjax::end() ?>
              <fieldset>
                  <legend>Добавить в историю</legend>
              </fieldset>

              <?= $form->field($topProduct, 'product_id')->dropDownList(Product::getList()) ?>

              <div class="text-right">
                  <?= Html::button('<i class="fa fa-plus-circle"></i> Добавить', ['class' => 'btn btn-primary btn-add-top-product']) ?>
              </div>
          </div>
      </div>
   </div>
   <div class="box-footer">
       <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-flat']) ?>
   </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$var = empty($category->category_id ) ? 0 : $category->category_id;
$this->registerJs(
    "
    $('#language a:first').tab('show');
    $('#language-seo a:first').tab('show');
    
    let loading = false;
    $('.btn-add-top-product').on('click', function() {
        if (loading) return;
        
        let productId = $('select[name=\'Product[product_id]\']').val();
    
        loading = true;
        $.ajax('$addTopProductUrl', {
            type: 'POST',
            'data': {
                'category_id': $var, 
                'product_id': productId,
                'operation_id': $op_id
            }
        }).done(function(data) {
            $.pjax.reload({
                container: '#top-product-pjax-container-op_id-$op_id'
            });
            $('select[name=\'Product[product_id]\']').val(0);
            loading = false;
        });
    });
    ",
    View::POS_READY,
    'script'
);
?>

