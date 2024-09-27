const path = require('path')
const CssExtractor = require('mini-css-extract-plugin')
const CssMinimizer = require('css-minimizer-webpack-plugin') // Minify css
const TerserWebpackPlugin = require('terser-webpack-plugin') // Minify js
const { CleanWebpackPlugin } = require('clean-webpack-plugin') // Clean old compiled files
const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer') // Analyze of compiled files

const isProduction = process.env.NODE_ENV === 'production'
const isDevelopment = !isProduction

const optimization = () => {
    let config = {}
    if (isProduction) {
        config = Object.assign(
            config,
            {
                minimize: true,
                minimizer: [
                    new CssMinimizer()
                ]
            }
        )

        config.minimizer.push(
            new TerserWebpackPlugin()
        )
    }
    return config
}

const cssLoaders = extra => {
    const loaders = [
        'style-loader',
        {
            loader: CssExtractor.loader,
            options: {
                esModule: false,
            }
        },
        {
            loader: 'css-loader',
            options: {
                url: true
            }
        },
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
                '@babel/preset-env'
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
    entry: {
        app: ['./index.ts']
    },
    output: {
        path: path.resolve(__dirname, './dist'),
        filename: `[name].js`
    },
    plugins: plugins(),
    optimization: optimization(),
    resolve: {
        extensions: ['.ts', '.js', '.json', '.scss', '.less', '.css'],
        alias: {
            // Need duplicate into tsconfig.json for phpstorm's highlight
            '@': path.resolve(__dirname, '..'),
            '@js': path.resolve(__dirname, '../js'),
            '@ts': path.resolve(__dirname, '../ts'),
            '@css': path.resolve(__dirname, '../css'),
            '@data': path.resolve(__dirname, '../data'),
            '@less': path.resolve(__dirname, '../less'),
            '@scss': path.resolve(__dirname, '../scss'),
            '@image': path.resolve(__dirname, '../image'),
            '@webpack': path.resolve(__dirname, '../webpack'),
        },
        roots: [__dirname, path.resolve(__dirname, '../..')],
    },
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
                use: jsLoaders('@babel/preset-typescript')
            },
            {
                test: require.resolve('jquery'),
                loader: 'expose-loader',
                options: {
                    exposes: ['$', 'jQuery']
                }
            }
        ]
    }
}