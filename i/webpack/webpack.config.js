const path = require('path')
const webpack = require('webpack')
const CssExtractor = require('mini-css-extract-plugin')
const CssMinimizer = require('css-minimizer-webpack-plugin') // Minify css
const TerserWebpackPlugin = require('terser-webpack-plugin') // Minify js
const { CleanWebpackPlugin } = require('clean-webpack-plugin') // Clean old compiled files
const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer') // Analyze of compiled files

const isProduction = process.env.NODE_ENV === 'production'

const optimization = () => {
    const config = {
        minimize: true,
        minimizer: [
            new CssMinimizer()
        ]
    }
    if (isProduction) {
        config.minimizer.push(
            new TerserWebpackPlugin()
        )
    }
    return config
}

const cssLoaders = extra => {
    const loaders = [
        'style-loader',
        CssExtractor.loader,
        'css-loader',
        {
            loader: 'postcss-loader',
            options: {
                postcssOptions: {
                    plugins: () => {
                        return [
                            require('autoprefixer')
                        ]
                    }
                }
            }
        }
    ]
    if (extra) {
        loaders.push(extra)
    }
    return loaders
}

const babelOptions = preset => {
    const opts = {
        presets: [
            [
                '@babel/preset-env',
                {
                    targets: {
                        node: '8'
                    }
                }
            ]
        ],
        plugins: [
            '@babel/plugin-proposal-class-properties'
        ]
    }
    if (preset) {
        opts.presets.push(preset)
    }
    return opts
}

const jsLoaders = preset => {
    return [{
        loader: 'babel-loader',
        options: babelOptions(preset)
    }]
}

const plugins = () => {
    const plugins = [
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery'
        }),
        new CleanWebpackPlugin(),
        new CssExtractor({
            filename: `[name].css`
        })
    ]
    if (isProduction) {
        plugins.push(new BundleAnalyzerPlugin())
    }
    return plugins
}

module.exports = {
    context: path.resolve(__dirname, '.'),
    mode: 'development',
    entry: {
        app: ['@babel/polyfill', './index.ts']
    },
    output: {
        path: path.resolve(__dirname, './dist'),
        filename: `[name].js`
    },
    resolve: {
        extensions: ['.ts', '.js', '.json', '.scss', '.less', '.css'],
        alias: {
            'jquery': path.resolve(__dirname, '/node_modules/jquery/dist/jquery.min.js'),

            // Нужно дублировать в tsconfig.json что бы phpstorm понимал
            '@': path.resolve(__dirname, '..'),
            '@js': path.resolve(__dirname, '../js'),
            '@ts': path.resolve(__dirname, '../ts'),
            '@css': path.resolve(__dirname, '../css'),
            '@less': path.resolve(__dirname, '../less'),
            '@scss': path.resolve(__dirname, '../scss'),
            '@image': path.resolve(__dirname, '../image'),
        }
    },
    optimization: optimization(),
    plugins: plugins(),
    module: {
        rules: [
            {
                test: /\.css$/,
                use: cssLoaders()
            },
            {
                test: /\.less$/,
                use: cssLoaders('less-loader')
            },
            {
                test: /\.s[ac]ss$/,
                use: cssLoaders('sass-loader')
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: jsLoaders()
            },
            {
                test: /\.ts$/,
                exclude: /node_modules/,
                use: jsLoaders('@babel/preset-typescript'),
            },
            {
                test: /\.(png|jpg|jpeg|svg|gif)$/,
                use: ['file-loader']
            },
            {
                test: /\.(ttf|woff|woff2|eot)$/,
                use: ['file-loader']
            },
            {
                test: /\.xml$/,
                use: ['xml-loader']
            },
            {
                test: /\.csv$/,
                use: ['csv-loader']
            }
        ]
    }
}