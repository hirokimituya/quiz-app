const path = require('path')
const CompressionPlugin = require('compression-webpack-plugin')

module.exports = {
  plugins: [
    new CompressionPlugin({
      filename: '[path][base].gz[query]',
      algorithm: 'gzip',
      test: /\.js$|\.css$|\.html$|\.svg$/,
      threshold: 10240,
      minRatio: 0.8,
    }),
  ],
  resolve: {
    alias: {
      '@': path.resolve('resources/js'),
    },
  },
  cache: {
    type: 'filesystem',
    buildDependencies: {
      config: [__filename],
    },
  },
}
