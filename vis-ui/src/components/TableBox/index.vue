<template>
    <div class="el-table-box">
        <div
           v-if="showToolbar"
            class="el-table-box__toolbar">
            <div class="el-table-box__toolbar-action">
                <slot :query="postData" :selection="selectionInner" :selection-data="selectionDataInner" name="action" />
            </div>
            <div
                v-if="!!$slots.search || !!$scopedSlots.search"
                class="el-table-box__toolbar-search">
                <slot :search="handleSearch" :fetch-data="fetchData" name="search" />
                <el-button v-if="showSearchButton" type="primary" icon="el-icon-search" @click="handleSearch" size="small">
                    <!--<svg-icon name="search-o" />-->
                </el-button>
            </div>
        </div>
        <slot
            :query="postData"
            :selection="selectionInner"
            :selection-data="selectionDataInner"
            :search="handleSearch"
            name="toolbar"/>
        <myTable
            v-loading="innerLoading"
            ref="myTable"
            v-bind="$attrs"
            :data="dataList"
            :row-key="rowKey"
            :row-class-name="rowClassName"
            :fix-height="fixHeight"
            :empty-icon="emptyIcon"
            class="el-table-box__table"
            @sort-change="handleSortChange"
            @selection-change="handleSelectionChange"
            v-on="$listeners"
        >
            <slot />
        </myTable>
        <pagination
            v-if="hasPagination"
            v-bind="$attrs"
            :total="total"
            :current-page.sync="postData.pageNum"
            :page-size.sync="postData.pageSize"
            @change="fetchListData"
        ></pagination>
    </div>
</template>

<script>
    import myTable from '../table';
    import pagination from '../Pagination';
    export default {
        name: 'TableBox',
        components: { pagination , myTable},
        inheritAttrs: false,
        props: {
            rowKey: [String, Function],
            fixHeight: {
                type: Number,
                default: -173
            },
            http: {
                type:  Function,
                required: true
            },
            url: {
                type: String,
                default: ''
            },
            method: {
                type: String,
                default: 'post'
            },
            selection: {
                type: Array,
                default() {
                    return [];
                }
            },
            dataColumnName: {
                type: String,
                default: 'listRow'
            },
            emptyIcon: {
                type: String,
                default: 'empty'
            },
            autoFetch: {
                type: Boolean,
                default: true
            },
            loading: {
                type: Boolean,
                default: false
            },
            hasPagination: {
                type: Boolean,
                default: true
            },
            showSearchButton: {
                type: Boolean,
                default: true
            },
            filter: {
                type: Boolean,
                default: false
            },
            filterData: {
                type: Array
            },
            query: {
                type: Object,
                default() {
                    return {};
                }
            },
            resultFormat: {
                type: Function,
                default(data) {
                    return data;
                }
            },
            dataFormat: {
                type: Function,
                default(data) {
                    return data;
                }
            },
            getTotal: {
                type: Function,
                default(data) {
                    let { total } = data;
                    if (Number.isInteger(total)) {
                        return total;
                    }
                    // console.log(data);
                    // let { pageBean, lineTotal, rowCount } = data;
                    // if (Number.isInteger(lineTotal) || Number.isInteger(rowCount)) {
                    //     return lineTotal || rowCount;
                    // }
                    // if (!pageBean) {
                    //     pageBean = data.data || {};
                    // }
                    // return pageBean.lineTotal || pageBean.rowCount || 0;
                }
            }
        },
        data() {
            return {
                innerLoading: false,
                fetchData: {},
                dataList: [],
                total: 0,
                selectionInner: [],
                selectionDataInner: [],
                postData: {
                    //pageNum pageSize
                    pageNum: 1,
                    pageSize: this.$attrs['page-size'] || 15,
                    headSort: '',
                    headSortType: ''
                }
            }
        },
        computed: {
            showToolbar() {
                const { $slots = {}, $scopedSlots = {} } = this;
                return (
                    ($slots.search || $scopedSlots.search || $slots.action || $scopedSlots.action) &&
                    (!$slots.toolbar && !$scopedSlots.toolbar)
                );
            }
        },
        watch: {
            innerLoading: {
                immediate: true,
                handler(val) {
                    this.$emit('update:loading', val);
                }
            },
            selection: {
                immediate: true,
                handler(val) {
                    this.$nextTick(() => {
                        const table = this.getTableRef();
                        if (table) {
                            this.dataList.forEach(data => {
                                const key = getRowIdentity(data, this.rowKey);
                                table.toggleRowSelection(data, val.includes(key));
                            })
                        }
                    })
                }
            },
            loading: {
                immediate: true,
                handler(val) {
                    this.innerLoading = val;
                }
            }
        },
        created() {
            this.autoFetch && this.fetchListData();
        },
        methods: {
            fetchListData() {
                const query = Object.assign({}, this.postData, this.query);
                let data;
                let params;
                if (['post', 'put', 'patch'].includes(String(this.method).toLowerCase())) {
                    data = this.dataFormat(query);
                } else {
                    params = this.dataFormat(query);
                }

                this.innerLoading = true;
                this.http({
                    url:  this.url,
                    method: this.method,
                    data,
                    params
                })
                    .then(({ data = {} }) => {
                        let _data;
                        if (data.code === 10000) {
                            _data = this.resultFormat(data.queryResult);
                        } else {
                            _data = this.resultFormat(data);
                        }
                        this.dataList = _data[this.dataColumnName] || [];
                        this.total = this.getTotal(_data) || 0;
                        this.fetchData = data;
                        this.$emit('fetch-success', data, query);
                        this.$emit('data-list', this.dataList);
                    })
                    .finally(() => {
                        this.innerLoading = false;
                    })
            },
            reloadList() {
                this.$nextTick(this.fetchListData);
            },
            resetList() {
                this.postData.pageNum = 1;
                this.postData.pageSize = this.$attrs['page-size'] || 15;
                this.postData.headSort = '';
                this.postData.headSortType = '';
                const table = this.getTableRef();
                if (table) {
                    table.clearSort();
                }
                this.reloadList();
            },
            handleSortChange({ prop, order }) {
                this.postData.headSort = prop;
                this.postData.headSortType = order && order.replace('ending', '');
                this.fetchListData();
            },
            handleSelectionChange(arr) {
                this.selectionDataInner = arr;
                this.selectionInner = arr.map(row => getRowIdentity(row, this.rowKey));
                this.$emit('update:selection', this.selectionInner);
                this.$emit('selectionChange', arr);
            },
            handleSearch() {
                this.postData.pageNum = 1;
                this.fetchListData();
            },
            getTableRef() {
                return this.$refs.myTable && this.$refs.myTable.$refs.table;
            },
            rowClassName({ row }) {
                return this.selectionDataInner.includes(row) ? 'selected' : '';
            }
        }
    };
    function getRowIdentity(row, rowKey) {
        if (typeof rowKey === 'string') {
            if (rowKey.indexOf('.') < 0) {
                return row[rowKey];
            }
            let key = rowKey.split('.');
            let current = row;
            for(let i=0;i<key.length;i++) {
                current = current[key[i]];
            }
            return current;
        } else if (typeof rowKey === 'function') {
            return rowKey.call(null, row);
        }
    }
</script>

<style>
.el-table-box {
    background: #fff;
    padding: 20px 20px 0;
}
.el-table-box__toolbar {
     margin-bottom: 15px;
     overflow: hidden;
}
.el-table-box__toolbar ::after {
    content: '';
    clear: both;
}
.el-table-box__toolbar .el-button .el-button--small {
     height: 32px;
}
.el-table-box__toolbar-action {
     display: inline-block;
 }
.el-table-box__toolbar-search {
     display: inline-block;
     float: right;
}
.el-table-box__toolbar-search .el-input {
    width: 160px;
    margin-right: 10px;
}
.el-table-box__toolbar-search .el-button {
    margin-right: 0;
}
</style>