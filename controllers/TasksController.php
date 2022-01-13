<?php

namespace app\controllers;

use app\models\Tasks;
use yii\web\Controller;

class TasksController extends Controller
{
    public function actionIndex()
    {
        $query = Tasks::find()
            // ->joinWith('categories')
            ->where(['status' => NULL])
            ->orderBy('dt_add DESC');

        $tasks = $query->all();
        return $this->render('index', ['tasks' => $tasks]);
    }
}
