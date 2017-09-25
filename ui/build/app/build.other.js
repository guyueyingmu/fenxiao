require('../check-versions')()

process.env.NODE_ENV = 'production'

var ora = require('ora')
var rm = require('rimraf')
var path = require('path')
var chalk = require('chalk')
var webpack = require('webpack')
var config = require('../../config')
var webpackConfig = require('../webpack.app.other.conf')

var spinner = ora('正在打包 app 源文件，请不要关闭...')
spinner.start()

rm(path.join(config.build.assetsRoot, config.build.assetsSubDirectory), err => {
  if (err) throw err
  webpack(webpackConfig, function (err, stats) {
    spinner.stop()
    if (err) throw err
    process.stdout.write(stats.toString({
      colors: true,
      modules: false,
      children: false,
      chunks: false,
      chunkModules: false
    }) + '\n\n')

    if (stats.hasErrors()) {
      console.log(chalk.red(' 打包出错了.\n'))
      process.exit(1)
    }

    console.log(chalk.cyan('  打包完成，并发布到相应目录.\n'))

  })
})
