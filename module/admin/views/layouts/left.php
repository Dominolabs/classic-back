<?php
/**
 * Left aside menu view.
 */

use app\module\admin\AdminModule;

/* @var $this yii\web\View */

?>
<aside class="main-sidebar">
    <section class="sidebar">
        <?= dmstr\widgets\Menu::widget([
            'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
            'items' => AdminModule::getMenuItems(),
        ]) ?>
    </section>
</aside>
