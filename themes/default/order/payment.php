<?php

/* @var string $liqPayHtml */
/* @var string $error */
/* @var $this yii\web\View */

if (empty($error)) {
    echo $liqPayHtml;
} else {
    echo $error;
}
?>
<style>
    form {
        display: none;
    }
</style>
<script>
    document.querySelector('form').submit();
</script>
