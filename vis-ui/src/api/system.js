import http from './public'

/*数据字典 */
export const sys_getDictionary= dType => {
    return http.requestQuickGet('/sys/dictionary/'+dType)
};
/*获取jwt令牌*/
export const getjwt= () => {
    return http.requestQuickGet('/openapi/auth/userjwt')
};
