<?php

use app\module\admin\module\booking\models\Booking;
use app\module\admin\module\feedback\models\Feedback;
use app\module\admin\module\hotelservice\models\Hotelservice;
use app\module\admin\module\product\models\Category;
use app\module\admin\module\product\models\Product;
use app\module\admin\module\order\models\Order;
use app\module\admin\models\User;
use app\module\admin\module\room\models\Room;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?= Category::getAllCount() ?></h3>

                    <p>Категории</p>
                </div>
                <div class="icon">
                    <i class="fa fa-sitemap"></i>
                </div>
                <a href="<?= Url::to(['/admin/product/category']) ?>" class="small-box-footer">
                    Подробнее... <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?= Product::getAllCount() ?></h3>

                    <p>Товары</p>
                </div>
                <div class="icon">
                    <i class="fa fa-tag"></i>
                </div>
                <a href="<?= Url::to(['/admin/product/product']) ?>" class="small-box-footer">
                    Подробнее... <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?= Order::getAllCount() ?></h3>

                    <p>Заказы</p>
                </div>
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <a href="<?= Url::to(['/admin/order/order']) ?>" class="small-box-footer">
                    Подробнее... <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?= User::getAllCount() ?></h3>

                    <p>Пользователи</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <a href="<?= Url::to(['/admin/user']) ?>" class="small-box-footer">
                    Подробнее... <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
