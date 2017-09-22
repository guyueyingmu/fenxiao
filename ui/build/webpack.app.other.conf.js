var path = require('path')
var utils = require('./utils')
var webpack = require('webpack')
var config = require('../config/app')
var merge = require('webpack-merge')
var baseWebpackConfig = require('./webpack.app.conf')
var CopyWebpackPlugin = require('copy-webpack-plugin')
var HtmlWebpackPlugin = require('html-webpack-plugin')
var ExtractTextPlugin = require('extract-text-webpack-plugin')
var OptimizeCSSPlugin = require('optimize-css-assets-webpack-plugin')

var env = config.build.env

var webpackConfig = merge(baseWebpackConfig, {
  module: {
    rules: utils.styleLoaders({
      sourceMap: config.build.productionSourceMap,
      extract: true
    })
  },
  devtool: config.build.productionSourceMap ? '#source-map' : false,
  output: {
    path: config.build.assetsRoot,
    filename: utils.assetsPath('js/[name].[chunkhash:4].js'),
    chunkFilename: utils.assetsPath('js/[name].[chunkhash:4].js')
  },
  plugins: [
    // http://vuejs.github.io/vue-loader/en/workflow/production.html
    new webpack.DefinePlugin({
      'process.env': env
    }),
    new webpack.optimize.UglifyJsPlugin({
      compress: {
        warnings: false
      },
      sourceMap: true
    }),
    // extract css into its own file
    new ExtractTextPlugin({
      filename: utils.assetsPath('css/[name].[contenthash:4].css')
    }),
    // new assets-webpack-plugin({
    //     filename: '/static/app/dll/libs.js'
    //   }),
    // Compress extracted CSS. We are using this plugin so that possible
    // duplicated CSS from different components can be deduped.
 
    new webpack.DllReferencePlugin({
        context: __dirname,
        manifest: require('../../public/static/app/dll/libs-mainfest.json')
      }),
    // generate dist index.html with correct asset hash for caching.
    // you can customize output by editing /index.html
    // see https://github.com/ampedandwired/html-webpack-plugin
    new HtmlWebpackPlugin({
      filename: config.build.index,
      template: path.resolve(__dirname, '../src/app/index.html'),
      title:'首页',
      inject: true,
      lib:'/static/app/dll/libs.js',
      minify: {
        removeComments: true,
        collapseWhitespace: true,
        removeAttributeQuotes: true
        // more options:
        // https://github.com/kangax/html-minifier#options-quick-reference
      },
      // necessary to consistently work with multiple chunks via CommonsChunkPlugin
      chunksSortMode: 'dependency'
    }),


    // keep module.id stable when vender modules does not change
    new webpack.HashedModuleIdsPlugin(),
   
    // copy custom static assets
    new CopyWebpackPlugin([
      {
        from: path.resolve(__dirname, '../static/app'),
        to: config.build.assetsSubDirectory,
        ignore: ['.*']
      }
    ])
  ]
})


module.exports = webpackConfig
