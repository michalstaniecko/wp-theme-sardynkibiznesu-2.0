const path = require('path')
const glob = require('glob')
const webpackConfig = require('webpack')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const PurgecssPlugin = require('purgecss-webpack-plugin')

const PATHS = {
  src: path.join(__dirname, '')
}

module.exports = function (env = {}, argv) {
  const config = {
    mode: argv.mode,
    entry: {
      main: path.resolve(__dirname, './src/js/index.js')
    },
    output: {
      path: path.resolve(__dirname, './assets'),
      filename: 'index.js',
      clean: true
    },

    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: [/node_modules/],
          use: {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-env'],
              plugins: ['@babel/plugin-transform-runtime']
            },
          }
        },

        {
          test: /\.(css|scss)$/,
          use: [
            {
              loader: MiniCssExtractPlugin.loader
            },
            'css-loader',
            'sass-loader'
          ]
        },


        {
          test: /\.(woff|woff2|eot|ttf|oft|svg)(\?.*$|$)$/,
          type: 'asset/resource',
        },
      ]
    },

    plugins: [
      new MiniCssExtractPlugin()
    ]
  }

  /*if (config.mode !== 'development') {
    config.plugins.push(
      new PurgecssPlugin({
        paths: glob.sync(`${PATHS.src}/!**!/!*`, {
          nodir: true,
          ignore: [`${PATHS.src}/node_modules/!**!/!*`]
        })
      })
    )
  }*/

  return config
}
