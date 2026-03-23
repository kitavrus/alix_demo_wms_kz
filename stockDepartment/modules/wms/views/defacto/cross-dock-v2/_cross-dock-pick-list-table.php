
<table >
            <tr>
                <th>Название магазина</th>
                <th>Предполагаемое кол-во</th>
                <th>Действительное кол-во</th>
            </tr>;

<?php if($crossOrders){
    foreach($crossOrders as $co){
        if($store = $co->pointTo){ ?>

                <tr>
                    <td>test</td>
                    <td>test</td>
                    <td>test</td>
                 </tr>;
      <?php  }
    }
} ?>

</table>