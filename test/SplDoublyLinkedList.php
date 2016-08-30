<?php
//双向链表
$dlist = new SplDoublyLinkedList();

//插入到数据列表的最后
$dlist->push('hiramariam');
$dlist->push('maaz');
$dlist->push('zafar');
/* the list contains
hiramariam
maaz
zafar
 */

//插入到数据列表的最前
$dlist->unshift(1);
$dlist->unshift(2);
$dlist->unshift(3);
/* the list now contains
3
2
1
hiramariam
maaz
zafar
 */

//删除一个项目从列表的底部
$dlist->pop();
/* the list now contains
3
2
1
hiramariam
maaz
 */

//删除一个项目从列表的顶部
$dlist->shift();
/* the list now contains

2
1
hiramariam
maaz

 */

//详情见php手册SPL

var_dump($dlist);
