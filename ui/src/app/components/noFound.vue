<template>
    <div>
        <mt-cell title="标题文字"></mt-cell>
        <mt-cell title="标题文字" value="说明文字"></mt-cell>

    </div>
</template>
<script>
import { MessageBox } from 'mint-ui';
export default {
    created() {
        MessageBox.confirm('确定执行此操作?').then(action => {

        });
    }

}

</script>
