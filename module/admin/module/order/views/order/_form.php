<?php

use app\module\admin\models\Restaurant;
use app\module\admin\module\order\models\OrderHistory;
use app\module\admin\module\order\models\OrderProduct;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use app\module\admin\module\currency\models\Currency;
use app\module\admin\module\order\models\Order;
use app\module\admin\module\pizzeria\models\Pizzeria;
use kartik\datetime\DateTimePicker;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use kartik\rating\StarRating;

/* @var $this yii\web\View */
/* @var $pizzaCatId integer */
/* @var $model app\module\admin\module\order\models\Order */
/* @var $form yii\widgets\ActiveForm */
/* @var $orderProductModel app\module\admin\module\order\models\OrderProduct */
/* @var $orderProductDataProvider yii\data\ActiveDataProvider */
/* @var $orderHistoryModel app\module\admin\module\order\models\OrderHistory */
/* @var $orderHistoryDataProvider yii\data\ActiveDataProvider */
/* @var $lastOrderHistory app\module\admin\module\order\models\OrderHistory */

$userDataUrl = Url::to(['user-data']);
$getBalanceUrl = Url::to(['get-balance']);
$orderHistoryAddUrl = Url::to('/admin/order/order-history/create');
$orderHistoryRemoveUrl = Url::to('/admin/order/order-history/delete');
$orderProductAddUrl = Url::to('/admin/order/order-product/create');
$orderProductRemoveUrl = Url::to('/admin/order/order-product/delete');
$pizzaCatId = Yii::$app->params['pizzaCategoryId'] ?? 0;
$weigh_dishes_extra_ingredients_value = 0;
$orderOnlinePaymentType = Order::PAYMENT_TYPE_ONLINE;
$promotions_applied = [];
if(!empty($model->promotions_applied)){
   $data = json_decode($model->promotions_applied);
   foreach($data as $action){
      $promotions_applied[] = $action->name . ' (' . $action->discount_size . ')';
   };
}
$promotions_applied_remark = !empty($promotions_applied) ? implode(', ', $promotions_applied) : '';
?>
<div class="order-form box box-primary">
    <?php $form = ActiveForm::begin(); ?>
   <div class="box-body table-responsive">

      <div class="panel panel-default">
         <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-user"></i> Клиент</h3>
         </div>

         <div class="panel-content">

             <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

             <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

             <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

             <?= $form->field($model, 'city_id')->textInput(['maxlength' => true, 'value' => $model->getCityName(), 'disabled' => 'disabled']) ?>

             <?= $form->field($model, 'street')->textInput(['maxlength' => true]) ?>

             <?= $form->field($model, 'entrance')->textInput(['maxlength' => true]) ?>

             <?= $form->field($model, 'house_number')->textInput(['maxlength' => true]) ?>

             <?= $form->field($model, 'apartment_number')->textInput(['maxlength' => true]) ?>

         </div>

      </div>

       <?= $form->field($model, 'payment_type')->dropDownList(Order::getPaymentTypesList()) ?>

       <?= $form->field($model, 'delivery_type')->dropDownList(Order::getDeliveryTypesList()) ?>

       <?php if (isset($model->delivery_type) && isset($model->restaurant_id)): ?>
           <?= $form->field($model, 'restaurant_id')->textInput(['maxlength' => true, 'value' => $model->restaurant->restaurantTitleWithAddress, 'disabled' => 'disabled']) ?>
       <?php endif; ?>

       <?php if (!$model->isNewRecord && $model->payment_type === Order::PAYMENT_TYPE_ONLINE): ?>

           <?= $form->field($model, 'payment_status')->dropDownList(Order::getPaymentStatusesList(), ['disabled' => 'disabled']) ?>

       <?php endif; ?>

       <?= $form->field($model, 'time')->widget(DateTimePicker::class, [
           'type' => DateTimePicker::TYPE_INPUT,
           'options' => [
               'value' => Yii::$app->formatter->asDate($model->time, 'php:d.m.Y H:i'),
               'autocomplete' => 'off'
           ],
           'pluginOptions' => [
               'autoclose' => true,
               'format' => 'dd.mm.yyyy hh:ii'
           ]
       ]) ?>

       <?= $form->field($model, 'rating')->widget(StarRating::class, [
           'name' => 'rating',
           'value' => $model->rating,
           'pluginOptions' => [
               'readonly' => true,
               'showClear' => false,
               'showCaption' => false,
           ],
       ]) ?>

       <?= $form->field($model, 'comment')->textarea(['rows' => 3]) ?>

       <?= $form->field($model, 'call_me_back')->checkbox() ?>

      <div class="panel panel-default">
         <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-info-circle"></i> Детали заказа</h3>
         </div>

         <div class="panel-content">
             <?php Pjax::begin(['id' => 'order-product-pjax-container']) ?>
             <?= GridView::widget([
                 'dataProvider' => $orderProductDataProvider,
                 'layout' => "{items}\n<div class='text-center'>{pager}</div>",
                 'showFooter' => true,
                 'footerRowOptions' => [
                     'class' => 'order-details-grid-footer'
                 ],
                 'columns' => [
                     ['class' => SerialColumn::class],

                     [
                         'attribute' => 'name',
                         'enableSorting' => false,
                         'format' => 'raw',
                         'value' => static function (OrderProduct $model) use ($pizzaCatId) {
                             $ingredients = json_decode($model->ingredients, true);

                             if (!empty($ingredients['main_ingredients'])) {
                                 $mainIngredients = $ingredients['main_ingredients'];
                             }

                             if (!empty($ingredients['additional_ingredients'])) {
                                 $additionalIngredients = $ingredients['additional_ingredients'];
                             }

                             if (!empty($model->properties) && !empty($arr = json_decode($model->properties, true))) {
                                 $s = '(' . ($arr[0]['property']['uk'] ?? ($arr['property']['uk'] ?? '')) . ' соус) ';
                             } else $s = '';


//                             \yii\helpers\VarDumper::dump($model);
//                             exit;
                             if ($model->type === 'classic') {
                                 $result = Html::a($model->name, Url::to('/admin/product/classic')) . " $s" . ($model->category_id == (int)Yii::$app->params['noodlesCategoryId'] ? '' : $model->getProductTypeString()) . ' / ' . Currency::format($model->price, 'UAH', 1);
                             } else {
                                 $result = Html::a($model->name, Url::to('/admin/product/product/update?id=' . $model->product_id)) . " $s" . ($model->category_id == (int)Yii::$app->params['noodlesCategoryId'] ? '' : $model->getProductTypeString()) . ' / ' . Currency::format($model->price, 'UAH', 1);
                             }

                             if (!empty($mainIngredients) || !empty($additionalIngredients)) {
                                 $result .= '<br/></br>';
                                 if (!empty($mainIngredients)) {
                                     $result .= $model->type === 'classic' ? '<p>Основные ингредиенты:</p>' : '<p>Дополнительные ингредиенты:</p>';
                                     $result .= '<ul>';
                                     foreach ($mainIngredients as $mainIngredient) {
                                         if ($model->type === 'classic') {
                                             $price = 0.0000;
                                         } else {
                                             $price = $mainIngredient['price'];
                                         }
                                         $result .= '<li>' . $mainIngredient['name'] . ' / ' . Currency::format($price, 'UAH', 1) . ' / ' . ' x ' . $mainIngredient['quantity'] . '</li>';
                                     }
                                     $result .= '</ul>';
                                 }
                                 if (!empty($additionalIngredients)) {
                                     $result .= '<p>Дополнительные ингредиенты:</p>';
                                     $result .= '<ul>';
                                     foreach ($additionalIngredients as $additionalIngredient) {
                                         $price = $additionalIngredient['price'];
                                         $result .= '<li>' . $additionalIngredient['name'] . ' / ' . Currency::format($price, 'UAH', 1) . ' / ' . ' x ' . $additionalIngredient['quantity'] . '</li>';
                                     }
                                     $result .= '</ul>';
                                 }
                             }

                             if (!empty($model->properties) && $arr = json_decode($model->properties, true)) {
                                 $result .= '<p>Соус:</p>';
                                 $result .= '<ul>';
                                 $result .= "<li>" . $arr['property']['uk'] ?? '' . "</li>";
                                 $result .= '</ul>';
                             }

                             if (!empty($model->comment)) {
                                 $result .= '<br><br><p style="font-size:12px; margin: 0; padding: 0"><i><b>Комментарий к товару</b></i></p>';
                                 $result .= "<span style=\"font-size:12px\">$model->comment</span>";
                             }

                             return $result;
                         }
                     ], [
                         'attribute' => 'price',
                         'enableSorting' => false,
                         'format' => 'raw',
                         'value' => function ($model) {
                             $price = Currency::format($model->price, 'UAH');
                             $color = $model->isWeighDish() ? 'red' : 'black';
                             return "<span style=\"color: {$color}\">{$price}</span>";
                         },
                     ], [
                         'attribute' => 'quantity',
                         'enableSorting' => false,
                         'format' => 'raw',
                         'value' => function ($model) {
                             $color = $model->isWeighDish() ? 'red' : 'black';
                             return "<span style=\"color: {$color}\">{$model->quantity}</span>";
                         },
                         'footer' => '<strong>Итого</strong>',
                     ], [
                         'attribute' => 'total',
                         'enableSorting' => false,
                         'format' => 'raw',
                         'value' => function ($model) use ($pizzaCatId, &$weigh_dishes_extra_ingredients_value) {
                             $total = Currency::format($model->total, 'UAH');
                             $price = Currency::format($model->price * $model->quantity, 'UAH');


                             $ingredients = json_decode($model->ingredients, true);
                             $non_pizza_extra_ingredients_value = 0;

                             if (!empty($ingredients['main_ingredients'])) {
                                 $mainIngredients = $ingredients['main_ingredients'];
                                 foreach ($mainIngredients as $ingredient) {
                                     if ($model->category_id !== (int)$pizzaCatId) {
                                         $non_pizza_extra_ingredients_value += $ingredient['price'] * $ingredient['quantity'];
                                         if (isset($model->weight_dish) && $model->weight_dish === OrderProduct::YES) {
                                             $weigh_dishes_extra_ingredients_value += $ingredient['price'] * $ingredient['quantity'];
                                         }
                                     }
                                 }
                             }

                             if (!empty($ingredients['additional_ingredients'])) {
                                 $additionalIngredients = $ingredients['additional_ingredients'];
                                 foreach ($additionalIngredients as $ingredient) {
                                     if ($model->category_id !== (int)$pizzaCatId) {
                                         $non_pizza_extra_ingredients_value += $ingredient['price'] * $ingredient['quantity'];
                                         if (isset($model->weight_dish) && $model->weight_dish === OrderProduct::YES) {
                                             $weigh_dishes_extra_ingredients_value += $ingredient['price'] * $ingredient['quantity'];
                                         }
                                     }
                                 }
                             }
                             $color = $model->isWeighDish() ? 'red' : 'black';

                             if ($non_pizza_extra_ingredients_value > 0) {
                                 $ingredients_val = Currency::format($non_pizza_extra_ingredients_value, 'UAH');
                                 $result = "<span style=\"color: {$color}\">{$price}</span>";
                                 $result .= "<br /><span style=\"color: {$color}\">+</span><br />";
                                 $result .= "<span style=\"color: {$color}\">{$ingredients_val}</span>";
                             } else {
                                 $result = "<span style=\"color: {$color}\">{$total}</span>";
                             }

                             return $result;
                         },
                         'footer' => '<strong>' . Currency::format($model->total, 'UAH') . '</strong>',
                     ],
                 ],
             ]) ?>
             <?php Pjax::end() ?>
            <?php if(!empty($model->promotions_applied)): ?>
               <div style="font-style: italic">
                  <p><span style="color:red;font-size: 1.5rem">*</span> <span style="font-weight:bold">Применены следующие скидки:</span> <?php echo $promotions_applied_remark ?></p>
               </div>
            <?php endif; ?>
         </div>
      </div>

       <?php if (!$model->isNewRecord): ?>

          <div class="panel panel-default">
             <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-comment-o"></i> История заказа</h3>
             </div>

             <div class="panel-content">
                 <?php Pjax::begin(['id' => 'order-history-pjax-container']) ?>
                 <?= GridView::widget([
                     'dataProvider' => $orderHistoryDataProvider,
                     'layout' => "{items}\n<div class='text-right'>{summary}</div>\n<div class='text-center'>{pager}</div>",
                     'columns' => [
                         ['class' => SerialColumn::class],
//                         [
//                             'attribute' => 'pizzeria_id',
//                             'enableSorting' => false,
//                             'value' => static function ($orderHistoryModel) {
//                                 /** @var OrderHistory $orderHistoryModel */
//                                 return $orderHistoryModel->getPizzeriaName();
//                             },
//                         ],
                         [
                             'attribute' => 'restaurant_id',
                             'enableSorting' => false,
                             'value' => static function ($orderHistoryModel) {
                                 /** @var OrderHistory $orderHistoryModel */
                                 return $orderHistoryModel->getRestaurantName();
                             },
                         ],
                         [
                             'attribute' => 'status',
                             'enableSorting' => false,
                             'value' => static function ($orderHistoryModel) {
                                 return Order::getStatusName($orderHistoryModel->status);
                             }
                         ],
                         [
                             'attribute' => 'comment',
                             'format' => 'ntext',
                             'enableSorting' => false,
                         ], [
                             'attribute' => 'created_at',
                             'format' => ['datetime', 'php:d.m.Y H:i'],
                             'enableSorting' => false,
                         ],

                         [
                             'class' => ActionColumn::class,
                             'template' => '{delete}',
                             'buttons' => [
                                 'delete' => static function ($url) {
                                     return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
                                         'title' => Yii::t('yii', 'Удалить'),
                                         'aria-label' => Yii::t('yii', 'Удалить'),
                                         'onclick' => "
                                            if (confirm('Вы уверены, что хотите удалить этот элемент?')) {
                                                $.ajax('$url', {
                                                    type: 'POST'
                                                }).done(function(data) {
                                                    $.pjax.reload({container: '#order-history-pjax-container'});
                                                });
                                            }
                                            return false;
                                        ",
                                     ]);
                                 },
                             ],
                             'urlCreator' => static function ($action, $model, $key, $index) {
                                 if ($action === 'delete') {
                                     return Url::to('/admin/order/order-history/delete?id=') . $model->order_history_id;
                                 }
                             }
                         ],
                     ],
                 ]); ?>
                 <?php Pjax::end() ?>

                <fieldset>
                   <legend>Добавить в историю</legend>
                </fieldset>

                 <?php
                 $drop_down_data = $model->delivery_type === Order::DELIVERY_TYPE_ADDRESS
                     ? Restaurant::getAvailableForAddressDelivery()
                     : Restaurant::getAvailableForSelfPicking();

                 if ($model->restaurant && stripos($model->restaurant->restaurantTitleWithAddress, 'classic') === false) {
                     $drop_down_data = array_filter($drop_down_data, function ($value) {
                         return is_string($value) && stripos($value, 'classic') === false;
                     });
                 } else {
                     $drop_down_data = array_filter($drop_down_data, function ($value) {
                         return is_string($value) && stripos($value, 'classic') !== false;
                     });
                 }
                 ?>

                 <?php if (isset($model->delivery_type) && $model->delivery_type === Order::DELIVERY_TYPE_ADDRESS): ?>
                     <?= $form->field($orderHistoryModel, 'restaurant_id')->dropDownList($drop_down_data,
                         ['options' => !empty($lastOrderHistory) ? [$lastOrderHistory->restaurant_id => ['selected' => true]] : []]) ?>
                 <?php else: ?>
                     <?= $form->field($orderHistoryModel, 'restaurant_id')->dropDownList($drop_down_data,
                         ['options' => [$model->restaurant_id => ['selected' => true]],
                             'disabled' => $model->restaurant_id ? true : false]) ?>
                 <?php endif; ?>

                 <?= $form->field($orderHistoryModel, 'status')->dropDownList(Order::getStatusesList(),
                     ['options' => !empty($lastOrderHistory) ? [$lastOrderHistory->status => ['selected' => true]] : []]) ?>

                 <?= $form->field($orderHistoryModel, 'comment')->textarea(['rows' => 3]) ?>

                <div class="text-right">
                    <?= Html::button('<i class="fa fa-plus-circle"></i> Добавить', ['class' => 'btn btn-primary btn-add-order-history']) ?>
                </div>

             </div>

          </div>

       <?php endif; ?>

   </div>
   <div class="box-footer">
       <?= Html::a('Распечатать', 'print?id=' . $model->order_id, ['class' => 'btn btn-primary btn-flat']) ?>
       <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-flat']) ?>
   </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(
    "
    var currencyText = ' грн';
    
    function updateBalance() {
        $.ajax({
            dataType: 'json',
            url: '$getBalanceUrl',
            'dataType': 'json',
            'data': {order_id: $model->order_id}
        }).then(function (data) {
            console.log(data)
            if(data.has_weight_dishes){
               let weightDishesPacking = data.weight_dishes_packaging_price;
               let totalPacking = parseFloat(data.packing);
               let usualDishesPacking = totalPacking - weightDishesPacking;
               
               let weightDishesProdPrice = data.weight_dishes_products_price + $weigh_dishes_extra_ingredients_value;
               let totalProdsPrice = parseFloat(data.sum);
               let usualDishesProdsPrice = totalProdsPrice - weightDishesProdPrice;               
               
               $('.order-details-grid-footer').before('<tr style=\'background: whitesmoke\'>&nbsp;<td colspan=\'5\'></td></tr>'); 
               $('.order-details-grid-footer').before('<tr style=\'color: red; text-align: center; font-size: 15px\'><td colspan=\'5\'><span style=\'font-weight: bold\'>Внимание!</span> В заказе присутствуют весовые блюда (подсвечено красным), стоимость которых, а также их доставки и упаковка оплачиваются клиентом наличными по факту доставки!</td></tr>');
               $('.order-details-grid-footer').before('<tr style=\'background: whitesmoke\'>&nbsp;<td colspan=\'5\'></td></tr>'); 
               $('.order-details-grid-footer').before('<tr style=\'font-style: italic; font-weight: bold; font-size: 13px\'><td></td><td></td><td>Весовые блюда</td><td>Обычные блюда</td><td>Вместе</td></tr>');               
               $('.order-details-grid-footer').before('<tr><td>&nbsp;</td><td style=\'text-align: right\'>Упаковка</td><td>' +  weightDishesPacking + ' грн' + '</td><td>' +  usualDishesPacking + ' грн' + '</td><td>' +  totalPacking + ' грн' + '</td></tr>');
               $('.order-details-grid-footer').before('<tr><td>&nbsp;</td><td style=\'text-align: right\'>Товар</td><td>' +  weightDishesProdPrice + ' грн' + '</td><td>' +  usualDishesProdsPrice + ' грн' + '</td><td>' +  totalProdsPrice + ' грн' + '</td></tr>');
               if(data.cityName){
                  $('.order-details-grid-footer').before('<tr><td>&nbsp;</td><td style=\'text-align: right\'>Доставка (' + data.cityName + ')</td><td>-</td><td>-</td><td>' + data.delivery + '</td></tr>');
               } else {
                  $('.order-details-grid-footer').before('<tr><td>&nbsp;</td><td style=\'text-align: right\'>Доставка</td><td>-</td><td>-</td><td>' + data.delivery + '</td></tr>');
               }
               $('.order-details-grid-footer').before('<tr style=\'background: whitesmoke\'>&nbsp;<td colspan=\'5\'></td></tr>');
               if(data.promotions_applied){
                  $('.order-details-grid-footer').before('<tr style=\'color:red\'><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Скидки по акциях *</td><td>- ' + getAppliedDiscountsSum(data.applied_promotions) + ' грн</td></tr>');
               }
               $('.order-details-grid-footer').before('<tr style=\'background: whitesmoke\'>&nbsp;<td colspan=\'5\'></td></tr>'); 
               if(parseInt(data.payment_type_code) === $orderOnlinePaymentType){
                  $('.order-details-grid-footer').before('<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style=\'font-weight: bold\'>Оплата онлайн в размере </td><td>' + data.online_payment_sum + ' грн' + '</td></tr>');
               }
            } else {
               if (data.cityName) {
                   $('.order-details-grid-footer').before('<tr><td></td><td></td><td></td><td></td><td></td></tr>');
                   $('.order-details-grid-footer').before('<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Доставка (' + data.cityName + ')</td><td>' + data.delivery + '</td></tr>');
               } else {
                   $('.order-details-grid-footer').before('<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Доставка</td><td>' + data.delivery + '</td</tr>');
               }
               
               $('.order-details-grid-footer').before('<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Упаковка</td><td>' + data.packing + '</td></tr>');               
               $('.order-details-grid-footer').before('<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Сумма</td><td>' + data.sum + '</td></tr>');
               if(data.promotions_applied){
                  $('.order-details-grid-footer').before('<tr style=\'color:red\'><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Скидки по акциях *</td><td>- ' + getAppliedDiscountsSum(data.applied_promotions) + ' грн</td></tr>');
               }
            }  
        });
    }
    
    function getAppliedDiscountsSum(data){
       let result = 0;
       data.forEach(item => {
         result += item.discount_sum;
       })
       return roundToTwo(result).toFixed(2);
    }
    
    function roundToTwo(num) {
       return +(Math.round(num + 'e+2')  + 'e-2');
    }
        
    $( document ).ready(function() {
        updateBalance();
    });
    
    $(document).on('pjax:complete' , function(event) {
        if (event.target.id !== 'order-history-pjax-container') {
            updateBalance();
        }
    });
    let isDataLoading = false;
    
    $('.btn-add-order-history').on('click', function() {
    
        let status = $('select[name=\'OrderHistory[status]\']').val();
        let pizzeriaId = $('select[name=\'OrderHistory[restaurant_id]\']').val();
        let comment = $('textarea[name=\'OrderHistory[comment]\']').val();
    
    if (isDataLoading) return;
    isDataLoading = true;
        $.ajax('$orderHistoryAddUrl', {
            type: 'POST',
            'data': {
                'OrderHistory[order_id]': '$model->order_id', 
                'OrderHistory[restaurant_id]': pizzeriaId, 
                'OrderHistory[status]': status, 
                'OrderHistory[comment]': comment
            }
        }).done(function(data) {
            $.pjax.reload({container: '#order-history-pjax-container'});
            $('select[name=\'OrderHistory[restaurant_id]\']').val(pizzeriaId);
            $('select[name=\'OrderHistory[status]\']').val(status);
            $('textarea[name=\'OrderHistory[comment]\']').val('');
            isDataLoading = false;
        });
    });
    
    $('select[name=\'OrderProduct[product_id]\']').on('select2:select', function() {
        var data = $(this).select2('data')[0];
        var price = parseFloat(data.price).toFixed(4);
        
        var quantity =  $('input[name=\'OrderProduct[quantity]\']').val();
        
        $('input[name=\'OrderProduct[code]\']').val(data.code);
        $('input[name=\'price\']').val(price + currencyText);
        $('input[name=\'price\']').data('price', price);
        $('input[name=\'total\']').val(parseFloat(data.price * quantity).toFixed(4) + currencyText);
    });
    
    $('select[name=\'OrderProduct[product_id]\']').on('select2:unselect', function() {
        $('input[name=\'OrderProduct[code]\']').val('');
        $('input[name=\'price\']').val('');
        $('input[name=\'price\']').data('price', '');
        $('input[name=\'total\']').val('');
    });
    
    $('input[name=\'OrderProduct[quantity]\']').on('change', function() {
        var price = parseFloat($('input[name=\'price\']').data('price'));

        if (price > 0) {
            var total = parseFloat($(this).val() * price).toFixed(4) + currencyText;
            
            $('input[name=\'total\']').val(total);
        } else {
            $('input[name=\'total\']').val('');
        }
    });
    
    $('.btn-add-order-product').on('click', function() {
        var productId = $('select[name=\'OrderProduct[product_id]\']').val();
        var quantity = $('input[name=\'OrderProduct[quantity]\']').val();
    
        $.ajax('$orderProductAddUrl', {
            type: 'POST',
            'data': {
                'OrderProduct[product_id]': productId, 
                'OrderProduct[order_id]': '$model->order_id',
                'OrderProduct[quantity]': quantity
            }
        }).done(function(data) {
            $.pjax.reload({container: '#order-product-pjax-container'});
            
            $('select[name=\'OrderProduct[product_id]\']').val('').trigger('change');
            
            $('input[name=\'price\']').val('');
            $('input[name=\'total\']').val('');
        });
    });
    ",
    View::POS_READY,
    'script'
);
?>
