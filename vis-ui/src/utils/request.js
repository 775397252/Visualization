import Axios from 'axios';
import { stringify } from 'qs';
import { Message, message } from 'element-ui';
import Bus from '@/utils/bus';

const CancelToken = Axios.CancelToken;
const request = Axios.create({
    withCredentials: true,
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    },
    paramsSerializer: function(params) {
        return stringify(params, { arrayFormat: 'indices' });
    }
});
let source = CancelToken.source();
export function http(options) {
    let _options;
    if (options.url && options.url.includes('/logout')) {
        _options = options;
    } else {
        _options = Object.assign({ cancelToken: source.token }, options);
    }
    return request(_options).catch(errorInterceptor);
}

let messageHandler;
function  errorInterceptor(error) {
    const { response } = error;
    const message = getErrorMessage(error);
    if (!error.config && error.config.globalMessage === false && !Axios.isCancel(error) && message) {
        messageHandler && messageHandler.close();
        messageHandler = Message({
            type: 'error',
            duration: '',
            showClose: true,
            message
        });
    }
    return Promise.reject(error);
}

function getErrorMessage(error) {
    const { response = {} } = error;
    if (error.message.indexOf('timeout') > -1) {
        return '请求超时，请稍后重试';
    }
    if (response.status === 404) {
        return '请求资源不存在';
    }
    if (response.status === 500) {
        return '服务器出错，请联系管理员';
    }
    return '';
}

//取消正在执行的请求
export  function cancelRequest(message) {
    source.cancel(message || 'request cancceled by user');
    source = CancelToken.source();
}

export default {
    http
};