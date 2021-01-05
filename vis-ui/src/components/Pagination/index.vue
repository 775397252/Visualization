<template>
  <div class="el-pagination">
    <div class="el-pagination__info"><slot name="info" /></div>
    <el-pagination
      v-bind="[$attrs, $props]"
      :current-page="currentPageInner"
      :page-size="pageSizeInner"
      class="el-pagination__pager"
      @size-change="handleSizeChange"
      @current-change="handleCurrentChange"
      v-on="$listeners"
    >
      <span class="el-pagination__pager-slot">
        <slot name="pagination" />
      </span>
    </el-pagination>
  </div>
</template>

<script>

export default {
  name: 'Pagination',
  inheritAttrs: false,
  props: {
    background: {
      type: Boolean,
      default: true
    },
    pageSize: {
      type: Number,
      default: 15
    },
    currentPage: {
      type: Number,
      default: 1
    },
    layout: {
      type: String,
      default: 'slot, total, sizes, prev, pager, next, jumper'
    },
    pageSizes: {
      type: Array,
      default() {
        return [10, 15, 20, 30, 50, 100];
      }
    }
  },
  data() {
    return {
      currentPageInner: 1,
      pageSizeInner: 15
    };
  },
  watch: {
    currentPage: {
      immediate: true,
      handler(val) {
        this.currentPageInner = val;
      }
    },
    pageSize: {
      immediate: true,
      handler(val) {
        this.pageSizeInner = val;
      }
    }
  },
  methods: {
    handleSizeChange(pageSize) {
      this.$emit('update:pageSize', pageSize);
      this.$emit('change', { pageSize });
    },
    handleCurrentChange(current) {
      this.$emit('update:currentPage', current);
      this.$emit('change',{ pageNum: current });
    }
  }
}
</script>

<style>
.el-pagination {
  text-align: right;
  flex-flow: row;
  justify-content: space-between;
  padding: 10px;
}

.el-pagination__pager-slot {
  margin-right: 10px;
  margin-top: 10px;
}
.el-pagination__info {
  line-height: 28px;
}
  .el-pagination .el-input .el-input__inner {
    line-height: normal;
  }
</style>
