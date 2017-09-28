// see http://vuejs-templates.github.io/webpack for documentation.
var path = require('path')

module.exports = {
  build: {
    env: require('./prod.env'),
    index: path.resolve(__dirname, '../../application/mini/view/index/index.html'),
    assetsRoot: path.resolve(__dirname, '../../public/static/mini'),
    assetsSubDirectory: 'build', //引用子目录 
    assetsPublicPath: '/static/mini/', //引用根目录
    productionSourceMap:false ,
    // Gzip off by default as many popular static hosts such as
    // Surge or Netlify already gzip all static assets for you.
    // Before setting to `true`, make sure to:
    // npm install --save-dev compression-webpack-plugin
    productionGzip: false,
    productionGzipExtensions: ['js', 'css'],
    // Run the build command with an extra argument to
    // View the bundle analyzer report after build finishes:
    // `npm run build --report`
    // Set to `true` or `false` to always turn it on or off
    bundleAnalyzerReport: process.env.npm_config_report
  },
  dev: {
    env: require('./dev.env'),
    port: 8080,
    autoOpenBrowser: 0,
    assetsSubDirectory: 'static',
    assetsPublicPath: '/',
    proxyTable: {
        "/app/**":"http://192.168.8.87:91/",
        "/admin/**":"http://192.168.8.87:91/",
        "/uploads/**":"http://192.168.8.87:91/"
    },
    // CSS Sourcemaps off by default because relative paths are "buggy"
    // with this option, according to the CSS-Loader README
    // (https://github.com/webpack/css-loader#sourcemaps)
    // In our experience, they generally work as expected,
    // just be aware of this issue when enabling this option.
    cssSourceMap: false
  }
}
