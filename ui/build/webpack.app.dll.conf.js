var path = require('path')
var webpack = require('webpack')
var utils = require('./utils')
var config = require('../config')
var vueLoaderConfig = require('./vue-loader.conf')
var ExtractTextPlugin = require('extract-text-webpack-plugin'); // 提取css
var AssetsPlugin = require('assets-webpack-plugin')

function resolve(dir) {
  return path.join(__dirname, '..', dir)
}



module.exports = {
  entry: {
    app_libs: [
      'vue/dist/vue.esm.js',
      'vue-resource',
      'vue-router',
      '@/assets/js/http.js'
    ]
  },
  output: {
    path: path.resolve(__dirname, '../../public/static/mini/dll/'),
    filename: '[name].js',
    library: '[name]_library',
    publicPath: process.env.NODE_ENV === 'production' ?
      config.build.assetsPublicPath : config.dev.assetsPublicPath
  },
  resolve: {
    extensions: ['.js', '.vue', '.json'],
    alias: {
      'vue$': 'vue/dist/vue.esm.js',
      '@': resolve('src/app/'),
    }
  },
  module: {
    rules: [{
        test: /\.vue$/,
        loader: 'vue-loader',
        options: vueLoaderConfig
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        include: [resolve('src'), resolve('test')]
      },
      {
        test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
        loader: 'url-loader',
        options: {
          limit: 5000,
          name: utils.assetsPath('img/[name].[hash:4].[ext]')
        }
      },
      {
        test: /\.(mp4|webm|ogg|mp3|wav|flac|aac)(\?.*)?$/,
        loader: 'url-loader',
        options: {
          limit: 5000,
          name: utils.assetsPath('media/[name].[hash:4].[ext]')
        }
      },
      {
        test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
        loader: 'url-loader',
        options: {
          limit: 5000,
          name: utils.assetsPath('fonts/[name].[hash:4].[ext]')
        }
      }
    ]
  },
  plugins: [
    new ExtractTextPlugin('[name].[contenthash:4].css'),
    new webpack.DllPlugin({
      path: path.resolve(__dirname, '../../public/static/mini/dll/[name]-mainfest.json'),
      name: '[name]_library',
      context: __dirname // 执行的上下文环境，对之后DllReferencePlugin有用
    }),
    new webpack.optimize.UglifyJsPlugin({
      compress: {
        warnings: false
      },
      sourceMap: false
    }),
    new AssetsPlugin({
      filename: 'bundle-config.json',
      path: path.resolve(__dirname, '../../public/static/mini/dll')
    }),
  ]
}
