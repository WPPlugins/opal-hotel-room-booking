const DEBUG = process.env.NODE_ENV !== 'production';
const path = require( 'path' );
const webpack = require( 'webpack' );
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );

module.exports = {
	entry: [
		path.resolve( __dirname, 'assets/es6/scripts.js' ),
		path.join( __dirname, 'assets/site/sass/opalhotel.scss' )
	],
	output: {
		filename : "js/scripts.min.js",
		path: path.resolve( __dirname, 'assets/site/' )
	},
	module: {
		loaders: [
			{
				test: /\.js$/,
				loader: 'babel-loader',
				exclude: /node_modules/,
				query: {
					presets: [ 'es2015', 'stage-0' ]
				}
			},
			{
				test: /\.(png|jpg|jpeg)$/,
				loader: 'url-loader'
			},
			{
				test: /\.css$/,
				loader: [ 'style-loader', 'css-loader' ]
			},
			{
				test: /\.scss$/,
				loader: ExtractTextPlugin.extract(['css-loader', 'sass-loader'])
			}
		]
	},
	devtool: DEBUG ? 'inline-source-map' : '',
	plugins: [
	    new webpack.DefinePlugin({
	      'process.env.NODE_ENV': JSON.stringify( DEBUG )
	    }),
	    // new webpack.optimize.DedupePlugin(),
	    new webpack.optimize.OccurrenceOrderPlugin(),
	    new webpack.optimize.UglifyJsPlugin({
	      	compress: { warnings: false },
	      	mangle: true,
	      	sourcemap: false,
	      	beautify: false,
	      	dead_code: true
	    }),
	    new ExtractTextPlugin({
	    	filename: 'css/opalhotel.css',
      		allChunks: true
	    })
  	]
}
