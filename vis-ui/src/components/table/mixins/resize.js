import { debounce } from 'lodash';

export default {
    props: {
        autoResize: {
            type: Boolean,
            default: true
        },
        maxHeight: {
            type: [String, Number]
        },
        fixHeight: {
            type: Number,
            default: -30
        },
        container: {
            type: String,
            default: '.main-container'
        }
    },
    data() {
        return {
            maxHeightInnerTemp: 'auto'
        };
    },
    computed: {
        maxHeightInner() {
            return this.maxHeight || this.maxHeightInnerTemp;
        }
    },
    mounted() {
        this.__resizeHandler = debounce(() => {
            this.autoResize && this.resize();
        }, 100);
        this.__resizeHandler();
        window.addEventListener('resize', this.__resizeHandler);
    },
    updated() {
        this.__resizeHandler();
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.__resizeHandler);
    },
    methods: {
        resize() {
            if (this.$el) {
                this.maxHeightInnerTemp = window.innerHeight - this.$el.offsetTop + this.fixHeight;
            }
        }
    }
}