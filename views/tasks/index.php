<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use TaskForce\utils\NounPluralConverter;
use yii\widgets\LinkPager;
?>

<div class="left-column">
    <h3 class="head-main head-task">Новые задания</h3>

    <?php foreach ($tasks as $task) : ?>
        <div class="task-card">
            <div class="header-task">
                <a href="<?= Url::to(['tasks/view', 'id' => $task->id]); ?>" class="link link--block link--big"><?= Html::encode($task->name); ?></a>
                <p class="price price--task"><?= Html::encode($task->budget); ?> <?= Html::encode(isset($task->budget)) ? '₽' : ''; ?> </p>
            </div>
            <p class="info-text"><span class="current-time"><?= NounPluralConverter::getTaskRelativeTime($task->dt_add); ?></span></p>
            <p class="task-text"><?= Html::encode($task->description); ?></p>
            <div class="footer-task">
                <p class="info-text town-text"><?= Html::encode($task->address); ?></p>
                <p class="info-text category-text"><?= Html::encode($task->category->name); ?></p>
                <a href="<?= Url::to(['tasks/view', 'id' => $task->id]); ?>" class="button button--black">Смотреть Задание</a>
            </div>
        </div>
    <?php endforeach; ?>


    <div class="pagination-wrapper">
        <ul class="pagination-list">
            <li class="pagination-item mark">
                <a href="#" class="link link--page"></a>
            </li>
            <li class="pagination-item">
                <a href="#" class="link link--page">1</a>
            </li>
            <li class="pagination-item pagination-item--active">
                <a href="#" class="link link--page">2</a>
            </li>
            <li class="pagination-item">
                <a href="#" class="link link--page">3</a>
            </li>
            <li class="pagination-item mark">
                <a href="#" class="link link--page"></a>
            </li>
        </ul>
        <?= LinkPager::widget([
            'pagination' => $pages,

            // Настройки контейнера пагинации
            'options' => [
                'tag' => 'ul',
                'class' => 'pagination-list',
                // 'id' => 'pager-container',
            ],

            'linkContainerOptions' => ['class' => 'pagination-item'],

            'linkOptions' => ['class' => 'link link--page'],
            'activePageCssClass' => 'pagination-item--active',
            // 'disabledPageCssClass' => 'mydisable',

            // Настройки для навигационных ссылок

            'prevPageCssClass' => 'pagination-item mark',
            'nextPageCssClass' => 'pagination-item mark',
            // 'firstPageCssClass' => 'link link--page',
            // 'lastPageCssClass' => 'link link--page',


            // 'activePageCssClass' => 'pagination-item pagination-item--active',
            // 'nextPageCssClass' => 'pagination-item mark',
            // 'prevPageCssClass' => 'pagination-item mark',

            // 'hideOnSinglePage' => false,
            'prevPageLabel' => '',
            'nextPageLabel' => '',

            // Настройки контейнера пагинации
            // 'options' => [
            //     'tag' => 'ul',
            //     'class' => 'pagination-list',
            // ],

            // // Настройки классов css для ссылок
            // 'linkOptions' => ['class' => 'link link--page'],
            // // 'activePageCssClass' => 'myactive',
            // // 'disabledPageCssClass' => 'mydisable',

            // // Настройки для навигационных ссылок
            // 'prevPageCssClass' => 'pagination-item mark',
            // 'nextPageCssClass' => 'pagination-item mark',
            // 'firstPageCssClass' => 'link link--page',
            // 'lastPageCssClass' => 'link link--page',
        ]); ?>

        <!-- $this->widget('CLinkPager', array(
        'internalPageCssClass' => '',
        'pages' => $paginator, //$paginator определен в контроллере
        'id' => '',
        'header' => '',
        'selectedPageCssClass' => 'active',
        'hiddenPageCssClass' => 'disabled',
        'nextPageLabel' => '&raquo;', // »
        'prevPageLabel' => '&laquo;', // «
        'lastPageLabel' => '&raquo;&raquo;', // »»
        'firstPageLabel' => '&laquo;&laquo;', // ««
        'htmlOptions' => array('class' => 'pagination'),
        )); -->

    </div>


</div>
<div class="right-column">
    <div class="right-card black">
        <div class="search-form">

            <?php $form = ActiveForm::begin([
                'id' => 'tasks-form',
                'fieldConfig' => [
                    'template' => "{input}"
                ]
            ]); ?>

            <h4 class="head-card">Категории</h4>
            <div class="form-group">
                <div>
                    <?= $form->field($model, 'categories[]')->checkboxList(
                        ArrayHelper::map($categories, 'id', 'name'),
                        [
                            'separator' => '<br>',
                            'item' => function ($index, $label, $name, $checked, $value) use ($model) {
                                settype($model->categories, 'array');
                                $checked = in_array($value, $model->categories) ? ' checked' : '';
                                $input = "<input type=\"checkbox\" name=\"{$name}\" id=\"{$value}\" value=\"{$value}\"{$checked}>";
                                $label = "<label class=\"control-label\" for=\"{$value}\">{$label}</label>";
                                return "{$input}{$label}";
                            }
                        ]
                    ); ?>
                </div>
            </div>

            <h4 class="head-card">Дополнительно</h4>
            <?= $form
                ->field($model, 'without_executor', ['template' => "{input}\n{label}"])
                ->checkbox(['id' => 'without-performer'], false); ?>

            <h4 class="head-card">Период</h4>
            <?= $form->field($model, 'period')->dropDownList($period_values, ['id' => 'period-value']); ?>
            <?= Html::submitButton('Искать', ['class' => 'button button--blue']); ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>