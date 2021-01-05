module.exports = {
    devServer: {
        // port:8080,
        open:true,
        proxy: {
            '/': {
                target: 'http://129.204.251.71:9500/',
                ws: true,
                changeOrigin: true,
                pathWrite:{
                    '^/':''
                }
            }
        }
    }
}