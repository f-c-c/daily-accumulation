<?php

namespace app\controllers;

use yii\web\Controller;
use yii\data\Pagination;
use app\models\Books;


class BooksController extends Controller
{
    public $enableCsrfValidation=false;
    //Index 方法，查询出图书列表
    public function actionIndex() {
        $query = Books::find();
        $pagination = new Pagination([
            'defaultPageSize' => 5,
            'totalCount' => $query->count(),
        ]);
        
        $books = $query->orderBy('id')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('index', [
            'books' => $books,
            'pagination' => $pagination,
            'addBooksUrl' => "?r=books/addpage",
            "title" => "图书列表"
        ]);
    }
    //Addpage 方法，加载新增页面
    public function actionAddpage() {
        return $this->render('add', [
            
        ]);
    }
    // add 方法，接受post数据，完成数据库的 insert
    public function actionAdd() {
        //后台数据验证，暂时不做了。。。。。。。
        $request = \Yii::$app->request; 
  
        $addbooks=new Books();
        $addbooks->bookid = $request->post('bookid');
        $addbooks->bookname = $request->post('bookname');
        $addbooks->booktype = $request->post('booktype');
        $addbooks->bookauthor = $request->post('bookauthor');
        $addbooks->bookupdate = $request->post('bookupdate');
        $addbooks->bookpages = $request->post('bookpages');
        $addbooks->bookindate = $request->post('bookindate');
        $addbooks->isborrowed = $request->post('isborrowed');
        $addbooks->bookinfo = $request->post('bookinfo');
        if($addbooks->insert()){
          // $info = "添加成功";
          $this->redirect(array('/books/index'));   // 添加成功后跳转到 list 页面
        }else{
          //处理添加失败的情况，跳转到error页面
        }
  
      }
    //delete 方法，接收get请求的id 删除一本图书
    public function actionDelete()  {
        $request = \Yii::$app->request; 
        $id = $request->get('id');
        if (Books::findOne($id)->delete()) {
            $this->redirect(array('/books/index'));   // 删除成功后跳转到 list 页面
        }
    }
}