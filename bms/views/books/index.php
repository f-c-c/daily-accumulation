<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
<?php $this->registerCssFile('css/books.css');?>
<?php $this->registerJsFile('js/books.js');?>
<div class="container-con">
  <h1><?php  echo $title?></h1>
  <div>
  <button id="add"><a href="<?php echo $addBooksUrl ?>">新增图书</a></button>
  </div>
      <table class="table-list">
        <tr>
          <th>id</th>
          <th>图书id</th>
          <th>书名</th>
          <th>类型</th>
          <th>作者</th>
          <th>发版日期</th>
          <th>页数</th>
          <th>入库日期</th>
          <th>是否借阅</th>
          <th>简要信息</th>
          <th>操作</th>
        </tr>
      <?php foreach ($books as $books): ?>
        <tr>
          <td><?= $books->id ?></td>
          <td><?= $books->bookid ?></td>
          <td><?= $books->bookname ?></td>
          <td><?= $books->booktype ?></td>
          <td><?= $books->bookauthor ?></td>
          <td><?= $books->bookupdate ?></td>
          <td><?= $books->bookpages ?></td>
          <td><?= $books->bookindate ?></td>
          <td><?= $books->isborrowed ?></td>
          <td><?= $books->bookinfo ?></td>
          <td><a href="?r=books/delete&id=<?= $books->id ?>">删除</a></td>
        </tr>
        <?php endforeach; ?>
      </table>


</div>


<?= LinkPager::widget(['pagination' => $pagination]) ?>