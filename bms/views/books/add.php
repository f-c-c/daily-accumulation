<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
<?php $this->registerCssFile('css/books.css');?>
<?php $this->registerJsFile('js/addbooks.js');?>
<h1>新增图书</h1>
<div class="container-t">
  <form action="?r=books/add" method="post">
    <div class="item-row">
      <div>图书ID：</div>
      <div><input name="bookid"/></div>
    </div>
    <div class="item-row">
      <div>图书name：</div>
      <div><input name="bookname" /></div>
    </div>
    <div class="item-row">
      <div>图书类型：</div>
      <div><input name="booktype" /></div>
    </div>
    <div class="item-row">
      <div>作者：</div>
      <div><input name="bookauthor"/></div>
    </div>
    <div class="item-row">
      <div>出版日期：</div>
      <div><input name="bookupdate" /></div>
    </div>
    <div class="item-row">
      <div>页数：</div>
      <div><input name="bookpages" /></div>
    </div>
    <div class="item-row">
      <div>入库日期：</div>
      <div><input name="bookindate" /></div>
    </div>
    <div class="item-row">
      <div>是否借阅：</div>
      <div><input name="isborrowed" /></div>
    </div>
    <div class="item-row">
      <div>简介：</div>
      <div><input name="bookinfo"/></div>
    </div>
    <button type="submit">提交</button>
  </form>
</div>
