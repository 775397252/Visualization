<template>
    <el-table
        ref="tableList"
        v-bind="$attrs"
        :max-height="maxHeightInner"
        :class="{'el-table__no-border': !$attrs.border}"
        class="el-table"
        v-on="$listeners">
        <slot />
        <div
            v-if="!!emptyIcon"
            slot="empty">
            <!--<svg-icon-->
                <!--:name="emptyIcon"-->
                <!--class="el-table__empty-icon"-->
            <!--/>-->
            <div class="el-table__empty-message">{{ emptyText }}</div>
        </div>
        <span
            v-else
            slot="empty"
        >{{ emptyText }}</span>
    </el-table>
</template>

<script>
    import resize from './mixins/resize';
    export default {
        name: 'MyTable',
        mixins: [resize],
        inheritAttrs: false,
        props: {
            emptyText: {
                type: String,
                default: '暂时没有内容'
            },
            emptyIcon: {
                type: String
            }
        },
        mounted() {
            this.$el.addEventListener('click', this.clickCheckbox);
        },
        beforeDestroy() {
            this.$el.removeEventListener('click', this.clickCheckbox);
        },
        methods: {
            clickCheckbox(evt) {
                const tagName = evt.target.tagName.toLowerCase();
                if (tagName === 'td' || tagName === 'th') {
                    const classList = evt.target.className.split(' ');
                    if (classList.includes('el-table-column--selection')) {
                        evt.target.querySelector('input').click();
                    }
                }
            }
        }
    };
</script>

<style scoped>
.el-table {
    &__empty-icon {
        width: 250px;
        height: 180px;
     }
     &__empty-message {
        position: relative;
        top: -20px;
        left: -15px;
      }
      &__no-border {
        >>> th {
            border-top: 1px solid #ebeef5;
        }
       }
        >>> .el-table__empty-text {
            line-height: 20px;
        }
        >>> .el-table__empty-block {
            border-bottom: 1px solid #ebeef5;
        }
        >>> .el-table-column--selection {
            text-align: center;
            cursor: pointer;
            .cell {
                display: inline;
                padding: 0;
            }
        }
}
.el-table th, .el-table tr {
    background: #fff;
 }
</style>