<div id="toolbar">
        <button id="button" class="btn btn-default">增加一行</button>
        <button id="getTableData" class="btn btn-default">获取数据</button>
    </div>

<table id="kucun_table" ></table>


<script>
$(function() {
    let $table = $('#kucun_table');
    let $button = $('#button');
    let $getTableData = $('#getTableData');

    $button.click(function() {
        $table.bootstrapTable('insertRow', {
            index: 0,
            row: {
              TB_isbn: '',
              TB_Name: '',
              TB_Author: '',
              TB_Press: '',
              TB_Edition: '',
              TB_PublishDate: '',
              TB_Inventory: '',
              TB_Price: ''
            }
        });
    });



$('#kucun_table').bootstrapTable({
  url: '../../application/json/test.json',
  method:'post',
  search: true,
  striped: true,   
  showRefresh: true,
  toolbar: '#toolbar', 
  clickEdit: true,
  showColumns: true,
  showPaginationSwitch: true, 
  // showToggle:true,      
  // clickToSelect: true, 

  pagination: true,
  pageNumber: 1,                   //初始化加载第一页，默认第一页
  pageSize: 10,                    //每页的记录行数（*）
  pageList: [10, 25, 50, 100],     //可供选择的每页的行数（*）
  paginationPreText: "Previous",
  paginationNextText: "Next",
  paginationFirstText: "First",
  paginationLastText: "Last",

  columns: [{
            checkbox: true
        },
     {
    field: 'TB_isbn',
    title: 'ISBN号'
  }, {
    field: 'TB_Name',
    title: '书名'
  }, {
    field: 'TB_Author',
    title: '作者'
  }, {
    field: 'TB_Press',
    title: '出版社'
  }, {
    field: 'TB_Edition',
    title: '版次'
  }, {
    field: 'TB_PublishDate',
    title: '出版日期'
  }, {
    field: 'TB_Inventory',
    title: '库存'
  }, {
    field: 'TB_Price',
    title: '价格'
  }]
  ,
// })
 /**
         * @param {点击列的 field 名称} field
         * @param {点击列的 value 值} value
         * @param {点击列的整行数据} row
         * @param {td 元素} $element
         */
        onClickCell: function(field, value, row, $element) {
            $element.attr('contenteditable', true);
            $element.blur(function() {
                let index = $element.parent().data('index');
                let tdValue = $element.html();

                saveData(index, field, tdValue);
            })
        }
    });

    $getTableData.click(function() {
        alert(JSON.stringify($table.bootstrapTable('getData')));
    });

    function saveData(index, field, value) {
        $table.bootstrapTable('updateCell', {
            index: index,       //行索引
            field: field,       //列名
            value: value        //cell值
        })
    }

});



</script>



<!-- 
  <thead>
    <tr>
      <th data-field="TB_isbn">ISBN号</th>
      <th data-field="TB_Name">书名</th>
      <th data-field="TB_Author">作者</th>
      <th data-field="TB_Press">出版社</th>
      <th data-field="TB_Edition">版次</th> 
      <th data-field="TB_PublishDate">出版日期</th>
      <th data-field="TB_Inventory">库存</th>
      <th data-field="TB_Price">价格</th>

    </tr>

  </thead>
  <tbody>  

  </tbody>

</table> -->