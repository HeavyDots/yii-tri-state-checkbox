This is a three state checkbox input widget for Yii.

How to use:

1) Copy to extensions or widgets, as you wish. Then import it either manually or by adding to main.php config file imports.

    Yii::import('ext.yii-tri-state-checkbox.cbTriStateCheckbox.*');

2) Call it in your views by providing model and attribute:

    <?php $this->widget('CbTriStateCheckbox', array('model'=>$model,'attribute'=>'field_name')); ?>

Credits:  

Inspired in this blog post: 
https://giweb.wordpress.com/2009/12/01/three-state-tri-state-checkboxes-in-html/